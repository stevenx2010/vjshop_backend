<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    public function customer() 
    {
    	$this->belongsTo('customers');
    }
}
