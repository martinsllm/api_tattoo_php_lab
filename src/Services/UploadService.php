<?php

declare(strict_types=1);

namespace App\Services;

class UploadService
{
    public function __construct(private string $uploadPath)
    {
    }

    public function uploadsRoot(): string
    {
        return $this->uploadPath;
    }

    public function normalizeFiles(string $field, array $filesBag): array
    {
        if (!isset($filesBag[$field])) {
            return [];
        }

        $entry = $filesBag[$field];

        if (!isset($entry['name']) || $entry['name'] === '' || $entry['name'] === []) {
            return [];
        }

        if (!is_array($entry['name'])) {
            return [$entry];
        }

        $list = [];
        foreach (array_keys($entry['name']) as $i) {
            $list[] = [
                'name' => $entry['name'][$i],
                'type' => $entry['type'][$i],
                'tmp_name' => $entry['tmp_name'][$i],
                'error' => $entry['error'][$i],
                'size' => $entry['size'][$i],
            ];
        }

        return $list;
    }

    public function uploadFile(int $artistId, array $file): array
    {   
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Upload failed.');
        }
    
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new \InvalidArgumentException('File too large.');
        }
    
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \InvalidArgumentException('Invalid upload.');
        }
    
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
    
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
    
        if ($mime === false || !isset($allowed[$mime])) {
            throw new \InvalidArgumentException('Invalid image type.');
        }
    
        $extension = $allowed[$mime];
        $dir = rtrim($this->uploadPath, '/') . '/artists/' . $artistId;
    
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Could not create upload directory.');
        }
    
        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $fullPath = $dir . '/' . $storedName;
    
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \RuntimeException('Could not save file.');
        }
    
        return [
            'relative_path' => 'artists/' . $artistId . '/' . $storedName,
            'image_filename' => (string) $file['name'],
            'mime_type' => $mime,
            'size_bytes' => (int) $file['size'],
        ];
    }
}