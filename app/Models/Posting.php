<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    protected $table = 'postings';
    protected $fillable = [
        'posting_id',
        'source_id',
        'transaction_type_id',
        'source_cat_id',
        'source_subcat_id',
        'expense_type_id',
        'point_of_contact_id',
        'channel_detail_id',
        'recived_ac',
        'from_ac',
        'foreign_currency',
        'exchange_rate',
        'total_amount',
        'posting_date',
        'note',
        'rejected_note',
        'status',
    ];

}
