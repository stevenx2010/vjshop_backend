<?php

namespace App\Libraries\Utilities;

abstract class DeliveryStatus {
	const WAITING_FOR_DELIVERY = 1;
	const IN_DELIVERY = 2;
	const DELIVERED_NOT_CONFIRM = 3;
	const CONFIRMED = 4;
}