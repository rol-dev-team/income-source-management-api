<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceSubcategory extends Model
{
    protected $table = 'source_subcategories';

    protected $fillable = [
        'source_id',
        'subcat_name',
    ];
}
