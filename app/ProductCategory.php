<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductSubCategory;
use App\Product;

class ProductCategory extends Model
{
    public function productSubCategories() {
    	return $this->hasMany('App\ProductSubCategory');
    }

    public function products() {
    	return $this->hasManyThrough('App\Product', 'App\ProductSubCategory');
    }
}
