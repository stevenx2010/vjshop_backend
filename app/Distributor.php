<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\DistributorAddress;

class Distributor extends Model
{
    public function addresses() 
    {
    	return $this->hasMany('App\DistributorAddress');
    }

    public function contacts()
    {
    	return $this->hasMany('App\DistributorContact');
    }

    public function inventories()
    {
    	return $this->hasMany('App\DistributorInventory');
    }

    public function products()
    {
    	return $this->belongsToMany('App\Product')->withPivot('inventory')->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
