<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Order;
use App\Product;
use App\Coupon;
use App\Customer;
use App\ShippingAddress;
use App\Distributor;
use App\DistributorContact;
use App\DistributorAddress;

use App\Libraries\Utilities\DeliveryStatus;
use App\Libraries\Utilities\OrderStatus;
use App\Libraries\Utilities\CommentStatus;
use App\Libraries\Utilities\InvoiceStatus;
use App\Libraries\Utilities\RefundStatus;

use App\Libraries\Payment\PaymentMethods;
use App\Libraries\Payment\AlipayOrderInfo;
use App\Libraries\Payment\AlipayPayRequest;
use App\Libraries\Payment\WechatUnifiedOrderRequest;
use App\Libraries\Payment\WechatPay;
use App\Libraries\Payment\WechatPayRequest;

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

   public function showOrdersByStatusPaged($mobile, $orderStatus, $page) 
    {
        $page_size = 10;
        Log::debug($mobile);
        $user = Customer::where('mobile', $mobile);
        $userId = (json_decode($user->get(), true))[0]['id'];
        $user_obj = Customer::find($userId);

        switch($orderStatus) {
            case 'to_pay':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::NOT_PAY_YET)->skip($page * $page_size)->limit($page_size)->get();
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

                $thumbnail_url = Product::select('thumbnail_url')->where('id', $product->id)->get();
                $shoppingItem['thumbnail_url'] = $thumbnail_url[0]->thumbnail_url;

                array_push($shoppingItems, $shoppingItem);

                $resp = json_decode($order, true);
                $resp['products'] = $shoppingItems;
            }

            array_push($final_resp, $resp);
        }
        return $final_resp;
    }

    public function showByConditions(Request $request)
    {
        Log::debug($request);
        $orders = Order::all();

        Log::debug($orders);

        if($request['order_serial'] != null) {
            $orders = $orders->where('order_serial', $request['order_serial']);
        }
 
        if($request['keyword'] != null) {
            $keyword = $request['keyword'];
            $orders = Order::where('order_serial', 'like', '%'. $keyword . '%')->get();

            if(count($orders) < 1) return [];
        }

        if($request['mobile'] != null) {
            $id = Customer::select('id')->where('mobile', $request['mobile'])->get();
            $id_array = json_decode($id, true);
            if($id_array)
                $orders = Customer::find(($id_array[0])['id'])->orders()->get();
            else
                return [];
        }
        if($request['order_status'] > 0) {
            $orders = $orders->where('order_status', $request['order_status']);
        }
        if($request['delivery_status'] > 0) {
            $orders = $orders->where('delivery_status', $request['delivery_status']);
        }
        if($request['invoice_status'] > 0) {
            $orders = $orders->where('invoice_status', $request['invoice_status']);
        }
        if($request['comment_status'] > 0) {
            $orders = $orders->where('comment_status', $request['comment_status']);
        }

        if($request['query_by_date']) {
            $orders = $orders->where('order_date', '>', $request['date1'])->where('order_date', '<', $request['date2']);
        }

        if($request['invoice_required']) {
            $orders = $orders->where('is_invoice_required', true)->where('invoice_status', InvoiceStatus::NOT_ISSUED)->where('delivery_status', DeliveryStatus::CONFIRMED);
        }

        if($request['invoice_issued']) {
            $orders = $orders->where('invoice_status', InvoiceStatus::ISSUED);
        }

        if($request['refund_orders']) {
            $orders = $orders->where('refund_status', RefundStatus::REFUNDED);
        }

        if($request['refund_process']) {
            $orders = $orders->where('refund_status', '!=', RefundStatus::REFUNDED)->where('order_status', '!=', OrderStatus::NOT_PAY_YET);
        }

        $final_resp = [];
        foreach ($orders as $order) {
            array_push($final_resp, $order);
        }
        return $final_resp;
    }

    public function showByConditionsForDistributor(Request $request)
    {
        Log::debug($request);
        $distributor_id = $request['distributor_id'];
        $distributor = Distributor::find($distributor_id);

        if($request['keyword'] != null) {
            $keyword = $request['keyword'];
            $orders = Order::where('order_serial', 'like', '%'. $keyword . '%')->where('distributor_id', $request['distributor_id'])->where('order_status', '!=', OrderStatus::NOT_PAY_YET)->get();
            if(count($orders) < 1) return [];
        }

        $orders = $distributor->orders()->where('order_status', '!=', OrderStatus::NOT_PAY_YET)->get();

        if($request['order_serial'] != null) {
            $orders = $orders->where('order_serial', $request['order_serial']);
        }

        if($request['delivery_status'] != 0) {
            $orders = $orders->where('delivery_status', $request['delivery_status']);
        }

        if($request['query_by_date']) {
            $orders = $orders->where('order_date', '>', $request['date1'])->where('order_date', '<', $request['date2']);
        }



        $final_resp = [];
        foreach ($orders as $order) {
            array_push($final_resp, $order);
        }
        return $final_resp;

    }
    public function showDetailByOrderId($orderId)
    {
        $order = Order::find($orderId);
        $shipping_address = ShippingAddress::where('id', $order->shipping_address_id)->get();
        $customer = Customer::where('id', $order->customer_id)->get();
        $products = $order->products()->get();
        $distributor = Distributor::where('id', $order->distributor_id)->get();
        $distributor_address = DistributorAddress::where('distributor_id', $order->distributor_id)->where('default_address', 1)->get();
        $distibutor_contact = DistributorContact::where('distributor_id', $order->distributor_id)->where('default_contact', 1)->get();

        $resp = [];

        if($order->invoice_status == InvoiceStatus::ISSUED) {
            $invoice = $order->invoice()->get();
            $resp['invoice'] = $invoice;
        }
        
        $resp['shipping_address'] = $shipping_address;
        $resp['customer'] = $customer;
        $resp['products'] = $products;
        $resp['distributor'] = $distributor;
        $resp['distributor_contact'] = $distibutor_contact;
        $resp['distributor_address'] = $distributor_address;

        return $resp;
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

        $payment_method = $request['payment_method'];
        $order_id = $request['id'];
        $order_price = 0;   
        $order_body = 'VJ';   
        $order_subject = '';

        if($order_id == null) {     // new order to pay

            // Update/Create Order Basic info
            $order = Order::updateOrCreate(
                ['order_serial' => $request['order_serial']],
                [
                    'order_serial' => $request['order_serial'],
                    'customer_id' => $request['customer_id'],
                    'distributor_id' => $request['distributor_id'],
                    'total_price' => $request['total_price'],
                    'shipping_charges' => $request['shipping_charges'],
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

            // Attach/Update product_ids & Calculate Order Price
            foreach($request['products'] as $product) {
                Log::debug($product);
                $thisOrder = Order::find($order_id);

                $product_id =$product['productId'];
                $thisProduct = Product::find($product_id);

                $thisProductDetails = Product::select('model', 'name', 'product_sub_category_name', 'description')->where('id', $product_id)->get();
                $thisProductDetails_array = json_decode($thisProductDetails, true);
                Log::debug($thisProductDetails_array);

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

                $order_price += $product['price'] * $product['quantity'];

                if(strlen($order_body) < 120) {
                    $order_body = $order_body . '-' . $thisProductDetails_array[0]['model'];
                } 
                $order_subject = $thisProductDetails_array[0]['product_sub_category_name'];
        
            
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
        } else { // existing unpaid order
            $order = Order::find($request['id']);
            $order->payment_method = $request['payment_method'];
            $order->order_date = $request['order_date'];
            $order->order_serial = $request['order_serial'];
            $order->save();

            // calculate the total price
            foreach($request['products'] as $product) {
                $order_price += $product['price'] * $product['quantity'];

                $thisProductDetails = Product::where('id', $product['productId'])->get();
                $thisProductDetails_array = json_decode($thisProductDetails, true);

                if(strlen($order_body) < 120) {
                    $order_body = $order_body . '-' . $thisProductDetails_array[0]['model'];
                } 
                $order_subject = $thisProductDetails_array[0]['product_sub_category_name'];
            }
        }           

        Log::debug($order_price);
        Log::debug($order_body);
        Log::debug($order_subject);

        // Assemble payment packets
        switch ($payment_method) {
            case PaymentMethods::WECHAT:
                // Step 1: Prepare preorder request
                $preOrderObj = new WechatUnifiedOrderRequest($request['order_serial']);
                //$preOrderObj->appid = env('WECHAT_PAY_APP_ID');
                //$preOrderObj->mch_id = env('WECHAT_MCH_ID');
                //$preOrderObj->nonce_str = strtoupper(md5($request['order_serial']));
                $preOrderObj->body .= $order_body;
                $preOrderObj->out_trade_no = $request['order_serial'];
                $preOrderObj->total_fee = 1; // round($order_price * 100); test 1 cent
                $preOrderObj->spbill_create_ip = $request->ip();
                //$preOrderObj->notify_url = env('WECHAT_NOTIFY_URL');
              
                $prePayRequest = $preOrderObj->getPreOrderRequest();

                 Log::debug($prePayRequest);

                // Step 2: send preorder request to Wechat Service to get the prepayId
                $wechatPayObj = new WechatPay($prePayRequest, env('WECHAT_UNIFIED_ORDER_URL'));

                $prepayId = '';
                if($wechatPayObj->sendUnifiedOrderRequest()) {
                    $prepayId = $wechatPayObj->getPrepayId();
                }

                // Step 3: prepare the pay request with the prepayId
                $payRequestObj = new WechatPayRequest($prepayId, $request['order_serial']);

                $final_resp = $payRequestObj->getWechatPayRequest();
                break;
            
            case PaymentMethods::ALIPAY:
                $order_info = new AlipayOrderInfo();
                $order_info->body = $order_body;
                $order_info->subject = $order_subject;
                $order_info->out_trade_no = $request['order_serial'];
                $order_info->total_amount = '0.01'; //round($order_price) . '';

                Log::debug(json_encode($order_info));
                $bz_content = json_encode($order_info);

                $payRequest = new AlipayPayRequest($bz_content);

                $final_resp = $payRequest->getRequest();
                break;
        }

        return json_encode($final_resp);

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

    public function updatePaymentMethod($orderId, $paymentMethod)
    {
        $order = Order::find($orderId);
        $order->payment_method = $paymentMethod;

        $order->save();
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
        if($order) {
            $order->delete();  
            return Response('deleted', 200);
        } else {
            return Response('id not found (delete)', 400);
        }   
    }

    public function destroyByOrderSerial($orderSerial)
    {
        return Order::where('order_serial', $orderSerial)->delete();
    }
}
