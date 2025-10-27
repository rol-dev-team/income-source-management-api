<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalHouse extends Model
{
    protected $fillable = [
        'house_name',
        'address',
    ];
}
