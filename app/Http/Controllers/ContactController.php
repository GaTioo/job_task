<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$contacts = Contact::paginate(10);

    	return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contacts.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$contact = new Contact($request->all());

    	$billy = new Billy();

    	$fields = $this->billy_contact_fields($request->all());

    	try {
    		$billy_id = $billy->create_contact($fields);

    		$contact->external_id = $billy_id;

    		$contact->save();
    	} catch (\Exception $e) {
    		return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
    	}
    	

    	return redirect('/contacts');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    	$contact =  Contact::findOrFail($id);

        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	$contact = Contact::findOrFail($id);

    	$billy = new Billy();

    	$fields = $this->billy_contact_fields($request->all());

    	try {
    		$billy_id = $billy->update_contact($fields, $contact->external_id);

    		$contact->fill($request->all());

    		$contact->save();
    	} catch (\Exception $e) {
    		return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
    	}

    	return redirect('/contacts/' . $id . '/edit');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$contact = Contact::findOrFail($id);

		// first try to delete
		// from the Billy
		try {
			$billy = new Billy();
			$billy_id = $billy->delete_contact($contact->external_id);
		} catch (Exception $e) {
			return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
		}

		// delete in system db
		$contact->delete();

		return redirect('/contacts');
    }

    /**
     * Extract and prepare needed fields
     * to send them with the request
     * to Billy
     *
     * @param array $fields
     * @return array $prepared_fields
     */
    public function billy_contact_fields($fields) {

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
}
