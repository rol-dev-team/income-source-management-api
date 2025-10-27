<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentPosting extends Model
{
    protected $table = 'investment_postings';

    protected $fillable = [
        'transaction_type',
        'head_id',
        'payment_channel_id',
        'account_id',
        'receipt_number',
        'amount_bdt',
        'posting_date',
        'note',
        'investment_id',
        'rejected_note',
        'status',
        'entry_type',
    ];

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class, 'investment_id');
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
