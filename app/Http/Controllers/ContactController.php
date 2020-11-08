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

    	$billy = resolve('Billy');

    	$fields = $billy->billy_fields($request->all());

    	try {
    		$billy_contact = $billy->create_object(array('contact' => $fields), 'contacts');

    		$contact->external_id = $billy_contact->contacts[0]->id;

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

    	$billy = resolve('Billy');

    	$fields = $billy->billy_fields($request->all());

    	try {
    		$billy_id = $billy->update_object(array('contact' => $fields), $contact->external_id, 'contacts');

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
			$billy = resolve('Billy');
			$billy->delete_object($contact->external_id, 'contacts');
		} catch (Exception $e) {
			return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
		}

		// delete in system db
		$contact->delete();

		return redirect('/contacts');
    }
}