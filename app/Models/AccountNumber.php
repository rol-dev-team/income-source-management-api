<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountNumber extends Model
{
     protected $table = 'account_numbers';
        protected $fillable = [
            'channel_detail_id',
            'ac_no',
            'ac_name',
            'ac_details',
        ];
}
