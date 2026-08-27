<?php

namespace App\Http\Controllers;

use App\Models\DokumenFolder;
use App\Models\DokumenFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = DokumenFolder::with(['files.uploader', 'files.iku', 'children.files', 'children' => function($q) {
            $q->withCount('files');
        }])->withCount('files');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
            });
        } else {
            $query->whereNull('parent_id');
        }
        
        $folders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $folders
        ]);
    }


    public function showFolder($id)
    {
        $folder = DokumenFolder::with(['files.uploader', 'files.iku', 'parent', 'children.files', 'children' => function($q) {
            $q->withCount('files');
        }])->withCount('files')->find($id);

        if (!$folder) {
            return response()->json(['success' => false, 'message' => 'Folder not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $folder]);
    }

    /**
     * Resolve a folder by its slug path (e.g. "parent-slug/child-slug").
     * Returns the folder data with children and files, plus ancestor chain for breadcrumb.
     */
    public function resolveBySlugPath(Request $request)
    {
        $slugPath = $request->get('path', '');
        if (empty($slugPath)) {
            return response()->json(['success' => false, 'message' => 'Path is required'], 400);
        }

        $slugs = array_filter(explode('/', $slugPath));
        if (empty($slugs)) {
            return response()->json(['success' => false, 'message' => 'Invalid path'], 400);
        }

        // Walk the slug hierarchy from root to target
        $parentId = null;
        $ancestors = [];
        $folder = null;

        foreach ($slugs as $slug) {
            $query = DokumenFolder::where('slug', $slug);
            if ($parentId === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
            $folder = $query->first();

            if (!$folder) {
                return response()->json(['success' => false, 'message' => 'Folder not found'], 404);
            }

            // Add to ancestors (all except the last slug which is the target)
            $ancestors[] = ['id' => $folder->id, 'nama' => $folder->nama, 'slug' => $folder->slug];
            $parentId = $folder->id;
        }

        // Remove the last element from ancestors (that's the current folder, not an ancestor)
        array_pop($ancestors);

        // Load the target folder with full details
        $folder = DokumenFolder::with(['files.uploader', 'files.iku', 'parent', 'children.files', 'children' => function($q) {
            $q->withCount('files');
        }])->withCount('files')->find($folder->id);

        return response()->json([
            'success' => true,
            'data' => $folder,
            'ancestors' => $ancestors
        ]);
    }

    public function storeFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_public' => 'boolean',
            'parent_id' => 'nullable|exists:dokumen_folders,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $folder = DokumenFolder::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Folder berhasil dibuat',
            'data' => $folder
        ], 201);
    }

    public function updateFolder(Request $request, $id)
    {
        $folder = DokumenFolder::find($id);
        
        if (!$folder) {
            return response()->json([
                'success' => false,
                'message' => 'Folder not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_public' => 'boolean',
            'parent_id' => 'nullable|exists:dokumen_folders,id'
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Validation failed in updateFolder: ', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $folder->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_public' => $request->boolean('is_public'),
            'parent_id' => $request->parent_id,
        ]);

        $folder->refresh();
        $folder->loadCount('files');

        return response()->json([
            'success' => true,
            'message' => 'Folder berhasil diperbarui',
            'data' => $folder
        ]);
    }

    public function destroyFolder($id)
    {
        $folder = DokumenFolder::find($id);
        
        if (!$folder) {
            return response()->json([
                'success' => false,
                'message' => 'Folder not found'
            ], 404);
        }

        // Recursively collect all file paths from this folder and its descendants
        $this->deleteAllFilesRecursive($folder);

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder deleted successfully'
        ]);
    }

    /**
     * Recursively delete all physical files from a folder and its child folders.
     */
    private function deleteAllFilesRecursive(DokumenFolder $folder)
    {
        // Delete files in this folder
        foreach ($folder->files as $file) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        // Recursively process child folders
        foreach ($folder->children as $child) {
            $this->deleteAllFilesRecursive($child);
        }
    }

    public function toggleFolderPublic($id)
    {
        $folder = DokumenFolder::find($id);
        
        if (!$folder) {
            return response()->json([
                'success' => false,
                'message' => 'Folder not found'
            ], 404);
        }

        $folder->is_public = !$folder->is_public;
        $folder->save();

        // Update semua file di dalamnya agar menyesuaikan status folder
        DokumenFile::where('dokumen_folder_id', $folder->id)->update(['is_public' => $folder->is_public]);

        return response()->json([
            'success' => true,
            'message' => 'Folder publish status toggled',
            'data' => $folder
        ]);
    }

    // Mendukung upload single maupun massal (batch upload)
    public function storeFile(Request $request, $folderId)
    {
        $folder = DokumenFolder::find($folderId);
        
        if (!$folder) {
            return response()->json([
                'success' => false,
                'message' => 'Folder not found'
            ], 404);
        }

        // Jika request berisi array files[] (Input Massal)
        if ($request->hasFile('files')) {
            $validator = Validator::make($request->all(), [
                'files' => 'required|array',
                'files.*' => 'required|file|max:51200', // Ukuran dinaikkan hingga 50 MB per file
                'deskripsi' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $createdFiles = [];
            $isPublic = $request->boolean('is_public', $folder->is_public);

            foreach ($request->file('files') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('dokumen/' . $folderId, $filename, 'public');

                $createdFiles[] = DokumenFile::create([
                    'dokumen_folder_id' => $folderId,
                    'nama' => $originalName,
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'deskripsi' => $request->deskripsi,
                    'is_public' => $isPublic
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => count($createdFiles) . ' file berhasil diupload',
                'data' => $createdFiles
            ], 201);
        }

        // Upload single file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200', // Maksimal 50 MB
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('dokumen/' . $folderId, $filename, 'public');

        $dokumenFile = DokumenFile::create([
            'dokumen_folder_id' => $folderId,
            'nama' => $request->nama,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'deskripsi' => $request->deskripsi,
            'is_public' => $request->boolean('is_public')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload',
            'data' => $dokumenFile
        ], 201);
    }

    public function updateFile(Request $request, $id)
    {
        $file = DokumenFile::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_public' => $request->boolean('is_public')
        ]);

        $file->refresh();

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diperbarui',
            'data' => $file
        ]);
    }

    public function destroyFile($id)
    {
        $file = DokumenFile::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully'
        ]);
    }

    public function toggleFilePublic($id)
    {
        $file = DokumenFile::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $file->is_public = !$file->is_public;
        $file->save();

        return response()->json([
            'success' => true,
            'message' => 'File publish status toggled',
            'data' => $file
        ]);
    }

    // Mengambil semua folder publik atau folder yang memiliki file publik
    public function publicFolders()
    {
        $folders = DokumenFolder::where(function($query) {
            $query->where('is_public', true)
                  ->orWhereHas('files', function($q) {
                      $q->where('is_public', true);
                  });
        })
        ->with(['publicFiles' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $folders
        ]);
    }

    public function globalSearch(Request $request)
    {
        $search = $request->get('q');
        if (!$search) {
            return response()->json(['success' => true, 'data' => ['folders' => [], 'files' => []]]);
        }

        $folders = DokumenFolder::where('nama', 'LIKE', "%{$search}%")
            ->orWhere('deskripsi', 'LIKE', "%{$search}%")
            ->take(5)
            ->get();

        $files = DokumenFile::with('folder')
            ->where('nama', 'LIKE', "%{$search}%")
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'folders' => $folders,
                'files' => $files
            ]
        ]);
    }

    public function downloadFile($id)
    {
        $file = DokumenFile::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        if (!$file->is_public) {
            if (!auth('sanctum')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        }

        if (!$file->file_path || !Storage::disk('public')->exists($file->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found in storage'
            ], 404);
        }

        return Storage::disk('public')->download(
            $file->file_path, 
            $file->nama . '.' . pathinfo($file->file_path, PATHINFO_EXTENSION)
        );
    }
}