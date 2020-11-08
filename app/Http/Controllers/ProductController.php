<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Price;
use Illuminate\Http\Request;
use DB;

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

        $product_fields = $billy->billy_fields(
            array(
                'name' => $request->name,
                'product_no' => $request->product_no,
                'description' => $request->description
            )
        );

        $price_fields = $billy->billy_fields(
            array(
                'unit_price' => $request->unit_price,
                'currencyId' => $request->currency
            )
        );

        try {

            $product_fields['prices'] = array($price_fields);

            $billy_product = $billy->create_object(array('product' => $product_fields), 'products');

            $product->external_id = $billy_product->products[0]->id;
            $product->save();

            $price = new Price(array(
                'product_id' => $product->id,
                'external_id' => $billy_product->productPrices[0]->id,
                'unit_price' => $request->unit_price,
                'currency' => $request->currency
            ));
            $price->save();

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
        $price = DB::table('prices')->where('product_id', '=', $product->id)->first();

        return view('products.edit', compact('product', 'price'));
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

        $product_fields = $billy->billy_fields(
            array(
                'name' => $request->name,
                'product_no' => $request->product_no,
                'description' => $request->description
            )
        );

        $price_fields = $billy->billy_fields(
            array(
                'unit_price' => $request->unit_price,
                'currencyId' => $request->currency
            )
        );

        try {

            $product_fields['prices'] = array($price_fields);

            $billy_id = $billy->update_object(array('product' => $product_fields), $product->external_id, 'products');

            $product->fill($request->all());

            $product->save();

            DB::table('prices')->where('product_id', $product->id)->update(['unit_price' => $request->unit_price, 'currency' => $request->currency]);
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
