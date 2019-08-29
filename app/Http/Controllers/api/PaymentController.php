<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Cache;

use App\Libraries\Payment\WechatNotify;
use App\Libraries\Payment\AlipayNotify;

use App\Libraries\Utilities\OrderStatus;
use App\Libraries\Utilities\DeliveryStatus;
use App\Libraries\Utilities\InvoiceStatus;
use App\Libraries\Utilities\CommentStatus;
use App\Libraries\Utilities\LogType;

use App\Order;
use App\AppLog;
use App\Product;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Libraries\Payment\AlipayPayRequest;
use App\Libraries\Payment\AlipayInterfaces;
use App\Libraries\Payment\AlipayQueryInfo;

class PaymentController extends Controller
{
    /* if success, change following 
         1. ORDER_STATUS to PAID
         2. DELIVERY_STATUS to WAITING_FOR_SHIPPING
         3. xxx INVOICE_STATUS to NO_ISSUED: only set it when DELIVERY_STATUS == DELIVERED_NOT_CONFIRMED
         4. xxx COMMENT_STATUS to NOT_COMMENTED: only set it when DELIVERY_STATUS == DELIVERED_NOT_CONFIRMED
         5. write to Log
        */
    public function alipay(Request $request)
    {
    	Log::debug('*********alipay status********');
    	Log::debug($request);

        $notifyObj = new AlipayNotify($request);
        // Step 1: Verify the signature of request
        if($notifyObj->isSignVerified()) {
            // step 2: check if the trade is successful
            if($request['trade_status'] == 'TRADE_SUCCESS' || $request['trade_status'] == 'TRADE_FINISHED') {
                // Step 3: verify app_id, alipay_pid, order_serial, order_totl_price all match
                $app_id = $request['auth_app_id'];
                $alipay_pid = $request['seller_id'];
                $order_serial = $request['out_trade_no'];
                $order_total_price = $request['total_amount'];

                $this->doLog(LogType::PAYMENT_ALIPAY_IN, $order_serial, json_encode($request->all()));
                
                // Step 4: locking the processing to avoiding multiple same requests
                if(Cache::get($order_serial) == null) {
                    Cache::put($order_serial, true);

                    if(($alipay_pid == env('ALIPAY_PID')) && ($app_id) == env('ALIPAY_APP_ID')) {
                        $order_array = Order::where('order_serial', $order_serial)->get();
                        if($order_array && count($order_array) > 0) { // we have this order in db
                            $order_id = $order_array[0]['id'];
                            $order = Order::find($order_id);

                            /*****************CHANGE THIS LINE IN PRODUCTION *************************/
                            if($order_total_price == $order->total_price) {
                            //if($order_total_price == 0.01/*$order->total_price*/) {     // all parameters are matched, then go furhter
                                $order->order_status = OrderStatus::PAID;
                                $order->delivery_status = DeliveryStatus::WAITING_FOR_DELIVERY;

                                //try {
                                    $response = 'success';
                                //    DB::transaction(function() use ($order, $response) {
                                        $order->save();
                                        // update product sold amount
                                        $this->updateSoldAmount($order);                                        
                                //    });
                                    $this->doLog(LogType::PAYMENT_ALIPAY_SUCCESS, $order_serial, $response);
/*                                } catch (\Exception $e) {
                                    $response = 'FAIL_DB_TRANSACTION';
                                    $this->doLog(LogType::PAYMNET_ALIPAY_TRANS_ERR, $order_serial, $response);
                                }*/
                            } else {
                                $response = 'FAIL_ORDER_PRICE_MISMTACH';
                                $this->doLog(LogType::PAYMENT_ALIPAY_TRANS_ERR, $order_serial, $response);
                            }
                        } else {
                            $response = 'FAIL_NO_SUCH_ORDER';
                            $this->doLog(LogType::PAYMENT_ALIPAY_TRANS_ERR, $order_serial, $response);
                        }
                    } else {
                        $response = 'FAIL_PID_OR_APP_ID';
                        $this->doLog(LogType::PAYMENT_ALIPAY_TRANS_ERR, $order_serial, $response);
                    }
                } else {
                    Log::debug('Processing... LOCKED! Alipay');
                    $response = 'FAIL_LOCKED_PROCESSING';
                    $this->doLog(LogType::PAYMENT_ALIPAY_TRANS_ERR, $order_serial, $response);
                }

                $this->doLog(LogType::PAYMENT_ALIPAY_OUT, $order_serial, $response);

                // Release the lock
                Cache::forget($order_serial);
                return Response($response, 200)->header('Content-type', 'application/json');

            } else $response = 'FAIL_TRADE';
        } else $response = 'FAIL_ALIPAY_SIGN_MISMATCH';

        $this->doLog(LogType::PAYMENT_ALIPAY_OUT, $request['out_trade_no'], $response);
        return Response($response, 200)->header('Content-type', 'application/json');
    }

