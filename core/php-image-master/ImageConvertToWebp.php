<?php

class ImageConvertToWebp
{

    private $quality;
    private $jpgPath = '';



    function __construct($jpgPath,$quality = 70) {
        $this->jpgPath = $jpgPath;
        $this->quality = $quality;
    }


    public function convert(){
        // check if file exists
        if (!file_exists($this->jpgPath)) {
            return false;
        }

        $file_type = exif_imagetype($this->jpgPath);
        //https://www.php.net/manual/en/function.exif-imagetype.php
        //exif_imagetype($file);
        // 1    IMAGETYPE_GIF
        // 2    IMAGETYPE_JPEG
        // 3    IMAGETYPE_PNG
        // 6    IMAGETYPE_BMP
        // 15   IMAGETYPE_WBMP
        // 16   IMAGETYPE_XBM
        $info = pathinfo($this->jpgPath);
        $output_file =  $info['dirname'].'/'.$info['filename'] . '.webp';

        if (file_exists($output_file)) {
            return $output_file;
        }

        if (function_exists('imagewebp')) {

            switch ($file_type) {
                case '1': //IMAGETYPE_GIF
                    $image = imagecreatefromgif($this->jpgPath);
                    break;
                case '2': //IMAGETYPE_JPEG
                    $image = imagecreatefromjpeg($this->jpgPath);
                    break;
                case '3': //IMAGETYPE_PNG
                    $image = imagecreatefrompng($this->jpgPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case '15': //IMAGETYPE_Webp
                    return false;
                    break;
                case '16': //IMAGETYPE_XBM
                    $image = imagecreatefromxbm($this->jpgPath);
                    break;
                default:
                    return false;
            }

            // Save the image
            $result = imagewebp($image, $output_file, $this->quality);
            if (false === $result) {
                return false;
            }
            // Free up memory
            imagedestroy($image);

            return $output_file;
        } elseif (class_exists('Imagick')) {

            $image = new Imagick();
            $image->readImage($this->jpgPath);
            if ($file_type === "3") {
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($this->quality);
                $image->setOption('webp:lossless', 'true');
            }
            $image->writeImage($output_file);
            return $output_file;
        }
        return false;
    }
}