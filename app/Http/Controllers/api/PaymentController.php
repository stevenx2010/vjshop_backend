<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function alipay(Request $request)
    {
    	Log::debug('*********alipay status********');
    	Log::debug($request);

        /* if success, change following 
         1. ORDER_STATUS to PAID
         2. DELIVERY_STATUS to WAITING_FOR_SHIPPING
         3. INVOICE_STATUS to NO_ISSUED
         4. COMMENT_STATUS to NOT_COMMENTED
        */
    }

    public function alipayCallback(Request $request)
    {
    	Log::debug('------alipay callback---------');
    	Log::debug($request);

        /* if success, change following 
         1. ORDER_STATUS to PAID
         2. DELIVERY_STATUS to WAITING_FOR_SHIPPING
         3. INVOICE_STATUS to NO_ISSUED
         4. COMMENT_STATUS to NOT_COMMENTED
        */
    }

    public function wechat(Request $request)
    {

    }

    public function wechatCallback(Request $request)
    {
        
    }
}
