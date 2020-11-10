<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

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
	 * in out system
	 *
	 * @param object $contact
	 * @param string $action
	 * @return array $fields
	 */
	public function billy_to_sytem_fields($contact, $action){

		// make id to be external_id
		$contact->external_id = $contact->id;
		unset($contact->id);

		// if it's update remove createdTime
		if ('update' == $action) {
			unset($contact->createdTime);
		} else {
			// changing also createdTime to created_at
			$contact->created_at = $contact->createdTime;
			unset($contact->createdTime);
		}

		$fields = array();

		// the real conversion
		// contactId to become contact_id
		foreach ($contact as $key => $value) {
			$fields[strtolower((preg_replace('/\B([A-Z])/', '_$1', $key)))] = $value;
		}

		return $fields;	
	}

	/**
	 * Sync all contacts from Billy
	 */
	public function sync_contacts() {

		// do the request to get all contacts
		$all_contacts = $this->request('GET', '/contacts');

		// loop all returned contacts
		foreach ($all_contacts->contacts as $contact) {

			// if there is aleady such contact
			// in our db, update if
			if ($find = Contact::where('external_id', '=', $contact->id)->first()) {
				$fields = $this->billy_to_sytem_fields($contact, 'update');
				$find->fill($fields);
				$find->save();
			} else {
				// if not - create it
				$fields = $this->billy_to_sytem_fields($contact, 'insert');
				$contact = new Contact($fields);
				$contact->save();
			}
		}

		return redirect('/contacts');

	}

}