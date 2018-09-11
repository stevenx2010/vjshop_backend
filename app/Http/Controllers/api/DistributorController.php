<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Distributor;
use App\DistributorAddress;
use App\DistributorContact;
use App\Order;
use App\ShippingAddress;

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
