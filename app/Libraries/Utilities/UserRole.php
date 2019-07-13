<?php

abstract class UserRole {
	const NORMAL_USER			= 0b000000000;
	const ADMINISTRATOR 		= 0b000000001;
	const PRODUCT_MANAGER 		= 0b000000010;
	const DISTRIBUTOR_MANAGER	= 0b000000100;
	const COUPON_MANAGER		= 0b000001000;
	const ORDER_MANAGER			= 0b000010000;
	const INVOICE_MANAGER		= 0b000100000;
	const PAGE_MANAGER			= 0b001000000;
	const SETTING_MANAGER		= 0b010000000;
	const PRICE_MANAGER			= 0b100000000;
}