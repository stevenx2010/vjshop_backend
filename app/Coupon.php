<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public function customers() 
    {
    	return $this->belongsToMany('App\Customer')->withPivot('quantity')->withTimestamps();
    }

    public function orders()
    {
    	return $this->belongsToMany('App\Order')->withTimestamps();
    }

    public function couponType()
    {
    	return $this->belongsTo('App\couponType');
    }
}
