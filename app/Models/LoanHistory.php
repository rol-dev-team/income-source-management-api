<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanHistory extends Model
{
    protected $table = 'loan_histories';

    protected $fillable = [
        'posting_id',
        'installment_number',
        'amount',
        'interest_rate',
        'term_months',
        'start_date',
        'end_date',
    ];
}
