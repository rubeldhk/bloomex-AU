<?php

class ImageConvertToJpeg
{

    private $quality;
    private $webpPath = '';
    private $jpgPath = '';

    function __construct($webpPath, $jpgPath, $quality = 70) {
        $this->webpPath = $webpPath;
        $this->jpgPath = $jpgPath;
        $this->quality = $quality;
    }

    public function convert()
    {
        // Check if the GD library is loaded
        if (!extension_loaded('gd')) {
            echo "Error: GD library is not loaded. Please enable it in your php.ini.\n";
            return false;
        }

        $extension = pathinfo($this->webpPath, PATHINFO_EXTENSION);
        if ($extension === 'jpg' || $extension === 'jpeg') {
            copy($this->webpPath, $this->jpgPath);

            return true;
        }

        // Check if the WebP image exists
        if (!file_exists($this->webpPath)) {
            echo "Error: WebP file not found at: " . $this->webpPath . "\n";
            return false;
        }

        // Create a new image resource from the WebP file
        $image = imagecreatefromwebp($this->webpPath);

        if ($image === false) {
            echo "Error: Could not create image resource from WebP file. Check file corruption or GD WebP support.\n";
            return false;
        }

        // Save the image as a JPEG
        $success = imagejpeg($image, $this->jpgPath, $this->quality);

        // Free up memory
        imagedestroy($image);

        return $success;
    }
}