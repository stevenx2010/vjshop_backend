<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('HomePageImages', 'HomePageImagesController');

Route::get('HomePageImages/images/{position}', 'HomePageImagesController@show');

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('product/categories', 'ProductCategoriesController@index');
Route::post('product/categories', 'ProductCategoriesController@store');
Route::post('product/categories/swap', 'ProductCategoriesController@swap');
Route::get('product/categories/{productId}', 'ProductCategoriesController@show');
Route::post('product/categories/update', 'ProductCategoriesController@update');
Route::delete('product/categories/delete/{productId}', 'ProductCategoriesController@destroy');

Route::get('product/subcategories/categoryid/{categoryId}', 'productSubCategoriesController@showByCategoryId');
Route::post('product/subcategories/swap', 'ProductSubCategoriesController@swap');
// Get all products
Route::get('product/all', 'ProductsController@index');

// Get product sub-categories
Route::get('product/productSubCategories/{categoryId}', 'ProductsController@showProductSubCategories');

// Get products of a category
Route::get('product/products/{productCategoryId}', 'ProductsController@showProducts');

// Get product detail info by product id
Route::get('product/detail/{productId}', 'ProductsController@show');

// Get images of product detail in position 1 & 2
Route::get('product/detail/images/{productId}/{position}','ProductsController@showImages');

// Get product search result by keyword
Route::get('product/search/{keyword}', 'ProductsController@showProductSearched');

// Get products by ids
Route::post('product/products/ids', 'ProductsController@showProductsByIds');



/*
|--------------------------------------------------------------------------
| Customer Login Routes
|--------------------------------------------------------------------------
*/
Route::post('customer/login', 'CustomersController@customerLogin');

/*
|--------------------------------------------------------------------------
| Shipping Address Routes
|--------------------------------------------------------------------------
*/
Route::get('address/all/{mobile}', 'ShippingAddressController@show');
Route::get('address/userid/{mobile}', 'ShippingAddressController@showUserId');