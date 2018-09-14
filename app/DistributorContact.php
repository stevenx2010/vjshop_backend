<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributorContact extends Model
{
	protected $fillable = ['name', 'mobile', 'telephone', 'phone_area_code', 'default_contact', 'distributor_id'];
	
    public function distributor()
    {
    	return $this->belongsTo('App\Distributor');
    }
}
