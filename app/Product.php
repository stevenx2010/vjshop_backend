<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductImage;
use App\ProductSubCategory;

class Product extends Model
{
	protected $fillable = ['product_sub_category_id', 'product_sub_category_name', 'name', 'model', 'description', 'package_unit', 'weight', 'weight_unit', 'price', 'brand', 'inventory', 'sort_order','thumbnail_url'];

    public function productImages() {
    	return $this->hasMany('App\ProductImage');
    }

    public function productSubCategories() {
    	return $this->belongsTo('App\ProductSubCategory');
    }

    public function distributors() {
    	return $this->belongsToMany('App\Distributor')->withPivot('inventory')->withTimestamps();
    }

    public function orders() {
        return $this->belongsToMany('App\Order')->withPivot('quantity')->withPivot('subtotal_price')->withTimestamps();
    }
}
