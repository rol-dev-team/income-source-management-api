<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannelDetails extends Model
{
    protected $table = 'payment_channel_details';

    protected $fillable = [
        'channel_id',
        'method_name',
        'ac_no',
    ];
}
