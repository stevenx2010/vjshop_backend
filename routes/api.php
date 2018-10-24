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

Route::middleware('auth:api')->get('front/product/categories/console', 'ProductCategoriesController@index_console');
Route::middleware('auth:api')->get('front/product/categories', 'ProductCategoriesController@index');
Route::middleware('auth:api')->post('front/product/categories/swap', 'ProductCategoriesController@swap');
Route::middleware('auth:api')->get('front/product/categories/{productId}', 'ProductCategoriesController@show');
Route::middleware('auth:api')->post('front/product/categories/update', 'ProductCategoriesController@update');
Route::middleware('auth:api')->delete('front/product/categories/delete/{categoryId}', 'ProductCategoriesController@destroy');


Route::middleware('auth:api')->get('front/product/subcategories/categoryid/{categoryId}', 'ProductSubCategoriesController@showByCategoryId');
Route::middleware('auth:api')->post('front/product/subcategories/swap', 'ProductSubCategoriesController@swap');
Route::middleware('auth:api')->post('front/product/subcategories/update', 'ProductSubCategoriesController@update');
Route::middleware('auth:api')->get('front/product/subcategories/subcategoryid/{subcategoryId}', 'ProductSubCategoriesController@showBySubCategoryId');
Route::middleware('auth:api')->delete('front/product/subcategories/delete/{subCategoryId}', 'ProductSubCategoriesController@destroy');
Route::middleware('auth:api')->get('front/product/categories/subCatId/{subCatId}', 'ProductSubCategoriesController@showCatId');

Route::middleware('auth:api')->post('front/product/product/update', 'ProductsController@update');
Route::middleware('auth:api')->post('front/product/product/updateImage', 'ProductsController@updateImage');
Route::middleware('auth:api')->get('front/product/products/bySubCatId/{productSubCategoryId}', 'ProductsController@showProductsBySubCategoryId');
Route::middleware('auth:api')->delete('front/product/products/delete/{productId}', 'ProductsController@destroy');
Route::middleware('auth:api')->post('front/product/products/swap', 'ProductsController@swap');
Route::middleware('auth:api')->get('front/product/query/keyword/{keyword}/{subCatId}', 'ProductsController@showByKeywordSubCatId');
Route::middleware('auth:api')->get('front/product/query/keyword/{keyword}/catid/{catId}', 'ProductsController@showByKeywordCatId');
Route::middleware('auth:api')->get('front/product/query/categoryId/{catId}', 'ProductsController@showByCatId');
Route::middleware('auth:api')->get('front/product/query/all', 'ProductsController@showAll');
Route::middleware('auth:api')->get('front/product/query/keyword/{keyword}', 'ProductsController@showByKeyword');
Route::middleware('auth:api')->get('front/product/query/id/{productId}', 'ProductsController@showByProductId');
Route::middleware('auth:api')->get('front/product/images/id/{productId}', 'ProductsController@showImagesByProductId');

