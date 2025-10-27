<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPosting extends Model
{
    protected $table = 'loan_postings';
    protected $fillable = [
        'transaction_type',
        'head_type',
        'head_id',
        'payment_channel_id',
        'account_id',
        'receipt_number',
        'amount_bdt',
        'posting_date',
        'note',
        'rejected_note',
        'status',
        'loan_id',
        'interest_rate_id',
        'entry_type',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }

    public function paymentChannelDetails()
    {
        return $this->belongsTo(PaymentChannelDetails::class, 'payment_channel_id');
    }

    public function loanBankParty()
    {
        return $this->belongsTo(LoanBankParty::class, 'head_id');
    }
}
