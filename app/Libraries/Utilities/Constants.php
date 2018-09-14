<?php
abstract class InvoiceType {
	const PERSONAL = 1;
	const ENTERPRISE = 2;
}

abstract class InvoiceStatus {
	const NOT_ISSUED = 1;
	const ISSUED = 2;
}

abstract class OrderStatus {
	const NOT_PAY_YET = 1;
	const PAYED = 2;
	const CLOSED = 3;
	const CANCELED = 4;
	const COMMENTED = 5;
	const NOT_COMMENTED = 6;
}



abstract class PaymentMethod {
	const WECHAT = 1;
	const ALIPAY = 2;
}

abstract class CouponDiscountMethod {
	const PERCENTAGE = 1;
	const VALE = 2;
}


