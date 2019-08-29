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
use App\AppLog;
use App\User;
use App\AccessLog;

use App\Libraries\Utilities\DeliveryStatus;
use App\Libraries\Utilities\OrderStatus;
use App\Libraries\Utilities\CommentStatus;
use App\Libraries\Utilities\InvoiceStatus;
use App\Libraries\Utilities\RefundStatus;
use App\Libraries\Utilities\LogType;
use App\Libraries\Utilities\AccessType;

use App\Libraries\Payment\PaymentMethods;
use App\Libraries\Payment\AlipayOrderInfo;
use App\Libraries\Payment\AlipayPayRequest;
use App\Libraries\Payment\WechatUnifiedOrderRequest;
use App\Libraries\Payment\WechatPay;
use App\Libraries\Payment\WechatPayRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
                //$orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::WAITING_FOR_DELIVERY)->orWhere('delivery_status', DeliveryStatus::IN_DELIVERY)->get();
                $orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::WAITING_FOR_DELIVERY)->get();
                $final_resp = $this->processOrders($orders);
                break;
            case 'to_receive':
                //$orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->orWhere('order_status', OrderStatus::COMMENTED)->where('delivery_status', DeliveryStatus::CONFIRMED)->get();
                $orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::IN_DELIVERY)->get();
                $final_resp = $this->processOrders($orders);            
                break;
            case 'to_confirm':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->orderBy('order_date', 'desc')->get();
                $final_resp = $this->processOrders($orders);            
                break;
            case 'to_comment':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->where('comment_status', CommentStatus::NOT_COMMENTED)->get();
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
                //$orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::WAITING_FOR_DELIVERY)->orWhere('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->get();
                $orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::WAITING_FOR_DELIVERY)->get();
                $final_resp = $this->processOrders($orders);
                break;
            case 'to_receive':
                //$orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->orWhere('order_status', OrderStatus::COMMENTED)->where('delivery_status', DeliveryStatus::CONFIRMED)->get();
                $orders = $user_obj->orders()->where('order_status', OrderStatus::PAID)->where('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->get();
                $final_resp = $this->processOrders($orders);            
                break;
            case 'to_confirm':
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->orderBy('order_date', 'desc')->get();
                $final_resp = $this->processOrders($orders);            
                break;
            case 'to_comment':
                //$orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::CONFIRMED)->where('comment_status', CommentStatus::NOT_COMMENTED)->get();
                $orders = $user_obj->orders()->where('order_status', OrderStatus::RECEIVED)->where('delivery_status', DeliveryStatus::DELIVERED_NOT_CONFIRM)->where('comment_status', CommentStatus::NOT_COMMENTED)->get();
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

            // Get coupons used
            $coupons = $order->coupons()->get();
            $resp['coupons'] = $coupons;

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
  /*
        if($request['invoice_status'] > 0) {
            $orders = $orders->where('invoice_status', $request['invoice_status']);
        }
*/
        if($request['comment_status'] > 0) {
            $orders = $orders->where('comment_status', $request['comment_status']);
        }

        if($request['query_by_date']) {
            $orders = $orders->where('order_date', '>', $request['date1'])->where('order_date', '<', $request['date2']);
        }

        if($request['is_invoice_required']) {
            $orders = $orders->where('is_invoice_required', true)->where('invoice_status', $request['invoice_status']);
        }

/*
        if($request['invoice_issued']) {
            $orders = $orders->where('invoice_status', InvoiceStatus::ISSUED);
        }
*/
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

        $coupons = $order->coupons()->get();

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
        $resp['coupons'] = $coupons;

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
                    'payment_serial' => $request['payment_serial'],
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
                    'is_invoice_required'=> $request['is_invoice_required'] ? 1 : 0,
                    'invoice_status' => $request['invoice_status'],
                    'invoice_head' =>$request['invoice_head'],
                    'invoice_tax_number' => $request['invoice_tax_number'],
                    'email' => $request['email'],
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
        
                // deduct coupons used from order price

                // deduct shipping free from order price
            
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

            $order_price = $request['total_price'];
            
        } else { // existing unpaid order
            $order = Order::find($request['id']);
            $order->payment_method = $request['payment_method'];
            $order->order_date = $request['order_date'];
            $order->order_serial = $request['order_serial'];
            $order->payment_serial = $request['payment_serial'];
            $order->save();

            $order_price = $order->total_price;

            // calculate the total price
            foreach($request['products'] as $product) {
                //$order_price += $product['price'] * $product['quantity'];

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

        $this->doLog(LogType::ORDER_FROM_USER, $request['order_serial'], json_encode($request));

        // Assemble payment packets
        switch ($payment_method) {
            case PaymentMethods::WECHAT:
                // Step 1: Prepare preorder request
                $preOrderObj = new WechatUnifiedOrderRequest($request['payment_serial']);
                $preOrderObj->body .= $order_body;
                $preOrderObj->out_trade_no = $request['payment_serial'];

                //**************** CHANGE THIS LINE IN PRODUCTON *******************//
                $preOrderObj->total_fee = round($order_price * 100);
                //$preOrderObj->total_fee = 1; //round($order_price * 100);   //for test: set it to 1 
                $preOrderObj->spbill_create_ip = $request->ip();
              
                $prePayRequest = $preOrderObj->getPreOrderRequest();

                 Log::debug($prePayRequest);

                // Step 2: send preorder request to Wechat Service to get the prepayId
                $wechatPayObj = new WechatPay($prePayRequest, env('WECHAT_UNIFIED_ORDER_URL'));

                //$this->doLog(LogType::PAYMENT_WECHAT_OUT, $request['order_serial'], $wechatPayObj->getRequest());

                $prepayId = '';
                if($wechatPayObj->sendUnifiedOrderRequest()) {
                    $prepayId = $wechatPayObj->getPrepayId();
                    //$this->doLog(LogType::PAYMENT_WECHAT_IN, $request['order_serial'], $wechatPayObj->getResponseRaw());
                }

                // Step 3: prepare the pay request with the prepayId
                $payRequestObj = new WechatPayRequest($prepayId, $request['order_serial']);

                $final_resp = $payRequestObj->getWechatPayRequest();

                //$this->doLog(LogType::ORDER_BACKTO_USER, $request['order_serial'], json_encode($final_resp));
                break;
            
            case PaymentMethods::ALIPAY:
                $order_info = new AlipayOrderInfo();
                $order_info->body = $order_body;
                $order_info->subject = $order_subject;
                $order_info->out_trade_no = $request['order_serial'];
                //**************** CHANGE THIS LINE IN PRODUCTON *******************//
                $order_info->total_amount = round($order_price, 2) . '';
                //$order_info->total_amount = '0.01'; //round($order_price, 2) . '';  //for test: set it to '0.01'

                Log::debug(json_encode($order_info));
                $bz_content = json_encode($order_info);

                $payRequest = new AlipayPayRequest($bz_content);

                $final_resp = $payRequest->getRequest();
                break;
        }

        return json_encode($final_resp);

    }

    public function updateDeliveryStatus($orderId, $status, $datetime/*, $mobile*/) 
    {
        $order = Order::find($orderId);
        if($order != null) {
            $order->delivery_status = $status;
            switch($status) {
                case DeliveryStatus::IN_DELIVERY:
                    $order->delivery_date = $datetime;
                    $order->order_status = OrderStatus::PAID;
                    break;
                /*case DeliveryStatus::DELIVERED_NOT_CONFIRM:
                    $order->delivery_confirm_date = $datetime;
                    $order->order_status = OrderStatus::RECEIVED;
                    break;*/
                case DeliveryStatus::DELIVERED_NOT_CONFIRM:
                    $order->delivery_confirm_date = $datetime;
                    $order->order_status = OrderStatus::RECEIVED;
                    // Update invoice_status, comment_status
                    $order->invoice_status = InvoiceStatus::NOT_ISSUED;
                    $order->comment_status = CommentStatus::NOT_COMMENTED;

              //      $this->updateDistributorInventory($orderId, $mobile);
                    // update distributor inventoy
              //    2019-5-16
                    $distributor_id = $order->distributor_id;
                    $distributor = Distributor::find($distributor_id);

                    foreach($order->products()->get() as $product) {
                        $product_id = $product->pivot->product_id;
                        $quantity = $product->pivot->quantity;
                        foreach($distributor->products()->get() as $dis_product) {
                            if($dis_product->pivot->product_id == $product_id) {
                                $new_inventory = $dis_product->pivot->inventory - $quantity;
                                if($new_inventory < 0) $new_inventory =0;
                                $distributor->products()->updateExistingPivot($product_id, ['inventory' => $new_inventory]);
                            }
                        }

                    }

                    break;
                case DeliveryStatus::CONFIRMED:
                    break;
            }
            
            $order->save();
        }
    }

    private function updateDistributorInventory($orderId, $mobile)
    {
        $distributors =DistributorContact::where('mobile', $mobile)->get();
        if($distributors && count($distributors) > 0) {
            $distributor_id = $distributors[0]['distributor_id'];
            $distributor_obj = Distributor::find($distributor_id);

            $order = Order::find($orderId);
            foreach ($order->products as $product) {
                $quantity =$product->pivot->quantity;
                $inventory = $distributor_obj->products->where('product_id', $product->id);

                Log::debug('====================================');
                Log::debug($inventory);
            }
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
            // return coupon used in this order to the user
            $customer_id = $order->customer_id;
            $customer_obj = Customer::find($customer_id);

            foreach($order->coupons as $coupon) {
                Log::debug($coupon);
                $coupon_id = $coupon['id'];
                Log::debug($coupon_id);
                if($customer_obj->coupons->contains($coupon)) {
                    $customer_obj->coupons()->updateExistingPivot($coupon_id, ['quantity' => 1]);
                } else {
                    $customer_obj->coupons()->attach([$coupon_id => ['quantity' => 1]]);
                }
            }

            $order->delete();  

            // send back customer's all coupons
            $coupons = $customer_obj->coupons()->get();

            return Response($coupons, 200);
        } else {
            return Response('id not found (delete)', 400);
        }   
    }

    public function updatePrice(Request $request)
    {
        Log::debug($request);
        $email = $request['email'];
        $username = $request['username'];
        $orderSerial = $request['order_serial'];
        $orderId = $request['order_id'];
        $oldPrice = $request['old_price'];
        $newPrice = $request['new_price'];
        $moduleName = $request['module_name'];

        // Verify the data is authentic
        $users = User::where('name', $username)->get();
        $user_is_correct = ($email == md5($users[0]['email'])) ? true : false;
        
        $orders = Order::where('order_serial', $orderSerial)->get();
        $order_is_correct = ($orderId == md5($orders[0]['id'])) ? true : false;
        
        $price_is_correct = ($oldPrice == md5($orders[0]['total_price'])) ? true : false;

        if($user_is_correct && $order_is_correct && $price_is_correct) {
            $order_obj = Order::find($orders[0]['id']);
            $order_obj->total_price = $newPrice;

            //Log modifier
            $log = new AccessLog;
            $log->user = $username;
            $log->email = $users[0]['email'];
            $log->module_name = $moduleName;
            $log->access_type = AccessType::MODIFY;
            $log->previous_content = $orderSerial . '(' . $orders[0]['total_price'] . ')';
            $log->current_content = $orderSerial . '(' . $newPrice . ')';

            DB::transaction(function() use ($order_obj, $log) {
                $order_obj->save();
                $log->save();
            });
        } else {
            return response(json_encode(['price dismatch'], 404));
        }

    }

    public function queryPriceChangeHistory(Request $request)
    {
        Log::debug($request);
        $query_by_date = $request['query_by_date'];
        $date1 = $request['date1'];
        $date2 = $request['date2'];

        if($query_by_date) {
            return AccessLog::where('created_at', '>=', $date1)->where('created_at', '<=', $date2)->get();
        } else {
            return AccessLog::all();
        }
    }

    public function destroyByOrderSerial($orderSerial)
    {
        return Order::where('order_serial', $orderSerial)->delete();
    }

    private function doLog($type, $key, $content) {
        $log = new AppLog();
        $log->log_type = $type;
        $log->log_key = $key;
        $log->content = $content;
        $log->save();
    }

}
