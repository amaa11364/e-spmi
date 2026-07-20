<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Jadwal;
use App\Models\HeroContent;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $totalBerita = Berita::count();
        $publishedBerita = Berita::where('is_published', true)->count();
        
        $totalJadwal = Jadwal::count();
        $upcomingJadwal = Jadwal::where('tanggal', '>=', Carbon::now()->toDateString())->count();
        
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'berita' => [
                    'total' => $totalBerita,
                    'published' => $publishedBerita,
                    'draft' => $totalBerita - $publishedBerita
                ],
                'jadwal' => [
                    'total' => $totalJadwal,
                    'upcoming' => $upcomingJadwal,
                    'past' => $totalJadwal - $upcomingJadwal
                ],
                'users' => [
                    'total' => $totalUsers,
                    'admin' => $adminUsers,
                    'regular' => $totalUsers - $adminUsers
                ],
                'hero' => [
                    'has_content' => HeroContent::exists(),
                    'has_image' => HeroContent::whereNotNull('background_image')->exists()
                ]
            ]
        ]);
    }

    /**
     * Get recent activity
     */
    public function recentActivity()
    {
        $recentBerita = Berita::orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function ($item) {
                                  return [
                                      'type' => 'berita',
                                      'title' => $item->judul,
                                      'created_at' => $item->created_at,
                                      'time_ago' => $item->created_at->diffForHumans()
                                  ];
                              });

        $recentJadwal = Jadwal::orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function ($item) {
                                  return [
                                      'type' => 'jadwal',
                                      'title' => $item->kegiatan,
                                      'created_at' => $item->created_at,
                                      'time_ago' => $item->created_at->diffForHumans()
                                  ];
                              });

        $recentUsers = User::orderBy('created_at', 'desc')
                           ->limit(5)
                           ->get()
                           ->map(function ($item) {
                               return [
                                   'type' => 'user',
                                   'title' => $item->name,
                                   'email' => $item->email,
                                   'created_at' => $item->created_at,
                                   'time_ago' => $item->created_at->diffForHumans()
                               ];
                           });

        // Combine and sort by created_at
        $activities = collect()
            ->merge($recentBerita)
            ->merge($recentJadwal)
            ->merge($recentUsers)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Get chart data for dashboard
     */
    public function chartData()
    {
        $months = collect(range(1, 6))->map(function ($month) {
            $date = Carbon::now()->subMonths(6 - $month);
            return $date->format('M');
        });

        // Berita per month (last 6 months)
        $beritaData = collect(range(1, 6))->map(function ($month) {
            $date = Carbon::now()->subMonths(6 - $month);
            return Berita::whereMonth('created_at', $date->month)
                         ->whereYear('created_at', $date->year)
                         ->count();
        });

        // Jadwal per month (last 6 months)
        $jadwalData = collect(range(1, 6))->map(function ($month) {
            $date = Carbon::now()->subMonths(6 - $month);
            return Jadwal::whereMonth('created_at', $date->month)
                         ->whereYear('created_at', $date->year)
                         ->count();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $months,
                'berita' => $beritaData,
                'jadwal' => $jadwalData
            ]
        ]);
    }
}