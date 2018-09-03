<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributorContact extends Model
{
    public function distributor()
    {
    	return $this->belongsTo('App\Distributor');
    }
}
