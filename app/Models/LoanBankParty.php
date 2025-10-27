<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanBankParty extends Model
{
    protected $table = 'loan_bank_parties';

    protected $fillable = [
        'type',
        'party_name',
    ];
}
