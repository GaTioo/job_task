<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Price;
use DB;

class Billy extends Controller
{

	/**
	 * Doing the request to billy API
	 * @param string $method
	 * @param string $url
	 * @param array $body
	 */
    public function request($method, $url, $body = null) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.billysbilling.com/v2" . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "X-Access-Token: " . config('services.billy')['api'],
            "Content-Type: application/json"
        ));

        // if there is body
        // set it, because
        // delete request don't need body
        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        // do the request
        $res = curl_exec($ch);
        $response = json_decode($res);
        curl_close($ch);

        // if there is error throw and exception and print
        // user-friendly message
        if ($response->meta->statusCode != 200 && $response->meta->success != 'true') {
            throw new \Exception($response->errorMessage);
        }

        return $response;
    }

	/**
	 * Create object in Billy
	 * and return the id of the newly
	 * created object
	 *
	 * @param array $body
	 * @param string $type
	 * @return string $id
	 */
	function create_object(array $body, $type) {

	    $res = $this->request("POST", "/{$type}", $body);

	    return $res;
	}


	/**
	 * Update object in Billy
	 *
	 * @param array $body
	 * @param string $id
	 * @param string $type
	 * @return string $id
	 */
	function update_object(array $body, $id, $type) {

	    $res = $this->request("PUT", "/{$type}/{$id}", $body);

	    return $res->{$type}[0]->id;
	}

	/**
	 * Delete object in Billy
	 *
	 * @param string $id
	 * @param string $type
	 * @return string $id
	 */
	function delete_object($id, $type) {

	    $res = $this->request("DELETE", "/{$type}/{$id}");

	    // return the deleted id
	    // or just true, because can be already deleted
	    return isset($res->meta->deletedRecords->{$type}[0]) ? $res->meta->deletedRecords->{$type}[0] : true;
	}

	/**
	 * Extract and prepare needed fields
	 * to send them with the request
	 * to Billy
	 *
	 * @param array $fields
	 * @return array $prepared_fields
	 */
	public function billy_fields($fields) {

		// removing laravel token
		// and method
		unset($fields['_token']);
		unset($fields['_method']);

		$prepared_fields = array();
		foreach ($fields as $key => $value) {
			// if there is _ in the key
			if (strpos($key, '_') !== false) {
				// replace it with whitespace
				// uppercase first letter of every word
				// remove all whitespaces
				// and lowercase first latter
			    $key = lcfirst(str_replace(' ', '', (ucwords(str_replace('_', ' ', $key)))));
			}

			$prepared_fields[$key] = $value;
		}

		return $prepared_fields;

	}

	/**
	 * Prepare billy fields
	 * to be ready for update/insert
	 * in our system
	 *
	 * @param object $object
	 * @param string $action
	 * @return array $fields
	 */
	public function billy_to_system_fields($object, $action){

		// make id to be external_id
		$object->external_id = $object->id;
		unset($object->id);

		// if it's update remove createdTime
		if ('update' == $action) {
			unset($object->createdTime);
		} else {
			// changing also createdTime to created_at
			if (isset($object->createdTime)) {
				$object->created_at = $object->createdTime;
				unset($object->createdTime);
			}
		}

		$fields = array();

		// the real conversion
		// objectId to become object_id
		foreach ($object as $key => $value) {
			$fields[strtolower((preg_replace('/\B([A-Z])/', '_$1', $key)))] = $value;
		}

		return $fields;	
	}

	/**
	 * Sync all contacts from Billy
	 */
	public function sync_contacts_from() {

		// do the request to get all contacts
		$all_contacts = $this->request('GET', '/contacts');

		// loop all returned contacts
		foreach ($all_contacts->contacts as $contact) {

			// if there is aleady such contact
			// in our db, update if
			if ($find = Contact::where('external_id', '=', $contact->id)->first()) {
				$fields = $this->billy_to_system_fields($contact, 'update');
				$find->fill($fields);
				$find->save();
			} else {
				// if not - create it
				$fields = $this->billy_to_system_fields($contact, 'insert');
				$contact = new Contact($fields);
				$contact->save();
			}
		}

		return redirect('/contacts');

	}

	/**
	 * Sync all products and prices from Billy
	 */
	public function sync_products_from() {

		// do the request to get all products
		$all_products = $this->request('GET', '/products');
		// loop all returned products
		foreach ($all_products->products as $product) {

			// get all prices for the product
			$prices = $this->request('GET', '/productPrices?productId=' . $product->id);

			// if there is aleady such product
			// in our db, update it
			if ($find = Product::where('external_id', '=', $product->id)->first()) {
				$fields = $this->billy_to_system_fields($product, 'update');
				$find->fill($fields);
				$find->save();

				// update the price
				DB::table('prices')
				    ->where('product_id', $find->id)
				    ->delete();

				foreach ($prices->productPrices as $p_price) {
					$fields = $this->billy_to_system_fields($p_price, 'insert');
					$fields['product_id'] = $find->id;
					$new_price = new Price($fields);
					$new_price->save();
				}

			} else {
				// if not - create it
				$fields = $this->billy_to_system_fields($product, 'insert');
				$new_product = new Product($fields);
				$new_product->save();

				foreach ($prices->productPrices as $p_price) {
					$fields = $this->billy_to_system_fields($p_price, 'insert');
					$fields['product_id'] = $new_product->id;
					$new_price = new Price($fields);
					$new_price->save();
				}
			}
		}

		return redirect('/products');

	}


	/**
	 * Sync all contacts to Billy
	 * Update or create contacts 
	 * in Billy with data from the system
	 *
	 * Could be added try catch and to 
	 * return status which objects
	 * were updated and which not
	 */
	public function sync_contacts_to() {

		$billy = resolve('Billy');

		// get all contacts from the db
		$all_contacts = Contact::all();

		// loop all returned contacts
		foreach ($all_contacts as $contact) {

			// get object fields and clear not yet used ones
			// I didn't implement all fields in the UI
			$to_array = $this->clear_contact_fields($contact->toArray());

		    // prepare billy fields
			$fields = $billy->billy_fields($to_array);

			// its possible contact to not have external id
			// which means its not created in Billy
			// so we need to create it in Billy API
			if (empty($contact->external_id)) {

			        // create contact in billy
					$billy_contact = $billy->create_object(
			            array('contact' => $fields),
			            'contacts'
			        );

			        // set external_id with id from the newly created contact
					$contact->external_id = $billy_contact->contacts[0]->id;

					$contact->save();
			} else {
					// just update the record
					$billy_contact = $billy->update_object(
			            array('contact' => $fields),
			            $contact->external_id,
			            'contacts'
			        );
			}
		}

		return redirect('/contacts');

	}

	/**
	 * Sync all products to Billy
	 * Update or create products 
	 * in Billy with data from the system
	 *
	 * Could be added try catch and to 
	 * return status which objects
	 * were updated and which not
	 */
	public function sync_products_to() {

		$billy = resolve('Billy');

		// get all products from the db
		$all_products = Product::all();

		// loop all returned contacts
		foreach ($all_products as $product) {

			// prepare price fields
			$price_fields = array();
			foreach ($product->prices as $pprice) {
				$price_fields[] = $billy->billy_fields(
		            array(
		                'unit_price' => $pprice->unit_price,
		                'currency_id' => $pprice->currency_id
		            )
		        );
			}

			// get object fields and clear not yet used ones
			// I didn't implement all fields in the UI
			$to_array = $this->clear_product_fields($product->toArray());

		    // prepare billy fields
			$fields = $billy->billy_fields($to_array);

			$fields['prices'] = $price_fields;

			// its possible product to not have external id
			// which means its not created in Billy
			// so we need to create it in Billy API
			if (empty($product->external_id)) {

			        // create product in billy
					$billy_product = $billy->create_object(
			            array('product' => $fields),
			            'products'
			        );

			        // set external_id with id from the newly created product
					$product->external_id = $billy_product->products[0]->id;

					$product->save();

					// update external_id of prices in the system
					foreach ($billy_product->productPrices as $result_prices) {
						Price::where('currency_id', '=', $result_prices->currencyId)
							->where('unit_price', '=', $result_prices->unitPrice)
							->where('product_id', '=', $product->id)
							->update(array('external_id' => $result_prices->id));
					}

			} else {
					// just update the record
					$billy_product = $billy->update_object(
			            array('product' => $fields),
			            $product->external_id,
			            'products'
			        );
			}
		}

		return redirect('/products');

	}

	/**
	 * Clear unused fields
	 * to prevent validation errors from Billy
	 * this can be changed in the future
	 *
	 * @param array $data
	 * @return array $data
	 */
	private function clear_contact_fields(array $data) {

		unset($data['id']);
		unset($data['external_id']);
		unset($data['updated_at']);
		unset($data['created_at']);
		unset($data['organization_id']);
		unset($data['locale_id']);
		unset($data['default_expense_account_id']);
		unset($data['is_sales_tax_exempt']);
		unset($data['payment_terms_mode']);
		unset($data['email_attachment_delivery_mode']);

		return $data;
	}

	/**
	 * Clear unused fields
	 * to prevent validation errors from Billy
	 * this can be changed in the future
	 *
	 * @param array $data
	 * @return array $data
	 */
	private function clear_product_fields(array $data) {

		unset($data['id']);
		unset($data['external_id']);
		unset($data['updated_at']);
		unset($data['created_at']);
		unset($data['organization_id']);
		unset($data['account_id']);
		unset($data['suppliers_product_no']);
		unset($data['sales_tax_ruleset_id']);
		unset($data['is_archived']);
		unset($data['is_in_inventory']);
		unset($data['image_id']);
		unset($data['image_url']);
		unset($data['inventory_account_id']);

		return $data;
	}

}