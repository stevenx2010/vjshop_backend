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
    }

    public function alipayCallback(Request $request)
    {
    	Log::debug('------alipay callback---------');
    	Log::debug($request);
    }
}
