<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDeposit extends Model
{
    protected $table = 'bank_deposits';
    protected $fillable = [
        'transaction_type',
        'payment_channel_id',
        'account_id',
        'amount_bdt',
        'posting_date',
        'note',
        'rejected_note',
        'status',
    ];

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }

    public function paymentChannelDetails()
    {
        return $this->belongsTo(PaymentChannelDetails::class, 'payment_channel_id');
    }
}
