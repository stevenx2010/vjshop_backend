<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    public function customer() {
    	return $this->belongsTo('App\customer');
    }
}
