<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::resource('contacts', App\Http\Controllers\ContactController::class);
Route::resource('products', App\Http\Controllers\ProductController::class);

Route::get('/sync_contacts_from', [Billy::class, 'sync_contacts_from']);
Route::get('/sync_products_from', [Billy::class, 'sync_products_from']);

Route::get('/sync_contacts_to', [Billy::class, 'sync_contacts_to']);
Route::get('/sync_products_to', [Billy::class, 'sync_products_to']);