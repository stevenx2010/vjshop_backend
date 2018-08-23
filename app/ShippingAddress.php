<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
	protected $fillable = ['username', 'customer_id', 'city', 'street', 'default_address'];

    public function customer() {
    	return $this->belongsTo('App\customer');
    }
}
