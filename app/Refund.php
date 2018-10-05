<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['order_id', 'refund_reason', 'refund_status',
    'refund_amount', 'refund_date', 'approved_by', 'audited_by'];
}
