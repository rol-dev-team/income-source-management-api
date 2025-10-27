<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSchedule extends Model
{
    protected $table = 'loan_schedules';
    protected $fillable = [
        'loan_id',
        'installment_no',
        'amount',
        'interest_rate',
        'start_date',
        'end_date',
        'status',
    ];
}
