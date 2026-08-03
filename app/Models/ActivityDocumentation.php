<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDocumentation extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'activity_date',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'activity_date' => 'date'
    ];
}
