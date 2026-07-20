<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\HeroContent;
use App\Models\Berita;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin SPMI',
            'email' => 'admin@qtrack.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create regular user
        User::create([
            'name' => 'User SPMI',
            'email' => 'user@qtrack.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create Hero Content
        HeroContent::create([
            'title' => 'Sistem Penjaminan Mutu Internal Digital',
            'subtitle' => 'Transformasi Digital SPMI IKIP',
            'description' => 'Platform terintegrasi untuk memonitor, mengelola, dan meningkatkan mutu pendidikan secara digital.',
            'cta_text' => 'Mulai Sekarang',
            'cta_link' => '#features',
            'is_active' => true,
        ]);

        // Create sample berita
        Berita::create([
            'judul' => 'SPMI Luncurkan Aplikasi Mobile untuk Monitoring Mutu',
            'deskripsi' => 'Aplikasi mobile ini memudahkan monitoring dan evaluasi mutu pendidikan secara real-time.',
            'link' => '#',
            'is_published' => true,
        ]);

        Berita::create([
            'judul' => 'Audit Internal SPMI Semester Ganjil 2024/2025',
            'deskripsi' => 'Audit internal akan dilaksanakan pada bulan Januari 2024 untuk semua program studi.',
            'link' => '#',
            'is_published' => true,
        ]);

        // Create sample jadwal
        Jadwal::create([
            'kegiatan' => 'Rapat Koordinasi SPMI',
            'deskripsi' => 'Rapat koordinasi untuk persiapan audit internal semester ganjil',
            'tanggal' => now()->addDays(5),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '12:00',
            'tempat' => 'Ruang Rapat Utama',
            'penyelenggara' => 'LPM',
            'kategori' => 'Rapat',
            'is_active' => true,
        ]);

        Jadwal::create([
            'kegiatan' => 'Workshop Penyusunan Dokumen Mutu',
            'deskripsi' => 'Workshop untuk penyusunan dan revisi dokumen mutu program studi',
            'tanggal' => now()->addDays(10),
            'waktu_mulai' => '13:00',
            'waktu_selesai' => '16:00',
            'tempat' => 'Aula Kampus',
            'penyelenggara' => 'LPPM',
            'kategori' => 'Workshop',
            'is_active' => true,
        ]);
    }
}