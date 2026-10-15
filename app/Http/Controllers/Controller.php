<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

abstract class Controller extends BaseController
{
    protected function storeUploadedFile(object $file, string $directory, ?string $existingPath = null): string
    {
        $basePath = storage_path('app/public/' . $directory);
        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        if ($existingPath) {
            $fullOldPath = storage_path('app/public/' . $existingPath);
            if (is_file($fullOldPath)) {
                unlink($fullOldPath);
            }
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $relativePath = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $relativePath);

        file_put_contents($fullPath, file_get_contents($file->getRealPath()));

        return $relativePath;
    }
}
