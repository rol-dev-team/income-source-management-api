<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceCategory extends Model
{
    protected $table = 'source_categories';

    protected $fillable = [
        'source_id',
        'cat_name'
    ];
}
