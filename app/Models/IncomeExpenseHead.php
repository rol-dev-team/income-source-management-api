<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeExpenseHead extends Model
{
    protected $table = 'income_expense_heads';

    protected $fillable = [
        'type',
        'head_name',
    ];
}
