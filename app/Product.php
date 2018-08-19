<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductImage;
use App\ProductSubCategory;

class Product extends Model
{
    public function productImages() {
    	return $this->hasMany('App\ProductImage');
    }

    public function productSubCategories() {
    	return $this->belongsTo('App\ProductSubCategory');
    }
}
