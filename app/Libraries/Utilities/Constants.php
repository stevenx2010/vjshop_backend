<?php

abstract class InvoiceType {
	const PERSONAL = 1;
	const ENTERPRISE = 2;
}

abstract class InvoiceStatus {
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
}


