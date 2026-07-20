<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BeritaController extends Controller
{
    /**
     * Get all berita with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        
        $query = Berita::query();
        
        if ($search) {
            $query->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
        }
        
        $beritas = $query->orderBy('created_at', 'desc')
                         ->paginate($perPage);
        
        // Add full image URL
        $beritas->getCollection()->transform(function ($item) {
            $item->gambar_url = $item->gambar ? asset('storage/' . $item->gambar) : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $beritas
        ]);
    }

    /**
     * Get latest berita for landing page (limit 4)
     */
    public function latest()
    {
        $beritas = Berita::orderBy('created_at', 'desc')
                         ->limit(4)
                         ->get();
        
        $beritas->transform(function ($item) {
            $item->gambar_url = $item->gambar ? asset('storage/' . $item->gambar) : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $beritas
        ]);
    }

    /**
     * Get single berita
     */
    public function show($id)
    {
        $berita = Berita::find($id);
        
        if (!$berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita not found'
            ], 404);
        }

        $berita->gambar_url = $berita->gambar ? asset('storage/' . $berita->gambar) : null;

        return response()->json([
            'success' => true,
            'data' => $berita
        ]);
    }

    /**
     * Create new berita
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'link' => 'nullable|url|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_published' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['gambar']);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('berita-images', $imageName, 'public');
            $data['gambar'] = $path;
        }

        $berita = Berita::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Berita created successfully',
            'data' => $berita
        ], 201);
    }

    /**
     * Update berita
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);
        
        if (!$berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'link' => 'nullable|url|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_published' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['gambar']);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            
            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('berita-images', $imageName, 'public');
            $data['gambar'] = $path;
        }

        $berita->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Berita updated successfully',
            'data' => $berita
        ]);
    }

    /**
     * Delete berita
     */
    public function destroy($id)
    {
        $berita = Berita::find($id);
        
        if (!$berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita not found'
            ], 404);
        }

        // Delete image if exists
        if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita deleted successfully'
        ]);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish($id)
    {
        $berita = Berita::find($id);
        
        if (!$berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita not found'
            ], 404);
        }

        $berita->is_published = !$berita->is_published;
        $berita->save();

        return response()->json([
            'success' => true,
            'message' => 'Berita publish status toggled',
            'data' => $berita
        ]);
    }
}