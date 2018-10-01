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
| Product Routes for Front End 
|--------------------------------------------------------------------------
*/

Route::get('product/categories', 'ProductCategoriesController@index');
Route::get('product/categories/console', 'ProductCategoriesController@index_console');
Route::post('product/categories', 'ProductCategoriesController@store');
Route::post('product/categories/swap', 'ProductCategoriesController@swap');
Route::get('product/categories/{productId}', 'ProductCategoriesController@show');
Route::post('product/categories/update', 'ProductCategoriesController@update');
Route::delete('product/categories/delete/{categoryId}', 'ProductCategoriesController@destroy');


Route::get('product/subcategories/categoryid/{categoryId}', 'productSubCategoriesController@showByCategoryId');
Route::post('product/subcategories/swap', 'ProductSubCategoriesController@swap');
Route::post('product/subcategories/update', 'ProductSubCategoriesController@update');
Route::get('product/subcategories/subcategoryid/{subcategoryId}', 'productSubCategoriesController@showBySubCategoryId');
Route::delete('product/subcategories/delete/{subCategoryId}', 'ProductSubCategoriesController@destroy');
Route::get('product/categories/subCatId/{subCatId}', 'ProductSubCategoriesController@showCatId');

Route::post('product/product/update', 'ProductsController@update');
Route::post('product/product/updateImage', 'ProductsController@updateImage');
Route::get('product/products/bySubCatId/{productSubCategoryId}', 'ProductsController@showProductsBySubCategoryId');
Route::delete('product/products/delete/{productId}', 'ProductsController@destroy');
Route::post('product/products/swap', 'ProductsController@swap');
Route::get('product/query/keyword/{keyword}/{subCatId}', 'ProductsController@showByKeywordSubCatId');
Route::get('product/query/keyword/{keyword}/catid/{catId}', 'ProductsController@showByKeywordCatId');
Route::get('product/query/categoryId/{catId}', 'ProductsController@showByCatId');
Route::get('product/query/all', 'ProductsController@showAll');
Route::get('product/query/keyword/{keyword}', 'ProductsController@showByKeyword');
Route::get('product/query/id/{productId}', 'ProductsController@showByProductId');
Route::get('product/images/id/{productId}', 'ProductsController@showImagesByProductId');

/*
|--------------------------------------------------------------------------
| Distributor Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('distributor/info/update', 'DistributorController@updateInfo');
Route::post('distributor/address/update', 'DistributorController@updateAddress');
Route::post('distributor/contact/update', 'DistributorController@updateContact');
Route::get('distributor/info/query/{keyword}', 'DistributorController@showInfo');
Route::get('distributor/inventory/query/{distributorId}', 'DistributorController@showInventory');
Route::post('distributor/inventory/increase', 'DistributorController@putInventory');
Route::get('distributor/distributors', 'DistributorController@showAll');
Route::delete('distributor/delete/{id}', 'DistributorController@destroy');
Route::get('distributor/query/{id}', 'DistributorController@showById');
Route::get('distributor/address/query/{addressId}', 'DistributorController@showAddressById');
Route::delete('distributor/address/delete/{addressId}', 'DistributorController@destroyAddressById');
Route::get('distributor/contact/query/{contactId}', 'DistributorController@showContactById');
Route::delete('distributor/contact/delete/{contactId}', 'DistributorController@destroyContactById');
Route::post('distributor/inventory/query', 'DistributorController@showInventoryByConditions');
Route::post('distributor/product/query', 'DistributorController@showProductByConditions');

/*
|--------------------------------------------------------------------------
| Coupon Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::get('coupon/type/all', 'CouponTypesController@showAll');
Route::post('coupon/type/update/sort_order', 'CouponTypesController@updateSortOrder');
Route::post('coupon/type/update/coupontype', 'CouponTypesController@updateOrCreateCouponType');
Route::get('coupon/type/query/id/{couponTypeId}', 'CouponTypesController@showCouponTypeById');
Route::delete('coupon/type/delete/id/{couponTypeId}', 'CouponTypesController@deleteCouponTypeById');

Route::post('coupon/query', 'CouponsController@showCoupons');
Route::post('coupon/update', 'CouponsController@updateOrCreateCoupon');
Route::get('coupon/query/id/{couponId}', 'CouponsController@showCouponById');

/*
|--------------------------------------------------------------------------
| Order Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('order/query/conditions', 'OrderController@showByConditions');




/*
|--------------------------------------------------------------------------
| Routes for APP
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Product Routes for APP
|--------------------------------------------------------------------------
*/

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
Route::get('customer/check_user/{mobile}', 'CustomersController@showExist');

