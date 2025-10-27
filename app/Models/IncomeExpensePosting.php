<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeExpensePosting extends Model
{
    protected $table = 'income_expense_postings';
    protected $fillable = [
        'transaction_type',
        'head_id',
        'payment_channel_id',
        'account_id',
        'receipt_number',
        'amount_bdt',
        'posting_date',
        'note',
        'rejected_note',
        'status',
    ];


    public function incomeExpenseHead(): BelongsTo
    {
        return $this->belongsTo(IncomeExpenseHead::class);
    }



    /**
     * Get the payment channel associated with the posting.
     */
    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannelDetails::class);
    }

    /**
     * Get the account associated with the posting.
     */
    // public function account(): BelongsTo
    // {
    //     return $this->belongsTo(AccountNumber::class);
    // }

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
