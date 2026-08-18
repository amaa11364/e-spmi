<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\DokumenFolder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DokumenUploadRealTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_upload()
    {
        Storage::fake('public');
        
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        $this->withoutMiddleware();
        
        $folder = DokumenFolder::create(['nama' => 'Test Folder']);
        
        $file1 = UploadedFile::fake()->create('document1.pdf', 100);
        $file2 = UploadedFile::fake()->create('document2.pdf', 100);

        // Simulate real FormData
        $response = $this->call('POST', "/api/dokumen/folders/{$folder->id}/files", [
            'is_public' => 1
        ], [], [
            'files' => [$file1, $file2]
        ]);

        dump($response->json());
        $response->assertStatus(201);
    }
}
