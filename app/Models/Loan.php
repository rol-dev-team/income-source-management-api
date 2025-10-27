<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $table = 'loans';
    protected $fillable = [
        'principal_amount',
        'extra_charge',
        'term_in_month',
        'loan_start_date',
        'installment_date',
        'status',
    ];
    public function postings(): HasMany
    {
        return $this->hasMany(LoanPosting::class, 'loan_id');
    }

    /**
     * Get the interest rates for the loan.
     */
    public function interestRates(): HasMany
    {
        return $this->hasMany(LoanInterestRate::class, 'loan_id');
    }
    public function loanPayments()
    {
        // return $this->hasMany(LoanPosting::class, 'loan_id')->where('entry_type', 'loan_payment');
        return $this->hasMany(LoanPosting::class, 'loan_id')
            ->whereIn('entry_type', ['loan_payment', 'loan_received']);
    }
}
