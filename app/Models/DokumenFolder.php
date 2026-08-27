<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DokumenFolder extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'is_public',
        'parent_id'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected $appends = ['full_slug_path'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($folder) {
            $folder->slug = static::generateUniqueSlug($folder->nama);
        });

        static::updating(function ($folder) {
            if ($folder->isDirty('nama')) {
                $folder->slug = static::generateUniqueSlug($folder->nama, $folder->id);
            }
        });
    }

    /**
     * Generate a unique slug from the given name.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'folder';
        }
        $slug = $baseSlug;
        $counter = 1;
        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }

    /**
     * Build the full slug path from root to this folder.
     * Example: "folder-utama/sub-folder/sub-sub-folder"
     */
    public function getFullSlugPathAttribute(): string
    {
        $segments = [];
        $current = $this;
        while ($current) {
            array_unshift($segments, $current->slug);
            $current = $current->parent;
        }
        return implode('/', $segments);
    }

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
