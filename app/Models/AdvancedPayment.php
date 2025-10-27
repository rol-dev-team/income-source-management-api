<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedPayment extends Model
{
    protected $fillable = [
        'advanced_payment_type',
        'sub_cat_id',
        'point_of_contact_id',
        'amount',
        'auto_adjustment_amount',
        'remaining_amount',
    ];
}

