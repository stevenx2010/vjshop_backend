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

Route::get('product/categories', 'ProductCategoriesController@index');

Route::get('product/all', 'ProductsController@index');

Route::get('product/detail/{productId}', 'ProductsController@show');
Route::get('product/detail/images/{productId}/{position}','ProductsController@showImages');