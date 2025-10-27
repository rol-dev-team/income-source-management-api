<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCurrentBalance extends Model
{
    protected $table = 'account_current_balances';

    protected $fillable = [
        'account_id',
        'balance',
    ];

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class, 'account_id');
    }
}
