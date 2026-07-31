<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenFolder extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'is_public',
        'parent_id'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function files()
    {
        return $this->hasMany(DokumenFile::class);
    }

    public function children()
    {
        return $this->hasMany(DokumenFolder::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(DokumenFolder::class, 'parent_id');
    }

    public function publicFiles()
    {
        return $this->hasMany(DokumenFile::class)->where('is_public', true);
    }
}
