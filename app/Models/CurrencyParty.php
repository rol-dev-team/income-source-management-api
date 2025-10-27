<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyParty extends Model
{
    protected $table = 'currency_parties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_type_id',
        'party_name',
        'mobile',
        'nid',
        'email',
        'address',
    ];
}