    public function alipayCallback(Request $request)
    {
    	Log::debug('------alipay callback---------');
    	Log::debug($request);

        
    }

    public function wechat(Request $request)
    {
        Log::debug('--------wechat notification-----------');
        $response_err ='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[NOTFOUND]]></return_msg></xml>';
        $xml = file_get_contents('php://input');

        Log::debug($xml);

        $notifyObj = new WechatNotify($xml);

        if($notifyObj->isSignatureCorrect()) {
            $req = $notifyObj->getRequest();

            if($req['return_code'] =='SUCCESS') {
                $order_serial = $req['out_trade_no'];

                $this->doLog(LogType::PAYMENT_WECHAT_IN, $order_serial, $xml);

                $response = '';

                // mutex to avoid multiple notifications in the same time
                if(Cache::get($order_serial) == null) {
                    Cache::put($order_serial, true);

                    $order_array = Order::where('payment_serial', $order_serial)->get();

                    if(count($order_array) > 0) {
                        $order = Order::find($order_array[0]->id);
                    
                        if($order) {
                            // check if the amount is equal
                            $order_price = $order->total_price * 100;

                            //**************COMMENT OUT THIS LINE IN PRODUCTION***********************//
                            //$order_price = 1;
                            $order_price_received = $req['total_fee'];
                            if($order_price == $order_price_received) {
                                $order->order_status = OrderStatus::PAID;
                                $order->delivery_status = DeliveryStatus::WAITING_FOR_DELIVERY;
                              //  $order->invoice_status = InvoiceStatus::NOT_ISSUED;
                                $response = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';

                                //try {
                                //    DB::transaction(function() use ($order, $response) {
                                        $order->save();
                                        // update product sold amount
                                        $this->updateSoldAmount($order);
                                //    });

                                    $this->doLog(LogType::PAYMENT_WECHAT_SUCCESS, $order_serial, $response);
                                /*} catch(\Exception $e) {
                                    $response = $response_err;
                                    $this->doLog(LogType::PAYMENT_WECHAT_TRANS_ERR, $order_serial, $response);
                                }*/
                                
                            } else {
                                $response = $response_err;
                                $this->doLog(LogType::PAYMENT_WECHAT_TRANS_ERR, $order_serial, $response);
                            }
                        } else {
                                $response = $response_err;
                                $this->doLog(LogType::PAYMENT_WECHAT_TRANS_ERR, $order_serial, $response);
                            }
                        
                    } else {
                        $response = $response_err;
                        $this->doLog(LogType::PAYMENT_WECHAT_TRANS_ERR, $order_serial, $response);
                    }

                    $this->doLog(LogType::PAYMENT_WECHAT_OUT, $order_serial, $response);

                    // remove the locks
                    Cache::forget($order_serial);

                    // send success response to Wechat               
                    return Response($response, 200)->header('Content-type', 'application/xml');
                } else {
                    Log::debug('processing.... LOCKED! Wechat');
                }              
            }
        }

        Log::debug($notifyObj->getRequest());

        $response = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[SIGNMISMATCH]]></return_msg></xml>';

        return Response($response, 200)->header('Content-type', 'application/xml');
    }

    private function doLog($type, $key, $content) {
        $log = new AppLog();
        $log->log_type = $type;
        $log->log_key = $key;
        $log->content = $content;
        $log->save();
    }

    private function updateSoldAmount($order) {
        $products = $order->products()->get();

        foreach ($products as $product) {
            Log::debug($product);
            $sold_amount = $product->sold_amount + $product->pivot->quantity;
            
            $pdt_obj = Product::find($product->id);
            $pdt_obj->sold_amount = $sold_amount;
            $pdt_obj->save();
        }
    }

    public function alipayQuery($order_serial)
    {
        $alipay_query_info = new AlipayQueryInfo();

        $alipay_query_info->out_trade_no = $order_serial;

        $bz_conent = json_encode($alipay_query_info);

        $alipay_request = new AlipayPayRequest($bz_conent, 'utf-8', AlipayInterfaces::TRADE_QUERY);

        // Set query request to Alipay
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, env('ALIPAY_GATEWAY_URL') . '?' . $alipay_request->getRequest());
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }
}
