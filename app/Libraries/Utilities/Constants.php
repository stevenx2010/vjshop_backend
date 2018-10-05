<?php

namespace App\Libraries\Utilities\Constants;

abstract class InvoiceType {
	const PERSONAL = 1;
	const ENTERPRISE = 2;
}

abstract class InvoiceStatus {
	const NA = 0;
	const NOT_ISSUED = 1;
	const ISSUED = 2;
}




abstract class PaymentMethod {
	const WECHAT = 1;
	const ALIPAY = 2;
}

abstract class CouponDiscountMethod {
	const PERCENTAGE = 1;
	const VALE = 2;
	const FREE_SHIPPING = 3;
}

abstract class OrderRefund {
	const NA = -1;
	const WAITING_FOR_REFUND = 0;
	const REFUNDED = 1;
}

abstract class RefundStatus {
	const NA = -1;
	const APPLICATION_FOR_REFUND = 1
	const WAITING_FOR_REFUND = 2;
	const REFUNDED = 3;
}
