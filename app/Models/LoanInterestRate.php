<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanInterestRate extends Model
{

    protected $table = 'loan_interest_rates';

    protected $fillable = [
        'loan_id',
        'interest_rate',
        'effective_date',
        'end_date',
    ];
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
