<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DokumenFolder;
use App\Models\DokumenFile;
use App\Models\Iku;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportDokumenFolder extends Command
{
    protected $signature = 'dokumen:import {json_path : Path file JSON pemetaan}';
    protected $description = 'Import dan kelompokkan dokumen akreditasi secara otomatis berdasarkan data JSON';

    public function handle()
    {
        $jsonPath = $this->argument('json_path');

        if (!File::exists($jsonPath)) {
            $this->error("File JSON tidak ditemukan di lokasi: " . $jsonPath);
            return;
        }

        $jsonData = json_decode(File::get($jsonPath), true);
        if (!$jsonData) {
            $this->error("Format JSON tidak valid!");
            return;
        }

        $this->info("Memulai impor otomatis berdasarkan metadata: " . $jsonData['metadata']['judul']);

        $admin = User::first() ?? User::factory()->create();
        $unit  = UnitKerja::firstOrCreate(['kode' => 'LPMI'], ['nama' => 'LPMI', 'status' => true]);

        $totalFile = 0;

        // Loop Kriteria C1 - C9 dari JSON
        foreach ($jsonData['kriteria'] as $kriteria) {
            $kodeIku = $kriteria['kode'];
            $namaIku = $kriteria['nama'];

            // 1. Buat Data IKU
            $iku = Iku::firstOrCreate(
                ['kode' => $kodeIku],
                ['nama' => $namaIku, 'deskripsi' => $kriteria['deskripsi'] ?? $namaIku, 'status' => true]
            );

            // 2. Buat Folder Main Kriteria
            $folderIku = DokumenFolder::firstOrCreate([
                'nama' => "Kriteria {$kodeIku} - {$namaIku}",
                'parent_id' => null
            ]);

            // 3. Loop Sub Kriteria (C.1.1, C.1.2, dst)
            if (isset($kriteria['sub_kriteria'])) {
                foreach ($kriteria['sub_kriteria'] as $sub) {
                    $folderSub = DokumenFolder::firstOrCreate([
                        'nama' => "{$sub['kode']} {$sub['nama']}",
                        'parent_id' => $folderIku->id
                    ]);

                    // 4. Loop Dokumen di dalam Sub Kriteria
                    foreach ($sub['dokumen'] as $doc) {
                        $filesToProcess = [];

                        if (isset($doc['versi'])) {
                            foreach ($doc['versi'] as $v) {
                                // Penanganan dinamis untuk key 'tahun' atau 'unit' pada versi
                                $label = isset($v['tahun']) ? " ({$v['tahun']})" : (isset($v['unit']) ? " ({$v['unit']})" : "");

                                $filesToProcess[] = [
                                    'judul' => $doc['judul'] . $label, 
                                    'file'  => $v['file']
                                ];
                            }
                        } elseif (isset($doc['file'])) {
                            $filesToProcess[] = [
                                'judul' => $doc['judul'], 
                                'file'  => $doc['file']
                            ];
                        }

                        foreach ($filesToProcess as $item) {
                            $fileName = $item['file'];
                            
                            // Cari lokasi file asli di disk
                            $possiblePath = storage_path("app/public/dokumen-akreditasi/2024-08/{$fileName}");

                            if (File::exists($possiblePath)) {
                                $storagePath = 'dokumen_akreditasi/' . $fileName;
                                Storage::disk('public')->put($storagePath, File::get($possiblePath));

                                DokumenFile::updateOrCreate([
                                    'file_path' => $storagePath,
                                ], [
                                    'dokumen_folder_id' => $folderSub->id,
                                    'iku_id'            => $iku->id,
                                    'unit_kerja_id'     => $unit->id,
                                    'user_id'           => $admin->id,
                                    'nama'              => $item['judul'],
                                    'file_type'         => 'application/pdf',
                                    'file_size'         => File::size($possiblePath),
                                    'is_public'         => true,
                                ]);

                                $this->info("[{$kodeIku}] Berhasil mengelompokkan: {$item['judul']}");
                                $totalFile++;
                            }
                        }
                    }
                }
            }
        }

        $this->info("SELESAI! Total {$totalFile} file berhasil dirapikan dan dikelompokkan otomatis.");
    }
}