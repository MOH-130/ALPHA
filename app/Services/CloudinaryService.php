<?php

namespace App\Services;

use CURLFile;

/**
 * Cloudinary Object Storage Service
 * Provides permanent, CDN-backed image storage for Alpha Gaming Shop.
 * Zero-dependency: Uses native PHP cURL and reads credentials strictly from environment variables.
 */
class CloudinaryService {
    private static ?string $cloudName = null;
    private static ?string $apiKey = null;
    private static ?string $apiSecret = null;
    private static bool $initialized = false;

    private static function init(): void {
        if (self::$initialized) return;

        // 1. Check direct environment variables
        self::$cloudName = env('CLOUDINARY_CLOUD_NAME');
        self::$apiKey = env('CLOUDINARY_API_KEY');
        self::$apiSecret = env('CLOUDINARY_API_SECRET');

        // 2. Fallback: Parse CLOUDINARY_URL if provided (e.g. cloudinary://api_key:api_secret@cloud_name)
        if (empty(self::$cloudName) || empty(self::$apiKey) || empty(self::$apiSecret)) {
            $cloudinaryUrl = env('CLOUDINARY_URL');
            if (!empty($cloudinaryUrl) && preg_match('#cloudinary://([^:]+):([^@]+)@(.+)#', $cloudinaryUrl, $matches)) {
                self::$apiKey = $matches[1];
                self::$apiSecret = $matches[2];
                self::$cloudName = $matches[3];
            }
        }

        self::$initialized = true;
    }

    /**
     * Check if Cloudinary is configured via environment variables
     */
    public static function isConfigured(): bool {
        self::init();
        return !empty(self::$cloudName) && !empty(self::$apiKey) && !empty(self::$apiSecret);
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param string|array $file Local file path or $_FILES['input'] array
     * @param string $folder Folder in Cloudinary (e.g. 'alpha_gaming/products')
     * @return array|null Returns array with 'url', 'public_id' on success, null on failure
     */
    public static function upload($file, string $folder = 'alpha_gaming/products'): ?array {
        if (!self::isConfigured()) {
            return null;
        }

        $filePath = null;
        if (is_array($file) && !empty($file['tmp_name'])) {
            $filePath = $file['tmp_name'];
        } elseif (is_string($file) && file_exists($file)) {
            $filePath = $file;
        }

        if (!$filePath || !file_exists($filePath)) {
            return null;
        }

        $timestamp = time();
        $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
        $signature = sha1($paramsToSign . self::$apiSecret);

        $endpoint = "https://api.cloudinary.com/v1_1/" . self::$cloudName . "/image/upload";

        $postFields = [
            'file' => new CURLFile($filePath),
            'api_key' => self::$apiKey,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode < 200 || $httpCode >= 300) {
            error_log("Cloudinary Upload Error [HTTP {$httpCode}]: " . ($curlError ?: $response));
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data['secure_url'])) {
            error_log("Cloudinary Invalid Response: " . $response);
            return null;
        }

        return [
            'success' => true,
            'url' => $data['secure_url'],
            'public_id' => $data['public_id'],
            'format' => $data['format'] ?? '',
            'bytes' => $data['bytes'] ?? 0,
            'width' => $data['width'] ?? 0,
            'height' => $data['height'] ?? 0
        ];
    }

    /**
     * Delete an image from Cloudinary by its public_id
     */
    public static function delete(string $publicId): bool {
        if (!self::isConfigured() || empty($publicId)) {
            return false;
        }

        $timestamp = time();
        $paramsToSign = "public_id={$publicId}&timestamp={$timestamp}";
        $signature = sha1($paramsToSign . self::$apiSecret);

        $endpoint = "https://api.cloudinary.com/v1_1/" . self::$cloudName . "/image/destroy";

        $postFields = [
            'public_id' => $publicId,
            'api_key' => self::$apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return isset($data['result']) && $data['result'] === 'ok';
    }

    /**
     * Helper to extract public_id from a Cloudinary URL if needed
     */
    public static function extractPublicIdFromUrl(string $url): ?string {
        if (!str_contains($url, 'res.cloudinary.com')) {
            return null;
        }
        $parts = explode('/upload/', $url);
        if (count($parts) < 2) return null;
        
        $pathAfterUpload = $parts[1];
        // Strip version prefix if present, e.g. v123456789/
        $pathWithoutVersion = preg_replace('#^v\d+/#', '', $pathAfterUpload);
        // Strip file extension
        return preg_replace('#\.[^.]+$#', '', $pathWithoutVersion);
    }
}
