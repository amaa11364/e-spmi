<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DokumenFolder;
use App\Models\DokumenFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportDokumenFolder extends Command
{
    protected $signature = 'dokumen:import {path : Path folder di komputer}';
    protected $description = 'Otomatis membuat folder dan memasukkan file PDF ke database';

    public function handle()
    {
        $sourcePath = $this->argument('path');

        if (!File::isDirectory($sourcePath)) {
            $this->error("Folder tidak ditemukan di lokasi: " . $sourcePath);
            return;
        }

        $this->info("Memulai proses impor dari folder: " . $sourcePath);
        $this->processDirectory($sourcePath, null);
        $this->info("SELESAI! Semua folder dan file PDF berhasil dimigrasikan.");
    }

    private function processDirectory($dirPath, $parentId = null)
    {
        $items = File::files($dirPath);
        $directories = File::directories($dirPath);

        foreach ($items as $file) {
            if (strtolower($file->getExtension()) === 'pdf') {
                $fileName = $file->getFilename();
                $fileTitle = pathinfo($fileName, PATHINFO_FILENAME);

                $storagePath = 'dokumen_akreditasi/' . uniqid() . '_' . $fileName;
                Storage::disk('public')->put($storagePath, File::get($file->getPathname()));

                DokumenFile::create([
                    'dokumen_folder_id' => $parentId,
                    'nama'              => str_replace(['_', '-'], ' ', $fileTitle),
                    'file_path'         => $storagePath,
                    'file_type'         => 'application/pdf',
                    'file_size'         => $file->getSize(),
                    'is_public'         => true,
                ]);

                $this->info(" -> File diimpor: " . $fileName);
            }
        }

        foreach ($directories as $subDir) {
            $folderName = basename($subDir);

            $newFolder = DokumenFolder::create([
                'nama'      => str_replace(['_', '-'], ' ', $folderName),
                'parent_id' => $parentId,
                'is_public' => true,
            ]);

            $this->info("[FOLDER] Membuat folder: " . $folderName);

            $this->processDirectory($subDir, $newFolder->id);
        }
    }
}