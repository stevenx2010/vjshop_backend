<?php

namespace App\Libraries\Utilities;

abstract class OrderStatus {
	const NOT_PAY_YET = 1;
	const PAYED = 2;
	const RECEIVED = 3;
	const CLOSED = 4;
	const CANCELED = 5;
	const COMMENTED = 6;
	const NOT_COMMENTED = 7;
}