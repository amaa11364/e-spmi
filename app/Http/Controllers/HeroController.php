<?php

namespace App\Http\Controllers;

use App\Models\HeroContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeroController extends Controller
{
    /**
     * Display the hero content
     */
    public function index()
    {
        $hero = HeroContent::first();
        
        if (!$hero) {
            // Create default hero content if none exists
            $hero = HeroContent::create([
                'title' => 'Sistem Penjaminan Mutu Internal Digital',
                'subtitle' => 'Transformasi Digital SPMI IKIP',
                'description' => 'Platform terintegrasi untuk memonitor, mengelola, dan meningkatkan mutu pendidikan secara digital.',
                'cta_text' => 'Mulai Sekarang',
                'cta_link' => '#features',
                'background_image' => null,
                'is_active' => true
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $hero
        ]);
    }

    /**
     * Update hero content
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cta_text' => 'required|string|max:100',
            'cta_link' => 'required|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hero = HeroContent::first();
        
        if (!$hero) {
            $hero = new HeroContent();
        }

        $data = $request->except(['background_image']);

        // Handle image upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($hero->background_image && Storage::disk('public')->exists($hero->background_image)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            
            $image = $request->file('background_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('hero-images', $imageName, 'public');
            $data['background_image'] = $path;
        }

        $hero->fill($data);
        $hero->save();

        return response()->json([
            'success' => true,
            'message' => 'Hero content updated successfully',
            'data' => $hero
        ]);
    }

    /**
     * Upload hero background image
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hero = HeroContent::first();
        
        if (!$hero) {
            $hero = new HeroContent();
        }

        // Delete old image if exists
        if ($hero->background_image && Storage::disk('public')->exists($hero->background_image)) {
            Storage::disk('public')->delete($hero->background_image);
        }

        $image = $request->file('image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('hero-images', $imageName, 'public');
        
        $hero->background_image = $path;
        $hero->save();

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data' => [
                'image_path' => $path,
                'image_url' => asset('storage/' . $path)
            ]
        ]);
    }

    /**
     * Delete hero background image
     */
    public function deleteImage()
    {
        $hero = HeroContent::first();
        
        if (!$hero || !$hero->background_image) {
            return response()->json([
                'success' => false,
                'message' => 'No image found to delete'
            ], 404);
        }

        if (Storage::disk('public')->exists($hero->background_image)) {
            Storage::disk('public')->delete($hero->background_image);
        }

        $hero->background_image = null;
        $hero->save();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}