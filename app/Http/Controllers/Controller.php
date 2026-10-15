<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller extends BaseController
{
    protected function storeUploadedFile(object $file, string $directory, ?string $existingPath = null): string
    {
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }
}