<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenFile extends Model
{
    protected $fillable = [
        'dokumen_folder_id',
        'iku_id',
        'unit_kerja_id',
        'user_id',
        'nama',
        'deskripsi',
        'file_path',
        'file_type',
        'file_size',
        'is_public'
    ];

    public function folder() { return $this->belongsTo(DokumenFolder::class, 'dokumen_folder_id'); }
    public function iku() { return $this->belongsTo(Iku::class); }
    public function unitKerja() { return $this->belongsTo(UnitKerja::class); }
    public function uploader() { return $this->belongsTo(User::class, 'user_id'); }
}