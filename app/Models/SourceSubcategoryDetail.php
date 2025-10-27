<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceSubcategoryDetail extends Model
{
    protected $table = 'source_subcategory_details';

    protected $fillable = [
        'source_id',
        'source_cat_id',
        'source_subcat_id',
        'point_of_contact_id',
        'status',
    ];
}
