<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalParty extends Model
{
    protected $fillable = [
        'party_name',
        'cell_number',
        'nid',
        'party_ac_no',
    ];


    public function rentalPostings()
    {
        return $this->hasMany(RentalPosting::class, 'head_id');
    }

    // Also add relationship to house mappings if needed
    public function rentalHousePartyMaps()
    {
        return $this->hasMany(RentalHousePartyMap::class, 'rental_party_id');
    }
}
