<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Distributor;
use App\DistributorAddress;
use App\DistributorContact;
use App\Order;
use App\ShippingAddress;
use App\Product;
use App\ProductSubCategory;

use App\Libraries\Utilities\OrderStatus;

use Illuminate\Support\Facades\Log;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function putInventory(Request $request)
    {
        Log::debug($request);

        $distributor = Distributor::find($request['distributor_id']);
        $product = Product::find($request['product_id']);
       // $product_id = ($product->get())['id'];

        if($distributor->products->contains($product)) {
            foreach($distributor->products as $p) {
                if($p['id'] == $product['id']) {
                    $current_inventory = $p->pivot->inventory;

                    if($current_inventory + $request['inventory'] <= 0) {
                        $distributor->products()->detach($request['product_id']);
                    } else {
                        $distributor->products()->detach($request['product_id']);
                        $distributor->products()->attach([
                            $request['product_id'] => ['inventory' => $current_inventory + $request['inventory']]
                        ]);
                    }

                    break;
                }
            }
        } else {
            if($request['inventory'] > 0) {
                $distributor->products()->attach([
                   $request['product_id'] => ['inventory' => $request['inventory']]
                ]);
            }
        }
    } 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($distributorId)
    {
        return Distributor::select('id', 'name', 'description')->where('id', $distributorId)->get();
    }

    public function showAddress($city) 
    {
        return DistributorAddress::select('distributor_id', 'city', 'street', 'default_address')->where('city', 'like', '%'. $city . '%')->where('default_address', true)->get();
    }

    public function showContact($distributorId)
    {
        return DistributorContact::select('distributor_id', 'name', 'mobile', 'phone_area_code', 'telephone', 'default_contact')->where('distributor_id', $distributorId)->where('default_contact', true)->get();
    }

    public function showOrders($mobile)
    {
        $distributorContacts = DistributorContact::where('mobile', $mobile)->get();
        if($distributorContacts == null) return Response('mobile not found', 404);

        $distributor = Distributor::find($distributorContacts[0]['id']);

        $orders = $distributor->orders()->orderBy('created_at', 'DESC')->get();
        Log::debug($orders);

        $final_orders = array();

        foreach($orders as $order) {
            // Get products info of this order
            $order_array = json_decode($order, true);
            $order_obj = Order::find($order_array['id']);
            $products = $order_obj->products()->get();
            $shopping_items = [];

            foreach($products as $product) {

                $pivot = json_decode($product['pivot'], true);
                $shopping_item = [];
                $shopping_item = [
                    'productId' => $product['id'],
                    'quantity' => $pivot['quantity'],
                    'price' => $pivot['price'],
                    'weight' => $product['weight'],
                    'weight_unit' => $product['weight_unit'],
                    'thumbnail_url' => $product['thumbnail_url'],
                    'selected' => true
                ];

                array_push($shopping_items, $shopping_item);
            }

            // Get shipping address
            $shipping_address_obj = ShippingAddress::find($order_array['shipping_address_id']);

            if($shipping_address_obj == null) {
                $shipping_address = [];
            }
            else {
                $shipping_address = $shipping_address_obj->select('id', 'username', 'mobile','tel', 'city', 'street', 'customer_id', 'default_address')->get();
                $shipping_address_array = json_decode($shipping_address[0], true);
            }   

            Log::debug(json_decode($shipping_address[0], true));

            /*
            $address = [
                'id' => $shipping_address_array['id'],
                'username' => $shipping_address_array['username'],
                'mobile' => $shipping_address_array['mobile'],
                'tel' => $shipping_address_array['tel'],
                'city' => $shipping_address_array['city'],
                'street' => $shipping_address_array['street'],
                'customer_id' => $shipping_address_array['customer_id'],
                'default_address' => $shipping_address_array['default_address']
            ];
*/
            // Merge products & address to this order
            if($shipping_address) {
                $merged = array_merge(array('shipping_address' => $shipping_address_array), array('products' => $shopping_items), $order_array);
            }
            else {
                $merged = array_merge(array('shipping_address' => $shipping_address_array), array('products' => $shopping_items), $order_array);
            }

            array_push($final_orders, $merged);
        }

        Log::debug($final_orders);

        return json_encode($final_orders);
    }

    public function showInventories($mobile) 
    {
        $distributorContacts = DistributorContact::where('mobile', $mobile)->get();
        if($distributorContacts == null) return Response('mobile not found', 404);

        $id = (json_decode($distributorContacts[0], true))['id'];

        $distributor = Distributor::find($id);

        return $products = $distributor->products()->get();         
    }

    public function showInfo($keyword)
    {
        if($keyword === '*')
            return Distributor::where('name', 'like', '%')->get();
        else
            return Distributor::where('name', 'like', '%' . $keyword . '%')->get();
    }

    public function showInventory($distributorId)
    {
        return Distributor::find($distributorId)->products()->get();
    }

    public function showInfoByMobile($mobile)
    {
        $contacts = DistributorContact::where('mobile', $mobile)->get();

        if($contacts == null) return Response('mobile not found', 404);

        $id = (json_decode($contacts[0], true))['id'];

        $distributor = Distributor::find($id);

        $addresses = $distributor->addresses()->get();

        $distributors = Distributor::where('id', $id)->get();
        $resp = json_decode($distributors[0], true);


        $resp['addresses'] = $addresses;
        $resp['contacts'] = $contacts;

        Log::debug($resp);
        Log::debug($addresses);
        Log::debug($contacts);

        return response(json_encode($resp));
    }

    public function showInfoByLocation($city) {
        $addresses = DistributorAddress::where('city', 'like', '%' . $city . '%')->where('default_address', 1)->get();
        $addresses_array = json_decode($addresses, true);
        if(sizeof($addresses_array) > 0) {
            $distributor_id = $addresses_array[0]['distributor_id'];

            $distributor = Distributor::find($distributor_id)->get();
            $contacts = DistributorContact::where('distributor_id', $distributor_id)->get();

            $resp = (json_decode($distributor, true))[0];
            $resp['addresses'] = $addresses;
            $resp['contacts'] = $contacts;

            return json_encode($resp);
        } else {
            return Response('distributor not found!', 404);
        }
    }

    public function showInventoryByProductId($distributorId, $productId)
    {    
        Log::debug($distributorId);
        Log::debug($productId);

        $inventory = 0;

        $distributor = Distributor::find($distributorId);

        foreach ($distributor->products as $product) {
            if($product['id'] == $productId) {
                $inventory = $product->pivot->inventory;

                return $inventory;
            }
        }

        return $inventory;
    }

    public function showAll() 
    {
        $distributors = Distributor::select('id', 'name', 'description')->get();
        Log::debug($distributors);
        $distributors_array = json_decode($distributors, true);

        $final_resp = [];
        foreach ($distributors_array as $distributor) {
            $resp = [];
            $resp['id'] = $distributor['id'];
            $resp['name'] = $distributor['name'];
            $resp['description'] = $distributor['description'];
            $id = $distributor['id'];
            Log::debug($id);
            $addresses = DistributorAddress::select('id', 'distributor_id', 'city', 'street', 'default_address')->where('distributor_id', $id)->get();
            $resp['addresses'] = $addresses;
            $contacts = DistributorContact::select('id', 'distributor_id', 'name', 'mobile', 'phone_area_code','telephone', 'default_contact')->where('distributor_id', $id)->get();
            $resp['contacts'] = $contacts;

            array_push($final_resp, $resp);
        }
        return $final_resp;
    }

    public function showById($id) 
    {
        $distributor = Distributor::select('id', 'name', 'description')->where('id', $id)->get();
        if(count($distributor) < 1) {
            return Response('record not found!', 404);
        }
        Log::debug($distributor);
        $resp = [];
        $resp['id'] = $distributor[0]->id;
        $resp['name'] = $distributor[0]->name;
        $resp['description'] = $distributor[0]->description;

        $addresses = DistributorAddress::select('id', 'distributor_id', 'city', 'street', 'default_address')->where('distributor_id', $id)->get();
        $resp['addresses'] = $addresses;
        $contacts = DistributorContact::select('id', 'distributor_id', 'name', 'mobile', 'phone_area_code','telephone', 'default_contact')->where('distributor_id', $id)->get();
        $resp['contacts'] = $contacts;

        return json_encode($resp);
    }

    public function showAddressById($addressId)
    {
        $address = DistributorAddress::select('id', 'city', 'street', 'distributor_id', 'default_address')->where('id', $addressId)->get();

        return $address;
    }

    public function showContactById($contactId)
    {
        $contact = DistributorContact::select('id', 'name', 'mobile', 'phone_area_code', 'telephone', 'default_contact')->where('id', $contactId)->get();

        return $contact;
    }

    public function showInventoryByConditions(Request $request)
    {
        Log::debug($request);
        $distributorId = $request['distributorId'];
        $categoryId = $request['categoryId'];           // a
        $subCategoryId = $request['subCategoryId'];     // b
        $keyword = $request['keyword'];                 // c

        if($categoryId == 0 || $categoryId == 1){   // a == 0; 1 for all product category for the APP category page
            if($keyword == null || ($keyword && strlen($keyword) ==0)){     // c ==  null
                return Distributor::find($distributorId)->products()->get();
            } else {    // c != null
                return Distributor::find($distributorId)->products()->where('products.name', 'like', '%' . $keyword . '%')->get();
            }
        } else {    // a!= 0
            if($subCategoryId == 0) {   // b == 0
                if($keyword == null || ($keyword && strlen($keyword) ==0)){     // c == null
                    $subCategories = ProductSubCategory::where('product_category_id', $categoryId)->get();
                    $subCategories_array = json_decode($subCategories, true);

                    $resp = [];
                    foreach ($subCategories as $subCat) {
                        $products = Distributor::find($distributorId)->products()->where('products.product_sub_category_id', $subCat['id'])->get();
                        Log::debug($products);
                        foreach($products as $p) {
                            array_push($resp, $p);
                        }
                    }

                    return $resp;
                } else {    // c != null
                     $subCategories = ProductSubCategory::where('product_category_id', $categoryId)->get();
                    $subCategories_array = json_decode($subCategories, true);

                    $resp = [];
                    foreach ($subCategories_array as $subCat) {
                        $products = Distributor::find($distributorId)->products()->where('products.product_sub_category_id', $subCat['id'])->where('products.name', 'like', '%' . $keyword . '%')->get();

                        foreach($products as $p) {
                            array_push($resp, $p);
                        }
                    }

                    return $resp;                   
                }
            } else {    // b != 0
                if($keyword == null || ($keyword && strlen($keyword) ==0)) {    // c == null
                    return Distributor::find($distributorId)->products()->where('products.product_sub_category_id', $subCategoryId)->get();
                } else {
                    return Distributor::find($distributorId)->products()->where('products.product_sub_category_id', $subCategoryId)->where('products.name', 'like', '%' . $keyword . '%')->get();                    
                }
            }
        }

    }

    public function showProductByConditions(Request $request)
    {
        Log::debug($request);
        $categoryId = $request['categoryId'];           // a
        $subCategoryId = $request['subCategoryId'];     // b
        $keyword = $request['keyword'];                 // c

        if($categoryId == 0 || $categoryId == 1){   // a == 0; 1 for all product category for the APP category page
            if($keyword == null || ($keyword && strlen($keyword) ==0)){     // c ==  null
                return Product::all();
            } else {    // c != null
                return Product::where('products.name', 'like', '%' . $keyword . '%')->get();
            }
        } else {    // a!= 0
            if($subCategoryId == 0) {   // b == 0
                if($keyword == null || ($keyword && strlen($keyword) ==0)){     // c == null
                    $subCategories = ProductSubCategory::where('product_category_id', $categoryId)->get();
                    $subCategories_array = json_decode($subCategories, true);

                    $resp = [];
                    foreach ($subCategories_array as $subCat) {
                        $products = Product::where('products.product_sub_category_id', $subCat['id'])->get();

                        foreach($products as $p) {
                            array_push($resp, $p);
                        }
                    }

                    return $resp;
                } else {    // c != null
                     $subCategories = ProductSubCategory::where('product_category_id', $categoryId)->get();
                    $subCategories_array = json_decode($subCategories, true);

                    $resp = [];
                    foreach ($subCategories as $subCat) {
                        $products = Product::where('products.product_sub_category_id', $subCat['id'])->where('products.name', 'like', '%' . $keyword . '%')->get();

                        foreach($products as $p) {
                            array_push($resp, $p);
                        }
                    }

                    return $resp;                   
                }
            } else {    // b != 0
                if($keyword == null || ($keyword && strlen($keyword) ==0)) {    // c == null
                    return Product::where('products.product_sub_category_id', $subCategoryId)->get();
                } else {
                    return Product::where('products.product_sub_category_id', $subCategoryId)->where('products.name', 'like', '%' . $keyword . '%')->get();                    
                }
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    public function updateInfo(Request $request)
    {

        $distributor = Distributor::updateOrCreate(
                ['id'=> $request['id']],
                ['name' => $request['name'],
                 'description' => $request['description']
                ]
        );

        return json_encode($distributor);
    }

    public function updateAddress(Request $request)
    {
        if($request['default_address']){
            DistributorAddress::where('distributor_id', $request['distributor_id'])->update(['default_address' => false]);
        }

        DistributorAddress::updateOrCreate(
            ['id' => $request['id'], 'distributor_id' => $request['distributor_id']],
            ['city' => $request['city'],
             'street' => $request['street'],
             'default_address' => $request['default_address'],
             'distributor_id' => $request['distributor_id']
            ]
        );

    }

    public function updateContact(Request $request) {
        Log::debug($request);

        if($request['default_contact']) {
            DistributorContact::where('distributor_id', $request['distributor_id'])->update(['default_contact' =>false]);
        }

        DistributorContact::updateOrCreate(
            ['id' => $request['id'], 'distributor_id' => $request['distributor_id']],
            [
                'name' => $request['name'],
                'mobile' => $request['mobile'],
                'phone_area_code' => $request['phone_area_code'],
                'telephone' => $request['telephone'],
                'default_contact' => $request['default_contact'],
                'distributor_id' => $request['distributor_id']
            ]
        );
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Distributor::destroy($id);

        //return Response('deleted', 200);
    }

    public function destroyAddressById($addressId)
    {
        return DistributorAddress::destroy($addressId);
    }

    public function destroyContactById($contactId)
    {
        return DistributorContact::destroy($contactId);
    }

    public function login(Request $request, $mobile) {
        
        //check if it's valid distributor
        $distributors = DistributorContact::where('mobile', $mobile)->get();
        if(count($distributors) > 0)
            return response('ok', 200);
        else 
            return response('not found', 404);
    }
}
