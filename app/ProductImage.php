<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public function products{
    	$this->belongsTo('App\Product');
    }
}