/*
|--------------------------------------------------------------------------
| Distributor Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/distributor/info/update', 'DistributorController@updateInfo');
Route::post('front/distributor/address/update', 'DistributorController@updateAddress');
Route::post('front/distributor/contact/update', 'DistributorController@updateContact');
Route::get('front/distributor/info/query/{keyword}', 'DistributorController@showInfo');
Route::get('front/distributor/inventory/query/{distributorId}', 'DistributorController@showInventory');
Route::post('front/distributor/inventory/increase', 'DistributorController@putInventory');
Route::middleware('auth:api')->get('front/distributor/distributors', 'DistributorController@showAll');
Route::delete('front/distributor/delete/{id}', 'DistributorController@destroy');
Route::get('front/distributor/query/{id}', 'DistributorController@showById');
Route::get('front/distributor/address/query/{addressId}', 'DistributorController@showAddressById');
Route::delete('front/distributor/address/delete/{addressId}', 'DistributorController@destroyAddressById');
Route::get('front/distributor/contact/query/{contactId}', 'DistributorController@showContactById');
Route::delete('front/distributor/contact/delete/{contactId}', 'DistributorController@destroyContactById');
Route::post('front/distributor/inventory/query', 'DistributorController@showInventoryByConditions');
Route::post('front/distributor/product/query', 'DistributorController@showProductByConditions');

/*
|--------------------------------------------------------------------------
| Coupon Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::get('front/coupon/type/all', 'CouponTypesController@showAll');
Route::post('front/coupon/type/update/sort_order', 'CouponTypesController@updateSortOrder');
Route::post('front/coupon/type/update/coupontype', 'CouponTypesController@updateOrCreateCouponType');
Route::get('front/coupon/type/query/id/{couponTypeId}', 'CouponTypesController@showCouponTypeById');
Route::delete('front/coupon/type/delete/id/{couponTypeId}', 'CouponTypesController@deleteCouponTypeById');

Route::post('front/coupon/query', 'CouponsController@showCoupons');
Route::post('front/coupon/update', 'CouponsController@updateOrCreateCoupon');
Route::get('front/coupon/query/id/{couponId}', 'CouponsController@showCouponById');

/*
|--------------------------------------------------------------------------
| Order Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/order/query/conditions', 'OrderController@showByConditions');
Route::get('front/order/query/detail/id/{id}', 'OrderController@showDetailByOrderId');
Route::post('front/order/query/conditions/distributor', 'OrderController@showByConditionsForDistributor');


/*
|--------------------------------------------------------------------------
| Invoice Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/invoice/updateOrCreate', 'InvoiceController@updateOrCreate');

/*
|--------------------------------------------------------------------------
| Refund Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/refund/update', 'RefundController@updateOrCreate');
Route::get('front/refund/get/{orderId}', 'RefundController@showByOrderId');

/*
|--------------------------------------------------------------------------
| Home Page / Newcomer Page Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/page/homepage/update', 'PageController@update');

Route::post('front/page/newcomerpage/update', 'PageController@updateNewComer');

/*
|--------------------------------------------------------------------------
| Customer Service Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::get('front/CustomerService/message/get/{mobile}', 'MessageController@show');
Route::get('front/CustomerService/message/checknew', 'MessageController@showNew');
Route::get('front/CustomerService/get', 'MessageController@showByCondition');
Route::get('front/CustomerService/get/all', 'MessageController@showAll');
Route::get('front/CustomerService/get/newcount', 'MessageController@showAllNewCount');
Route::get('front/CustomerService/qna/get', 'MessageController@showQnA');
Route::post('front/CustomerService/qna/update', 'MessageController@updateQnA');
Route::get('front/CustomerService/qna/delete/{id}', 'MessageController@destroyQnA');
Route::get('front/CustomerService/qna/get/id/{id}', 'MessageController@getQnAById');

/*
|--------------------------------------------------------------------------
| User Management Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::post('front/users/new', 'UserController@updateOrCreate');
Route::get('front/users/getAll', 'UserController@showAll');
Route::get('front/users/delete/{id}', 'UserController@destroyById');
Route::post('front/users/login', 'UserController@login');
Route::post('front/users/update/password', 'UserController@updatePassword');
Route::get('front/users/email/unique/{email}', 'UserController@checkEmailUnique');
Route::post('front/users/roles/update','UserController@updateRoles');

/*
|--------------------------------------------------------------------------
| Setting Routes for Front End 
|--------------------------------------------------------------------------
*/
Route::get('front/setting/shipping/get/all', 'SettingController@showShippingAll');
Route::post('front/setting/shipping/update', 'SettingController@updateOrCreateShipping');
Route::get('front/setting/shipping/delete/{id}', 'SettingController@destroyById');





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

Route::get('product/categories', 'ProductCategoriesController@index')->middleware('appauth');

// Get all products
Route::get('product/all', 'ProductsController@index')->middleware('appauth');

// Get product sub-categories
Route::get('product/productSubCategories/{categoryId}', 'ProductsController@showProductSubCategories')->middleware('appauth');

// Get products of a category
Route::get('product/products/{productCategoryId}', 'ProductsController@showProducts')->middleware('appauth');

// Get product detail info by product id
Route::get('product/detail/{productId}', 'ProductsController@show')->middleware('appauth');

// Get images of product detail in position 1 & 2
Route::get('product/detail/images/{productId}/{position}','ProductsController@showImages')->middleware('appauth');

// Get product search result by keyword
Route::get('product/search/{keyword}', 'ProductsController@showProductSearched')->middleware('appauth');

// Get products by ids
Route::post('product/products/ids', 'ProductsController@showProductsByIds')->middleware('appauth');



/*
|--------------------------------------------------------------------------
| Customer Login Routes
|--------------------------------------------------------------------------
*/
Route::post('customer/login', 'CustomersController@customerLogin')->middleware('appauth');
Route::get('customer/check_user/{mobile}', 'CustomersController@showExist')->middleware('appauth');

/*
|--------------------------------------------------------------------------
| Shipping Address Routes
|--------------------------------------------------------------------------
*/
Route::get('address/all/{mobile}', 'ShippingAddressController@show')->middleware('appauth');
Route::get('address/default/{mobile}', 'ShippingAddressController@showDefault')->middleware('appauth');
Route::get('address/userid/{mobile}', 'ShippingAddressController@showUserId')->middleware('appauth');
Route::delete('address/id/{addressId}', 'ShippingAddressController@destroy')->middleware('appauth');
Route::get('address/query/id/{addressId}', 'ShippingAddressController@showShippingAddressById')->middleware('appauth');