/*
|--------------------------------------------------------------------------
| Shipping Address Routes
|--------------------------------------------------------------------------
*/
Route::get('address/all/{mobile}', 'ShippingAddressController@show');
Route::get('address/default/{mobile}', 'ShippingAddressController@showDefault');
Route::get('address/userid/{mobile}', 'ShippingAddressController@showUserId');
Route::delete('address/id/{addressId}', 'ShippingAddressController@destroy');


/*
|--------------------------------------------------------------------------
| Distributor Related Routes
|--------------------------------------------------------------------------
*/
Route::get('distributor/address/{city}', 'DistributorController@showAddress');
Route::get('distributor/contact/{distributorId}', 'DistributorController@showContact');
Route::get('distributor/distributor/{distributorId}', 'DistributorController@show');
Route::get('distributor/login/{mobile}', 'DistributorController@login');
Route::get('distributor/orders/{mobile}', 'DistributorController@showOrders');
Route::get('distributor/inventories/{mobile}', 'DistributorController@showInventories');
Route::get('distributor/info/mobile/{mobile}', 'DistributorController@showInfoByMobile');
Route::get('distributor/info/city/{city}', 'DistributorController@showInfoByLocation');
Route::get('distributor/inventory/productId/{distributorId}/{productId}', 'DistributorController@showInventoryByProductId');

/*
|--------------------------------------------------------------------------
| Coupon Related Routes
|--------------------------------------------------------------------------
*/
Route::get('coupon/types', 'CouponTypesController@show');
Route::get('coupon/bytype/{typeId}', 'CouponsController@show');
Route::get('coupon/newcomer', 'CouponsController@showNewComer');
Route::post('coupon/update/expire_status', 'CouponsController@updateExpireStatus');
Route::get('coupon/coupons/mobile/{mobile}', 'CouponsController@showByMobile');
Route::post('coupon/coupon_customer', 'CouponsController@updateCouponCustomerRelation');
Route::get('coupon/coupons_filtered/{mobile}', 'CouponsController@showCouponsFiltered');

/*
|--------------------------------------------------------------------------
| Order Related Routes
|--------------------------------------------------------------------------
*/
Route::post('order/submit', 'OrderController@update');
Route::get('order/update/delivery/{orderId}/{status}/{datetime}', 'OrderController@updateDeliveryStatus');
Route::get('order/myorders/{mobile}/{orderStatus}', 'OrderController@showOrdersByStatus');
Route::delete('order/delete/id/{orderId}', 'OrderController@destroy');
Route::delete('order/delete/orderSerial/{orderSerial}', 'OrderController@destroyByOrderSerial');


/*
|--------------------------------------------------------------------------
| Comment Related Routes
|--------------------------------------------------------------------------
*/
Route::post('comment/update', 'CommentController@update');
Route::get('comment/show/{mobile}', 'CommentController@showByMobile');
Route::get('comment/not_commented/{orderId}', 'CommentController@showProductsNotCommented');


/*
|--------------------------------------------------------------------------
| Payment Related Routes
|--------------------------------------------------------------------------
*/
Route::get('payment/alipay', 'PaymentController@alipay');
Route::get('payment/alipay/callback', 'PaymentController@alipayCallback');

/*
|--------------------------------------------------------------------------
| Utitlities Related Routes
|--------------------------------------------------------------------------
*/
Route::get('app/version', 'AppVersionController@show');