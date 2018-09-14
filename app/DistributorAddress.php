<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributorAddress extends Model
{
	protected $fillable = [ 'city', 'street', 'default_address', 'distributor_id'];
	
    public function distributor() 
    {
    	return $this->belongsTo('App\Distributor');
    }
}
