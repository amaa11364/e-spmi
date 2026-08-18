<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumenFolder;
use App\Models\DokumenFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrasiDokumenAkreditasiSeeder extends Seeder
{
    public function run(): void
    {
        // Path absolut folder fisik
        $basePath = storage_path('app/public/dokumen-akreditasi');

        if (!File::isDirectory($basePath)) {
            $this->command->error("Folder 'storage/app/public/dokumen-akreditasi' tidak ditemukan!");
            return;
        }

        $this->command->info("Memulai migrasi & perbaikan link akses file PDF...");

        // 1. Buat/Dapatkan Folder Utama
        $folderUtama = DokumenFolder::firstOrCreate([
            'nama' => 'dokumen-akreditasi'
        ], [
            'deskripsi' => 'Folder utama dokumen akreditasi',
            'is_public' => true,
            'parent_id' => null,
        ]);

        $subFolders = File::directories($basePath);

        foreach ($subFolders as $subDirPath) {
            $folderName = basename($subDirPath); // e.g., '2024-08' atau '2025-02'

            // 2. Buat Sub-Folder Tahun
            $folderTahun = DokumenFolder::firstOrCreate([
                'nama' => $folderName,
                'parent_id' => $folderUtama->id,
            ], [
                'deskripsi' => 'Dokumen versi ' . $folderName,
                'is_public' => true,
            ]);

            $files = File::files($subDirPath);

            foreach ($files as $file) {
                if (strtolower($file->getExtension()) === 'pdf') {
                    $fileName = $file->getFilename();
                    $fileTitle = pathinfo($fileName, PATHINFO_FILENAME);

                    // Path relatif yang dikenali oleh Storage::url()
                    $relativePath = 'dokumen-akreditasi/' . $folderName . '/' . $fileName;

                    DokumenFile::updateOrCreate([
                        'dokumen_folder_id' => $folderTahun->id,
                        'nama'              => str_replace(['_', '-'], ' ', $fileTitle),
                    ], [
                        'file_path'         => $relativePath,
                        'file_type'         => 'application/pdf',
                        'file_size'         => $file->getSize(),
                        'is_public'         => true,
                    ]);
                }
            }
        }

        $this->command->info("SELESAI! Seluruh file PDF berhasil dihubungkan dan siap diakses.");
    }
}