/*
|--------------------------------------------------------------------------
| Distributor Related Routes
|--------------------------------------------------------------------------
*/
Route::get('distributor/address/{city}', 'DistributorController@showAddress')->middleware('appauth');
Route::get('distributor/contact/{distributorId}', 'DistributorController@showContact')->middleware('appauth');
Route::get('distributor/distributor/{distributorId}', 'DistributorController@show')->middleware('appauth');
Route::get('distributor/login/{mobile}', 'DistributorController@login')->middleware('appauth');
Route::get('distributor/orders/{mobile}', 'DistributorController@showOrders')->middleware('appauth');
Route::get('distributor/inventories/{mobile}', 'DistributorController@showInventories')->middleware('appauth');
Route::get('distributor/info/mobile/{mobile}', 'DistributorController@showInfoByMobile')->middleware('appauth');
Route::get('distributor/info/city/{city}', 'DistributorController@showInfoByLocation')->middleware('appauth');
Route::get('distributor/inventory/productId/{distributorId}/{productId}', 'DistributorController@showInventoryByProductId')->middleware('appauth');
Route::get('distributor/login/check/{mobile}', 'DistributorController@checkLogin')->middleware('appauth');
Route::post('distributor/summary/orders', 'DistributorController@summary')->middleware('appauth');
Route::get('distributor/allinfo/{distributorId}', 'DistributorController@showAllInfoById')->middleware('appauth');

/*
|--------------------------------------------------------------------------
| Coupon Related Routes
|--------------------------------------------------------------------------
*/
Route::get('coupon/types', 'CouponTypesController@show')->middleware('appauth');
Route::get('coupon/bytype/{typeId}', 'CouponsController@show')->middleware('appauth');
Route::get('coupon/newcomer', 'CouponsController@showNewComer')->middleware('appauth');
Route::post('coupon/update/expire_status', 'CouponsController@updateExpireStatus')->middleware('appauth');
Route::get('coupon/coupons/mobile/{mobile}', 'CouponsController@showByMobile')->middleware('appauth');
Route::post('coupon/coupon_customer', 'CouponsController@updateCouponCustomerRelation')->middleware('appauth');
Route::get('coupon/coupons_filtered/{mobile}', 'CouponsController@showCouponsFiltered')->middleware('appauth');

/*
|--------------------------------------------------------------------------
| Order Related Routes
|--------------------------------------------------------------------------
*/
Route::post('order/submit', 'OrderController@update')->middleware('appauth');
Route::get('order/update/delivery/{orderId}/{status}/{datetime}', 'OrderController@updateDeliveryStatus')->middleware('appauth');
Route::get('order/myorders/{mobile}/{orderStatus}', 'OrderController@showOrdersByStatus')->middleware('appauth');
Route::get('order/myorders/{mobile}/{orderStatus}/{page}', 'OrderController@showOrdersByStatusPaged')->middleware('appauth');
Route::delete('order/delete/id/{orderId}', 'OrderController@destroy')->middleware('appauth');
Route::delete('order/delete/orderSerial/{orderSerial}', 'OrderController@destroyByOrderSerial')->middleware('appauth');
Route::get('order/update/paymentMethod/{orderId}/{paymentMethod}', 'OrderController@updatePaymentMethod')->middleware('appauth');


/*
|--------------------------------------------------------------------------
| Comment Related Routes
|--------------------------------------------------------------------------
*/
Route::post('comment/update', 'CommentController@update')->middleware('appauth');
Route::get('comment/show/{mobile}', 'CommentController@showByMobile')->middleware('appauth');
Route::get('comment/not_commented/{orderId}', 'CommentController@showProductsNotCommented')->middleware('appauth');


/*
|--------------------------------------------------------------------------
| Payment Related Routes
|--------------------------------------------------------------------------
*/
Route::post('payment/alipay', 'PaymentController@alipay')->middleware('appauth');
Route::post('payment/alipay/callback', 'PaymentController@alipayCallback')->middleware('appauth');
Route::post('payment/wechat', 'PaymentController@wechat')->middleware('appauth');

/*
|--------------------------------------------------------------------------
| Utitlities Related Routes
|--------------------------------------------------------------------------
*/
Route::get('app/version', 'AppVersionController@show')->middleware('appauth');

/*
|--------------------------------------------------------------------------
| Customer Service Message Chat Related Routes
|--------------------------------------------------------------------------
*/
Route::post('CustomerService/message/update/', 'MessageController@update')->middleware('appauth');
Route::get('CustomerService/message/retrieve/{mobile}', 'MessageController@show')->middleware('appauth');
Route::get('CustomerService/qna/get', 'MessageController@showQnA')->middleware('appauth');


/*
|--------------------------------------------------------------------------
| Setting (Shipping Fee) Related Routes
|--------------------------------------------------------------------------
*/
Route::get('setting/shipping/formula/{weight}', 'SettingController@showFormula')->middleware('appauth');