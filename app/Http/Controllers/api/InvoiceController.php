<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Invoice;
use App\Order;
use App\Libraries\Utilities\InvoiceStatus; 
use App\Libraries\Utilities\DeliveryStatus;

use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function updateOrCreate(Request $request)
    {
    	Log::debug($request);
    	
    	$image_url = '';
    	if($request->hasFile('image_file')) {
    		$file = $request->file('image_file');

    		$hashName = $file->hashName();
    		Log::debug($hashName);

    		if($file->getMimeType() == 'image/jpeg')
                 $hashName = substr_replace($hashName, 'jp', -4, -1);
            $image_url = 'imgs/' . $hashName;

            $file->move(base_path('public/imgs'), $hashName);

    	}

    	$invoice = Invoice::updateOrCreate(
    		[
    		 'order_id' => $request['order_id']
    		],
    		[
    			'order_id' => $request['order_id'],
    			'invoice_number' => $request['invoice_number'],
    			'invoice_amount' => $request['invoice_amount'],
    			'approved_by' => $request['approved_by'],
    			'audited_by' => $request['audited_by'],
    			'issued_by' => $request['issued_by'],
    			'issued_date' => $request['date'],
    			'image_url' => $image_url	
    		]
    	);

    	$order = Order::find($request['order_id']);
    	$order->invoice_status = InvoiceStatus::ISSUED;
    	$order->save();

    	return $invoice;
    }
}
