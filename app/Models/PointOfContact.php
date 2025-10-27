<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointOfContact extends Model
{
    protected $table = 'point_of_contacts';
    protected $fillable = [
        'contact_name',
        'contact_no',
        'nid',
        'address',
    ];
}
