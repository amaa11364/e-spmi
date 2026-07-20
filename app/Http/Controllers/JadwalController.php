<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Get all jadwal with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', 'upcoming'); // upcoming, past, all
        
        $query = Jadwal::query();
        
        if ($search) {
            $query->where('kegiatan', 'LIKE', "%{$search}%")
                  ->orWhere('tempat', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
        }

        // Filter by status
        $now = Carbon::now();
        if ($status === 'upcoming') {
            $query->where('tanggal', '>=', $now->toDateString());
        } elseif ($status === 'past') {
            $query->where('tanggal', '<', $now->toDateString());
        }

        $jadwals = $query->orderBy('tanggal', 'asc')
                         ->orderBy('waktu_mulai', 'asc')
                         ->paginate($perPage);

        // Format date for display
        $jadwals->getCollection()->transform(function ($item) {
            $item->hari = Carbon::parse($item->tanggal)->format('d');
            $item->bulanSingkat = Carbon::parse($item->tanggal)->format('M');
            $item->bulanPanjang = Carbon::parse($item->tanggal)->format('F');
            $item->tanggal_formatted = Carbon::parse($item->tanggal)->format('d F Y');
            $item->waktu = $item->waktu_mulai ? Carbon::parse($item->waktu_mulai)->format('H:i') : null;
            $item->waktu_selesai_formatted = $item->waktu_selesai ? Carbon::parse($item->waktu_selesai)->format('H:i') : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $jadwals
        ]);
    }

    /**
     * Get upcoming jadwal for landing page (limit 5)
     */
    public function upcoming()
    {
        $now = Carbon::now();
        
        $jadwals = Jadwal::where('tanggal', '>=', $now->toDateString())
                         ->orderBy('tanggal', 'asc')
                         ->orderBy('waktu_mulai', 'asc')
                         ->limit(5)
                         ->get();

        $jadwals->transform(function ($item) {
            $item->hari = Carbon::parse($item->tanggal)->format('d');
            $item->bulanSingkat = Carbon::parse($item->tanggal)->format('M');
            $item->bulanPanjang = Carbon::parse($item->tanggal)->format('F');
            $item->tanggal_formatted = Carbon::parse($item->tanggal)->format('d F Y');
            $item->waktu = $item->waktu_mulai ? Carbon::parse($item->waktu_mulai)->format('H:i') : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $jadwals
        ]);
    }

    /**
     * Get single jadwal
     */
    public function show($id)
    {
        $jadwal = Jadwal::find($id);
        
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal not found'
            ], 404);
        }

        $jadwal->hari = Carbon::parse($jadwal->tanggal)->format('d');
        $jadwal->bulanSingkat = Carbon::parse($jadwal->tanggal)->format('M');
        $jadwal->tanggal_formatted = Carbon::parse($jadwal->tanggal)->format('d F Y');
        $jadwal->waktu = $jadwal->waktu_mulai ? Carbon::parse($jadwal->waktu_mulai)->format('H:i') : null;

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    /**
     * Create new jadwal
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'tempat' => 'nullable|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['is_active'] = $request->get('is_active', true);

        $jadwal = Jadwal::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal created successfully',
            'data' => $jadwal
        ], 201);
    }

    /**
     * Update jadwal
     */
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::find($id);
        
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'tempat' => 'nullable|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $jadwal->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal updated successfully',
            'data' => $jadwal
        ]);
    }

    /**
     * Delete jadwal
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::find($id);
        
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal not found'
            ], 404);
        }

        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal deleted successfully'
        ]);
    }

    /**
     * Get jadwal by date range
     */
    public function getByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jadwals = Jadwal::whereBetween('tanggal', [
                $request->start_date, 
                $request->end_date
            ])
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $jadwals->transform(function ($item) {
            $item->hari = Carbon::parse($item->tanggal)->format('d');
            $item->bulanSingkat = Carbon::parse($item->tanggal)->format('M');
            $item->tanggal_formatted = Carbon::parse($item->tanggal)->format('d F Y');
            $item->waktu = $item->waktu_mulai ? Carbon::parse($item->waktu_mulai)->format('H:i') : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $jadwals
        ]);
    }
}