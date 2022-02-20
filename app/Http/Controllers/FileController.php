<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Download file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Request $request, File $file)
    {
        return Storage::download($file->path);
    }

    /**
     * Upload file.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        /** @var \Illuminate\Http\UploadedFile */
        $image = $request->file('image');
        $path = $image->store('public/pet-shop');

        File::create([
            'uuid' => Str::uuid(),
            'name' => $image->getClientOriginalName(),
            'path' => $path,
            'size' => $image->getSize(),
            'type' => $image->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
