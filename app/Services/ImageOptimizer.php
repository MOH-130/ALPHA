<?php

namespace App\Services;

class ImageOptimizer {
    public static function processAndSave(array $file, string $destinationDir, int $maxWidth = 1200, int $maxHeight = 1200): ?string {
        if (empty($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowed)) {
            return null;
        }

        $newFileName = 'img_' . uniqid() . '.' . $extension;
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
        }

        // Fallback: standard move_uploaded_file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $newFileName;
        }

        return null;
    }
}
