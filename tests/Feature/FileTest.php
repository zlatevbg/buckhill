<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_images_can_be_uploaded()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->post('/api/v1/file/upload', [
            'image' => $file,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        Storage::disk('local')->assertExists('public/pet-shop/' . $file->hashName());
    }

    public function test_images_can_be_downloaded()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->post('/api/v1/file/upload', [
            'image' => $file,
        ]);

        $response = Storage::disk('local')->download('public/pet-shop/' . $file->hashName());
        $this->assertEquals($response->getStatusCode(), 200);
        $headers = $response->headers->allPreserveCase();
        $this->assertEquals($headers['Content-Disposition'][0], 'attachment; filename=' . $file->hashName());
    }
}
