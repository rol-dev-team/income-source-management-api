<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPosting extends Model
{
    protected $fillable = [
        'transaction_type',
        'head_id',
        'house_id',
        'payment_channel_id',
        'account_id',
        'receipt_number',
        'amount_bdt',
        'posting_date',
        'note',
        'rejected_note',
        'status',
        'entry_type',
    ];


    public function rentalParty()
    {
        return $this->belongsTo(RentalParty::class, 'head_id');
    }

    // Relationships
    // public function rentalHouse()
    // {
    //     return $this->belongsTo(RentalHouse::class);
    // }

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }

    public function paymentChannelDetails()
    {
        return $this->belongsTo(PaymentChannelDetails::class, 'payment_channel_id');
    }
}
