<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('dokumen_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_folder_id')->constrained('dokumen_folders')->cascadeOnDelete();
            
            // Hapus pengetatan relasi (constrained) agar tidak error saat migrate
            $table->unsignedBigInteger('iku_id')->nullable();
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dokumen_files');
    }
};