<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    public function productCategories() {
    	return $this->belongsTo('App\productCategory');
    }
}
