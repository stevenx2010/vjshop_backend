<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Refund;
use App\Order;

use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
   public function updateOrCreate(Request $request)
   {
   	Log::debug($request);
   	$refund = Refund::updateOrCreate(
   		[
   		'order_id' => $request['order_id']], 
   		[
   			'order_id' => $request['order_id'],
   			'refund_status' => $request['refund_status'],
   			'refund_amount' => floatval($request['refund_amount']),
   			'refund_reason' => $request['refund_reason'],
   			'refund_date' => $request['refund_date'],
   			'approved_by' => $request['approved_by'],
   			'audited_by' => $request['audited_by']
   	]);

   	if($request['order_id']) {
   		$order = Order::find($request['order_id']);
   		$order->refund_status = $request['refund_status'];
   		$order->save();
   	}
   }

   public function showByOrderId($orderId)
   {
   	return Refund::where('order_id', $orderId)->get();
   }
}
