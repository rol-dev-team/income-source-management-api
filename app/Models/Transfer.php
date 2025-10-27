<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfers';

    protected $fillable = [
        'transfer_id',
        'transaction_type',
        'payment_channel_id',
        'account_id',
        'amount_bdt',
        'transfer_date',
        'note',
    ];

    // Add this relationship
    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }

    // Add payment channel relationship if needed
    public function paymentChannelDetails()
    {
        return $this->belongsTo(PaymentChannelDetails::class, 'payment_channel_id');
    }
}
