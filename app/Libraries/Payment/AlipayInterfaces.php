<?php
namespace App\Libraries\Payment;

class AlipayInterfaces {
	const APP_PAY = 'alipay.trade.pay';
	const TRADE_CLOSE = 'alipay.trade.close';
	const TRADE_CANCEL = 'alipay.trade.cancel';
	const TRADE_REFUND = 'alipay.trade.refund';
	const TRADE_QUERY = 'alipay.trade.query';
}