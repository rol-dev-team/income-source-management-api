<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investment extends Model
{
    protected $table = 'investments';
    protected $fillable = [
        'principal_amount',
        'investment_start_date',
        'status',
    ];

    public function postings(): HasMany
    {
        return $this->hasMany(InvestmentPosting::class, 'investment_id');
    }

    public function investmentReturnProfit(): HasMany
    {
        return $this->hasMany(InvestmentPosting::class, 'investment_id')
            ->whereIn('entry_type', ['investment_return', 'investment_profit']);
    }
}
