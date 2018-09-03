<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public function customers() 
    {
    	return $this->belongsToMany('App\Customer')->withPivot('quantity')->withTimestamps();
    }
}
