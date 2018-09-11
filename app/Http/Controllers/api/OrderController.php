<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Order;
use App\Product;
use App\Coupon;

use App\Libraries\Utilities\DeliveryStatus;

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

        // Attach/Update coupons
        foreach($request['coupon_used_ids'] as $id) {
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
                case DeliveryStatus::RECEIVED:
                    $order->delivery_date = $datetime;
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
        //
    }
}
