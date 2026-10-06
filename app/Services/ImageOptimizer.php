<?php

namespace App\Services;

class ImageOptimizer {
    public static function processAndSave(array $file, string $destinationDir, int $maxWidth = 1200, int $maxHeight = 1200): ?string {
        if (empty($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        // 1. Strict File Size Limit (Max 8 MB, Min 100 bytes)
        $maxBytes = 8 * 1024 * 1024;
        $fileSize = $file['size'] ?? @filesize($file['tmp_name']);
        if (!$fileSize || $fileSize > $maxBytes || $fileSize < 100) {
            return null;
        }

        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0777, true);
        }

        // 2. Strict Extension Whitelist
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowed)) {
            return null;
        }

        // 3. Strict byte-level MIME type verification
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowedMimes)) {
                return null;
            }
        } elseif (function_exists('mime_content_type')) {
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, $allowedMimes)) {
                return null;
            }
        }

        // 4. Cryptographically Secure Random Filename (Eliminates path traversal and predictability)
        $newFileName = 'img_' . bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = rtrim($destinationDir, '/') . '/' . $newFileName;

        // If GD is available, optimize and resize
        if (extension_loaded('gd')) {
            $info = getimagesize($file['tmp_name']);
            if ($info) {
                [$width, $height, $type] = $info;
                $src = null;
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        $src = @imagecreatefromjpeg($file['tmp_name']);
                        break;
                    case IMAGETYPE_PNG:
                        $src = @imagecreatefrompng($file['tmp_name']);
                        break;
                    case IMAGETYPE_WEBP:
                        $src = @imagecreatefromwebp($file['tmp_name']);
                        break;
                }

                if ($src) {
                    $ratio = min($maxWidth / $width, $maxHeight / $height);
                    $newWidth = ($width > $maxWidth || $height > $maxHeight) ? (int)($width * $ratio) : $width;
                    $newHeight = ($width > $maxWidth || $height > $maxHeight) ? (int)($height * $ratio) : $height;

                    $dst = imagecreatetruecolor($newWidth, $newHeight);
                    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                    }

                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            imagejpeg($dst, $targetPath, 85);
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($dst, $targetPath, 8);
                            break;
                        case IMAGETYPE_WEBP:
                            imagewebp($dst, $targetPath, 85);
                            break;
                    }

                    imagedestroy($dst);
                    imagedestroy($src);
                    return $newFileName;
                }
            }
            // Strict defense: if GD is installed but cannot decode the file, reject it
            return null;
        }

        // Fallback ONLY when GD extension is not installed on the system
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $newFileName;
        }

        return null;
    }
}
