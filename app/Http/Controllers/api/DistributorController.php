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

        $orders = $distributor->orders()->get();

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
                ['name'=> $request['name']],
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
            ['city' => $request['city'], 'street' => $request['street'], 'distributor_id' => $request['distributor_id']],
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
            ['name' => $request['name'], 'distributor_id' => $request['distributor_id']],
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
        //
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
