<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Customer;

class Comment extends Model
{
	protected $fillable = ['order_id', 'product_id', 'comment', 'comment_date', 'rating', 'prev_id', 'next_id', 'comment_owner'];

    public function order() {
    	return $this->belongsTo('App\Order');
    }

    public function product() {
    	return $this->belongsTo('App\product');
    }

}
