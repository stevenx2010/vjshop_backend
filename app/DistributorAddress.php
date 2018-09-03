<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributorAddress extends Model
{
    public function distributor() 
    {
    	return $this->belongsTo('App\Distributor');
    }
}
