<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['name', 'description', 'coupon_type_id',
            'expire_date', 'expired', 'discount_method', 'discount_percentage',
            'discount_value', 'quantity_initial', 'quantity_available', 'for_new_comer',
            'image_url', 'min_purchased_amount'];

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
