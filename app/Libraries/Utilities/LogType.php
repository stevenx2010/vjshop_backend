<?php

namespace App\Libraries\Utilities;

abstract class LogType {
	const PAYMENT_WECHAT_OUT 	= 1;
	const PAYMENT_WECHAT_IN 	= 2;
	const PAYMENT_ALIPAY_OUT 	= 3;
	const PAYMENT_ALIPAY_IN		= 4;
	
	const ORDER_FROM_USER		= 5;
	const ORDER_BACKTO_USER		= 6;
}