<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Comment;

class Order extends Model
{
	protected $fillable = [	'order_serial',
							'customer_id',
							'distributor_id',
							'total_price',
							'total_weight',
							'order_date',
							'delivery_date',
							'delivery_confirm_date',
							'delivery_status',
							'payment_method',
							'shipping_address_id',
							'order_status',
							'is_invoice_required',
							'invoice_status',
							'invoice_head',
							'invoice_tax_number',
							'invoice_type'
						  ];

    public function comments() {
    	return $this->hasMany('App\Comment');
    }

    public function products() {
    	return $this->belongsToMany('App\Product')->withPivot('price')->withPivot('quantity')->withTimestamps();
    }

    public function coupons() {
    	return $this->belongsToMany('App\Coupon')->withTimestamps();
    }
}
