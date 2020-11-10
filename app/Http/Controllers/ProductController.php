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
     * Create product in DB
     * and in the Billy
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
                'currency_id' => $request->currency_id
            )
        );

        try {

            $product_fields['prices'] = array($price_fields);

            // create product in billy
            $billy_product = $billy->create_object(
                array('product' => $product_fields),
                'products'
            );

            // set the external_id which is id from billy
            $product->external_id = $billy_product->products[0]->id;
            $product->save();

            // insert the price
            $price = new Price(array(
                'product_id' => $product->id,
                'external_id' => $billy_product->productPrices[0]->id,
                'unit_price' => $request->unit_price,
                'currency_id' => $request->currency_id
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
     * Update the product in the system
     * and in the Billy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // find the product
        $product = Product::findOrFail($id);

        $billy = resolve('Billy');

        // prepare product fields
        $product_fields = $billy->billy_fields(
            array(
                'name' => $request->name,
                'product_no' => $request->product_no,
                'description' => $request->description
            )
        );

        // prepare price fields
        $price_fields = $billy->billy_fields(
            array(
                'unit_price' => $request->unit_price,
                'currencyId' => $request->currency_id
            )
        );

        try {

            // add price fields
            // in the products
            $product_fields['prices'] = array($price_fields);

            // update product
            // in billy
            $billy_id = $billy->update_object(
                array('product' => $product_fields),
                $product->external_id,
                'products'
            );

            // fill the object
            // with data from the form
            $product->fill($request->all());

            // save the product
            $product->save();

            // update the price
            DB::table('prices')
                ->where('product_id', $product->id)
                ->update(['unit_price' => $request->unit_price, 'currency_id' => $request->currency_id]);
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

        // then delete in system db
        $product->delete();

        return redirect('/products');
    }
}
