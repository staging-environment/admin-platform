<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function autoCropImageToRatio(string $fullPath, float $targetRatio = 3.5): void
    {
        try {
            if (!file_exists($fullPath)) {
                return;
            }

            [$width, $height, $type] = @getimagesize($fullPath);
            if ($width <= 0 || $height <= 0) {
                return;
            }

            $currentRatio = $width / $height;

            // If the ratio is already close enough, don't crop
            if (abs($currentRatio - $targetRatio) < 0.1) {
                return;
            }

            // Calculate crop dimensions
            if ($currentRatio > $targetRatio) {
                // Image is too wide, crop horizontally (keep full height, reduce width)
                $newWidth = (int) round($height * $targetRatio);
                $newHeight = $height;
                $x = (int) (($width - $newWidth) / 2);
                $y = 0;
            } else {
                // Image is too tall, crop vertically (keep full width, reduce height)
                $newWidth = $width;
                $newHeight = (int) round($width / $targetRatio);
                $x = 0;
                $y = (int) (($height - $newHeight) / 2);
            }

            // Load image based on type
            $srcImage = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $srcImage = @imagecreatefromjpeg($fullPath);
                    break;
                case IMAGETYPE_PNG:
                    $srcImage = @imagecreatefrompng($fullPath);
                    break;
                case IMAGETYPE_WEBP:
                    $srcImage = @imagecreatefromwebp($fullPath);
                    break;
                case IMAGETYPE_GIF:
                    $srcImage = @imagecreatefromgif($fullPath);
                    break;
            }

            if (!$srcImage) {
                return;
            }

            // Create new true color image
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and WebP
            if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
            }

            // Crop and copy
            imagecopyresampled($dstImage, $srcImage, 0, 0, $x, $y, $newWidth, $newHeight, $newWidth, $newHeight);

            // Save image back
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($dstImage, $fullPath, 90);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($dstImage, $fullPath, 6);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($dstImage, $fullPath, 85);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($dstImage, $fullPath);
                    break;
            }

            imagedestroy($srcImage);
            imagedestroy($dstImage);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to auto-crop image {$fullPath}: " . $e->getMessage());
        }
    }
}
