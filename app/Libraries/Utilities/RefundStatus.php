<?php

namespace App\Libraries\Utilities;

abstract class RefundStatus {
	const NA = -1;
	const APPLICATION_FOR_REFUND = 1;
	const WAITING_FOR_REFUND = 2;
	const REFUNDED = 3;
}