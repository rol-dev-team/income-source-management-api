<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyPosting extends Model
{
    use HasFactory;

    protected $table = 'currency_postings';

    protected $fillable = [
        'business_type_id',
        'transaction_type',
        'currency_id',
        'currency_party_id',
        'payment_channel_id',
        'account_id',
        'party_account_number',
        'currency_amount',
        'exchange_rate',
        'amount_bdt',
        'posting_date',
        'note',
        'rejected_note',
        'status',
    ];

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function currencyParty()
    {
        return $this->belongsTo(CurrencyParty::class);
    }

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }

    public function paymentChannelDetails()
    {
        return $this->belongsTo(PaymentChannelDetails::class, 'payment_channel_id');
    }
}
