<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    public function productSubCategories() {
    	return $this->hasMany('App\productSubCategory');
    }
}
