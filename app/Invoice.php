<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['order_id', 'invoice_number', 'invoice_amount', 'image_url', 'issued_by', 'approved_by', 'audited_by', 'issued_date'
		];
}
