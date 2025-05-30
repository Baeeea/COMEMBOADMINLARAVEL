<?php
/**
 * Helper functions for profile image API
 */

/**
 * Resize an image while keeping the aspect ratio
 * 
 * @param string $imageData Binary image data
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return string Resized image data
 */
function resizeImage($imageData, $maxWidth, $maxHeight) {
    if (!extension_loaded('gd')) {
        error_log('GD library is not available. Image resizing skipped.');
        return $imageData;
    }
    
    try {
        // Create image from string
        $source = imagecreatefromstring($imageData);
        if (!$source) {
            error_log('Failed to create image from string');
            return $imageData;
        }
        
        // Get original dimensions
        $width = imagesx($source);
        $height = imagesy($source);
        
        // If image is smaller than max dimensions, return original
        if ($width <= $maxWidth && $height <= $maxHeight) {
            imagedestroy($source);
            return $imageData;
        }
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);
        
        // Create new image with new dimensions
        $destination = imagecreatetruecolor($new_width, $new_height);
        
        // Handle transparency for PNG
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
        
        // Resize the image
        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $new_width, $new_height, $width, $height
        );
        
        // Start output buffering
        ob_start();
        
        // Get image type to determine output format
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($destination, null, 90);
                break;
            case 'image/png':
                imagepng($destination, null, 9);
                break;
            case 'image/gif':
                imagegif($destination);
                break;
            default:
                // Default to PNG
                imagepng($destination, null, 9);
        }
        
        // Get the buffer content and clean it
        $result = ob_get_clean();
        
        // Free up memory
        imagedestroy($source);
        imagedestroy($destination);
        
        return $result ?: $imageData;
    } catch (Exception $e) {
        error_log('Image resize error: ' . $e->getMessage());
        return $imageData;
    }
}
