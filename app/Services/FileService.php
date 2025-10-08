<?php

namespace App\Services;

class FileService
{
    public function sanitizeFilename(string $filename): string
    {
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        // Remove any runs of periods
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

        return $filename;
    }
}
