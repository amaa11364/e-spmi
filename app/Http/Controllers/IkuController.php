<?php

namespace App\Http\Controllers;

use App\Models\Iku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IkuController extends Controller
{
    /**
     * Get all IKU with pagination, search, and filter
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = Iku::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        $ikus = $query->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $ikus
        ]);
    }

    /**
     * Create new IKU
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:50|unique:ikus,kode',
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

            $iku = Iku::create($request->only(['kode', 'nama', 'deskripsi', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'IKU berhasil ditambahkan',
                'data' => $iku
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update IKU
     */
    public function update(Request $request, $id)
    {
        try {
            $iku = Iku::find($id);

            if (!$iku) {
                return response()->json([
                    'success' => false,
                    'message' => 'IKU tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:50|unique:ikus,kode,' . $id,
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

            $iku->update($request->only(['kode', 'nama', 'deskripsi', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'IKU berhasil diperbarui',
                'data' => $iku
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete IKU
     */
    public function destroy($id)
    {
        try {
            $iku = Iku::find($id);

            if (!$iku) {
                return response()->json([
                    'success' => false,
                    'message' => 'IKU tidak ditemukan'
                ], 404);
            }

            $iku->delete();

            return response()->json([
                'success' => true,
                'message' => 'IKU berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal di server: ' . $e->getMessage()
            ], 500);
        }
    }
}
