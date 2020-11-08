<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            "X-Access-Token: " . config('app.billy_api'),
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
	 * Create contact in Billy
	 * and return the id of the newly
	 * created contact
	 *
	 * @param array $body
	 * @return string $id
	 */
	function create_contact(array $body) {

	    $res = $this->request("POST", "/contacts", array('contact' => $body));

	    return $res->contacts[0]->id;
	}


	/**
	 * Update contact in Billy
	 *
	 * @param array $body
	 * @param string $id
	 * @return string $id
	 */
	function update_contact(array $body, $id) {

	    $res = $this->request("PUT", "/contacts/{$id}", array('contact' => $body));

	    return $res->contacts[0]->id;
	}

	/**
	 * Create product in Billy
	 * and return the id of the newly
	 * created product
	 *
	 * @param array $body
	 * @return string $id
	 */
	function create_product(array $body) {

	    $res = $client->request("POST", "/products", array('product' => $body));

	    return $res->products[0]->id;
	}

}