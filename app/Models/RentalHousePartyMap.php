<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalHousePartyMap extends Model
{
    protected $fillable = [
        'rental_house_id',
        'rental_party_id',
        'security_money',
        'remaining_security_money',
        'refund_security_money',
        'monthly_rent',
        'auto_adjustment',
        'payment_channel_id',
        'account_id',
        'rent_start_date',
        'status',
    ];

    public function rentalHouse()
    {
        return $this->belongsTo(RentalHouse::class, 'rental_house_id');
    }


    public function rentalParty()
    {
        return $this->belongsTo(RentalParty::class, 'rental_party_id');
    }
}
