<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponType extends Model
{
    public function productSubCategories() 
    {
    	return $this->belongsToMany('App\ProductSubCategory')->withTimestamps();
    }

    public function coupons()
    {
    	return $this->hasMany('App\Coupon');
    }
}
