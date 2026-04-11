<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private string $baseFolder;

    public function __construct()
    {
        $this->baseFolder = config('cloudinary.folder', '');
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $this->baseFolder . $folder,
        ]);

        return $result['secure_url'];
    }

    public function uploadFile(UploadedFile $file, string $folder): string
    {
        $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder'        => $this->baseFolder . $folder,
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }

    public function uploadMultiple(array $files, string $folder): array
    {
        return array_map(fn($file) => $this->uploadFile($file, $folder), $files);
    }

    public function delete(string $url): bool
    {
        $publicId = $this->extractPublicIdFromUrl($url);
        cloudinary()->uploadApi()->destroy($publicId);
        return true;
    }

    public function deleteMultiple(array $urls): bool
    {
        foreach ($urls as $url) {
            $this->delete($url);
        }
        return true;
    }

    private function extractPublicIdFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        $result = preg_replace('/^\/[^\/]+\/[^\/]+\/upload\/v\d+\//', '', $path);
        
        if ($result === $path) {
            $result = preg_replace('/^\/[^\/]+\/[^\/]+\/upload\//', '', $path);
        }

        return Str::beforeLast($result, '.');
    }
}