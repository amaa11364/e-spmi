<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitKerjaController extends Controller
{
    /**
     * Get all unit kerja with pagination, search, and filter
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = UnitKerja::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        $unitKerjas = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $unitKerjas
        ]);
    }

    /**
     * Create new unit kerja
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:50|unique:unit_kerjas,kode',
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $unitKerja = UnitKerja::create($request->only(['kode', 'nama', 'deskripsi', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Unit kerja berhasil ditambahkan',
                'data' => $unitKerja
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update unit kerja
     */
    public function update(Request $request, $id)
    {
        try {
            $unitKerja = UnitKerja::find($id);

            if (!$unitKerja) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit kerja tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:50|unique:unit_kerjas,kode,' . $id,
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'status' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $unitKerja->update($request->only(['kode', 'nama', 'deskripsi', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Unit kerja berhasil diperbarui',
                'data' => $unitKerja
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete unit kerja
     */
    public function destroy($id)
    {
        try {
            $unitKerja = UnitKerja::find($id);

            if (!$unitKerja) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit kerja tidak ditemukan'
                ], 404);
            }

            $unitKerja->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit kerja berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }
}
