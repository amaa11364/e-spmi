<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumenFolder;
use App\Models\DokumenFile;
use App\Models\Iku;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrasiDokumenAkreditasiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Pengunggah Default
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@ikipsiliwangi.ac.id'],
            ['name' => 'Admin LPMI', 'password' => bcrypt('password')]
        );

        // 2. Buat Unit Kerja Default
        $unitLpmi = UnitKerja::firstOrCreate(
            ['kode' => 'LPMI'],
            ['nama' => 'Lembaga Penjaminan Mutu Internal', 'status' => true]
        );

        // Path absolut folder fisik
        $basePath = storage_path('app/public/dokumen-akreditasi');

        if (!File::isDirectory($basePath)) {
            $this->command->error("Folder 'storage/app/public/dokumen-akreditasi' tidak ditemukan!");
            return;
        }

        $this->command->info("Memulai pembuatan data IKU dan pemetaan dokumen...");

        // 3. Buat Root Folder Utama
        $folderUtama = DokumenFolder::firstOrCreate([
            'nama' => 'Dokumen Akreditasi IKU',
            'parent_id' => null
        ], [
            'deskripsi' => 'Kumpulan folder dokumen kriteria IKU',
            'is_public' => true,
        ]);

        // Ambil Folder Kriteria (contoh: C1 - Visi Misi, C2 - Tata Pamong, dll)
        $kriteriaDirs = File::directories($basePath);

        foreach ($kriteriaDirs as $kriteriaDirPath) {
            $kriteriaDirName = basename($kriteriaDirPath); // misal: "C1 - Visi Misi"

            // A. Buat Master Data IKU berdasarkan Nama Folder Kriteria
            $kodeIku = explode(' ', $kriteriaDirName)[0]; // Mengambil kode "C1"
            $iku = Iku::firstOrCreate([
                'kode' => $kodeIku,
            ], [
                'nama' => $kriteriaDirName,
                'deskripsi' => 'Indikator Kinerja Utama ' . $kriteriaDirName,
                'status' => true,
            ]);

            // B. Buat Folder Level IKU di DokumenFolder
            $folderIku = DokumenFolder::firstOrCreate([
                'nama' => $kriteriaDirName,
                'parent_id' => $folderUtama->id,
            ], [
                'deskripsi' => 'Folder dokumen ' . $kriteriaDirName,
                'is_public' => true,
            ]);

            // C. Ambil Sub-Folder / Kategori (misal: C.1.1 Latar Belakang)
            $subDirs = File::directories($kriteriaDirPath);

            foreach ($subDirs as $subDirPath) {
                $subDirName = basename($subDirPath); // misal: "C.1.1 Latar Belakang"

                $subFolder = DokumenFolder::firstOrCreate([
                    'nama' => $subDirName,
                    'parent_id' => $folderIku->id,
                ], [
                    'deskripsi' => 'Sub-kategori ' . $subDirName,
                    'is_public' => true,
                ]);

                // D. Simpan File ke Database
                $files = File::files($subDirPath);

                foreach ($files as $file) {
                    if (strtolower($file->getExtension()) === 'pdf') {
                        $fileName = $file->getFilename();
                        $fileTitle = pathinfo($fileName, PATHINFO_FILENAME);
                        $cleanTitle = Str::title(str_replace(['_', '-'], ' ', $fileTitle));

                        $relativePath = 'dokumen-akreditasi/' . $kriteriaDirName . '/' . $subDirName . '/' . $fileName;

                        DokumenFile::updateOrCreate([
                            'file_path' => $relativePath,
                        ], [
                            'dokumen_folder_id' => $subFolder->id,
                            'iku_id'            => $iku->id,           // ID IKU Terkait
                            'unit_kerja_id'     => $unitLpmi->id,      // Unit Kerja
                            'user_id'           => $userAdmin->id,     // Uploaded by
                            'nama'              => $cleanTitle,
                            'deskripsi'         => 'Dokumen ' . $cleanTitle . ' pada kategori ' . $subDirName,
                            'file_type'         => 'application/pdf',
                            'file_size'         => $file->getSize(),
                            'is_public'         => true,
                        ]);
                    }
                }
            }
        }

        $this->command->info("SELESAI! Master IKU, Unit Kerja, Folder, dan File berhasil dimigrasikan.");
    }
}