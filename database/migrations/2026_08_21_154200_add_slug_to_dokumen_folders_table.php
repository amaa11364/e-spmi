<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\DokumenFolder;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_folders', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nama');
        });

        // Generate slugs for existing folders
        $folders = DokumenFolder::all();
        foreach ($folders as $folder) {
            $baseSlug = Str::slug($folder->nama);
            if (empty($baseSlug)) {
                $baseSlug = 'folder';
            }
            $slug = $baseSlug;
            $counter = 1;
            while (DokumenFolder::where('slug', $slug)->where('id', '!=', $folder->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $folder->slug = $slug;
            $folder->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('dokumen_folders', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
