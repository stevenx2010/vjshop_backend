<?php
namespace App\Libraries\Payment;

class AlipayOrderInfo {
	public $body;
	public $subject;
	public $out_trade_no;
//	public $timeout_express;
	public $total_amount;
	public $product_code = 'QUICK_MSECURITY_PAY';
}