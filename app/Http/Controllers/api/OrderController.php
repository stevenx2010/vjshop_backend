<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Order;
use App\Product;
use App\Coupon;
use App\Customer;

use App\Libraries\Utilities\DeliveryStatus;
use App\Libraries\Utilities\OrderStatus;
use App\Libraries\Utilities\CommentStatus;

use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
    public function show($id)
    {
        //
    }

    public function showOrdersByStatus($mobile, $orderStatus) 
    {
        Log::debug($mobile);
        $user = Customer::where('mobile', $mobile);
        $userId = (json_decode($user->get(), true))[0]['id'];
        $user_obj = Customer::find($userId);

        switch($orderStatus) {
            case 'to_pay':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::NOT_PAY_YET)->get();
                $final_resp = $this->processOrders($orders);
                break;
            case 'to_delivery':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::PAYED)->where('delivery_status', DeliveryStatus::WAITING_FOR_DELIVERY)->orWhere('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->get();
                $final_resp = $this->processOrders($orders);
                break;
            case 'to_receive':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->orWhere('order_status', OrderStatus::COMMENTED)->where('delivery_status', DeliveryStatus::CONFIRMED)->get();
                $final_resp = $this->processOrders($orders);            
                break;
            case 'to_comment':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::CONFIRMED)->where('comment_status', CommentStatus::NOT_COMMENTED)->get();
                $final_resp = $this->processOrders($orders);
                break;
        }

        usort($final_resp, array($this, 'cmp'));

        return json_encode($final_resp);
    }

    public function cmp($a, $b)
    {
        return strcmp($b['order_date'], $a['order_date']);
    }

    // Helper function
    public function processOrders($orders) {
        $final_resp = [];
        foreach ($orders as $order) {
            $products = $order->products()->get();
            $shoppingItem = [];
            $shoppingItems = [];
            foreach($products as $product) {

                $shoppingItem['productId'] = $product->id;
                $shoppingItem['quantity'] = $product->pivot->quantity;
                $shoppingItem['price'] = $product->pivot->price;
                $shoppingItem['weight'] = $product->weight;
                $shoppingItem['weight_unit'] = $product->weight_unit;
                $shoppingItem['selected'] = true;

                array_push($shoppingItems, $shoppingItem);

                $resp = json_decode($order, true);
                $resp['products'] = $shoppingItems;
            }

            array_push($final_resp, $resp);
        }
        return $final_resp;
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
    public function update(Request $request)
    {
        Log::debug($request);
        Log::debug($request['shipping_address']['id']);

        // Update/Create Order Basic info
        $order = Order::updateOrCreate(
            ['order_serial' => $request['order_serial']],
            [
                'order_serial' => $request['order_serial'],
                'customer_id' => $request['customer_id'],
                'distributor_id' => $request['distributor_id'],
                'total_price' => $request['total_price'],
                'total_weight' => $request['total_weight'],
                'order_date' => $request['order_date'],
                'delivery_date' => $request['delivery_date'],
                'delivery_status' => $request['delivery_status'],
                'payment_method' => $request['payment_method'],
                'shipping_address_id' => $request['shipping_address']['id'],
                'order_status' => $request['order_status'],
                'is_invoice_required'=> $request['is_invoice_required'],
                'invoice_status' => $request['invoice_status'],
                'invoice_head' =>$request['invoice_head'],
                'invoice_tax_number' => $request['invoice_tax_number'],
                'invoice_type' => $request['invoice_type']
                ]
            );

        $order_id = json_decode($order, true)['id'];

        // Attach/Update product_ids       
        foreach($request['products'] as $product) {
            Log::debug($product);
            $thisOrder = Order::find($order_id);

            $product_id =$product['productId'];
            $thisProduct = Product::find($product_id);

            if($thisOrder->products->contains($thisProduct)) {
                $thisOrder->updateExistingPivot($product_id,
                    [   'price' => $product['price'],
                        'quantity' => $product['quantity']
                    ]
                );
            } else {
                $thisOrder->products()->attach([$product_id => 
                    [   'price' => $product['price'], 
                        'quantity' => $product['quantity']
                    ]
                ]);
            }
        }

        // set these coupons as used
        $user = Customer::find($request['customer_id']);

        // Attach/Update coupons
        foreach($request['coupon_used_ids'] as $id) {
            foreach($user->coupons as $c) {
                if($c['id'] == $id) {
                    $user->coupons()->detach($id);
                    $user->coupons()->attach([$id => ['quantity' => 0]]);
                }
            }

            $coupon = Coupon::find($id);

            if(!$thisOrder->coupons->contains($coupon)) {
                $thisOrder->coupons()->attach($id);
            }
        }

    }

    public function updateDeliveryStatus($orderId, $status, $datetime) 
    {
        $order = Order::find($orderId);
        if($order != null) {
            $order->delivery_status = $status;
            switch($status) {
                case DeliveryStatus::DELIVERED_NOT_CONFIRM:
                    $order->delivery_date = $datetime;
                    $order->order_status = OrderStatus::RECEIVED;
                    break;
                case DeliveryStatus::CONFIRMED:
                    $order->delivery_confirm_date = $datetime;
                    break;
            }
            
            $order->save();
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::debug($id);
        $order = Order::find($id);
        $order->delete();  

        return Response('deleted', 200);   
    }
}
