<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseDetails extends Model
{
    protected $table = 'expense_details';
    protected $fillable = [
        'posting_id',
        'channel_detail_id',
        'recived_ac',
        'from_ac',
        'amount',
        'exchange_rate',
        'expense_date',
    ];
}
