<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ShippingAddress;
use App\Order;

class Customer extends Model
{
    public function addresses() {
    	return $this->hasMany('App\ShippingAddress');
    }

    public function orders()  {
    	return $this->hasMany('App\Order');
    }
}
