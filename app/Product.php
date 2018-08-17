<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function productImages() {
    	return $this->hasMany('App\ProductImage');
    }
}
