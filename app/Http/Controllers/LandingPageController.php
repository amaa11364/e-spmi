<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\ActivityDocumentation;
use App\Models\HeroContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    // Public endpoint for the frontend
    public function index()
    {
        $hero = HeroContent::first();
        $team = TeamMember::where('is_active', true)->orderBy('order')->get();
        $documentations = ActivityDocumentation::where('is_active', true)->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'hero' => $hero,
                'team' => $team,
                'documentations' => $documentations
            ]
        ]);
    }

    // --- Admin Endpoints ---

    public function getTeam()
    {
        $team = TeamMember::orderBy('order')->get();
        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }

    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $member = TeamMember::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Anggota tim berhasil ditambahkan',
            'data' => $member
        ]);
    }

    public function updateTeamMember(Request $request, $id)
    {
        $member = TeamMember::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image_url' => 'nullable|string',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $member->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Anggota tim berhasil diupdate',
            'data' => $member
        ]);
    }

    public function destroyTeamMember($id)
    {
        $member = TeamMember::findOrFail($id);
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anggota tim berhasil dihapus'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/landing');
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image upload failed'
        ], 400);
    }

    public function getDocumentations()
    {
        $docs = ActivityDocumentation::orderBy('order')->get();
        return response()->json([
            'success' => true,
            'data' => $docs
        ]);
    }

    public function storeDocumentation(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|string',
            'activity_date' => 'nullable|date',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $doc = ActivityDocumentation::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil ditambahkan',
            'data' => $doc
        ]);
    }

    public function updateDocumentation(Request $request, $id)
    {
        $doc = ActivityDocumentation::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|string',
            'activity_date' => 'nullable|date',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $doc->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil diupdate',
            'data' => $doc
        ]);
    }

    public function destroyDocumentation($id)
    {
        $doc = ActivityDocumentation::findOrFail($id);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumentasi berhasil dihapus'
        ]);
    }
}
