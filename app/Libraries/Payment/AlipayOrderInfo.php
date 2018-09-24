<?php
namespace App\Libraries\Payment;

class AlipayOrderInfo {
	public $timeout_express = '15m';
	public $product_code = 'QUICK_MSECURITY_PAY';
	public $total_amount;
	public $subject;	
	public $body;	
	public $out_trade_no;	
}