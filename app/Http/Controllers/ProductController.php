<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product($request->all());

        $billy = resolve('Billy');

        $fields = $billy->billy_fields($request->all());

        try {
            $billy_id = $billy->create_object(array('product' => $fields), 'products');

            $product->external_id = $billy_id;

            $product->save();
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
        }
        
        return redirect('/products');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
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
        $product = Product::findOrFail($id);

        $billy = resolve('Billy');

        $fields = $billy->billy_fields($request->all());

        try {
            $billy_id = $billy->update_object(array('contact' => $fields), $product->external_id, 'products');

            $product->fill($request->all());

            $product->save();
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
        }

        return redirect('/products/' . $id . '/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // first try to delete
        // from the Billy
        try {
            $billy = resolve('Billy');
            $billy->delete_object($product->external_id, 'products');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['exception' => $e->getMessage()]);
        }

        // delete in system db
        $product->delete();

        return redirect('/products');
    }
}
