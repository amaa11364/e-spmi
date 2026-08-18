<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
class TestUploadTest extends TestCase {
    public function test_upload() {
        $file1 = UploadedFile::fake()->create('document1.pdf', 100);
        $file2 = UploadedFile::fake()->create('document2.pdf', 100);
        $response = $this->call('POST', "/test-upload", [], [], ['files' => [$file1, $file2]]);
        dump($response->json());
        $response->assertStatus(200);
    }
}
