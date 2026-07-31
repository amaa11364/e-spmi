<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenFile extends Model
{
    protected $fillable = [
        'dokumen_folder_id',
        'nama',
        'file_path',
        'file_type',
        'file_size',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected $appends = ['file_url'];

    public function folder()
    {
        return $this->belongsTo(DokumenFolder::class, 'dokumen_folder_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
