<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['description', 'type', 'setting_name', 'setting_value', 'setting_value_postfix', 'parameter1', 'parameter2', 'condition1','condition2'];
}
