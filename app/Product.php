<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function productImages() {
    	$this->hasMany('App\ProductImage');
    }
}
