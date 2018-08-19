<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\ProductCategory;

class ProductSubCategory extends Model
{
    public function productCategories() {
    	return $this->belongsTo('App\ProductCategory');
    }

    public function products() {
    	return $this->hasMany('App\Product');
    }
}
