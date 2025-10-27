<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentParty extends Model
{
    protected $table = 'investment_parties';

    protected $fillable = [
        'party_name',
    ];
}
