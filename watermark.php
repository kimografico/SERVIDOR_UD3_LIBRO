<?php

    $watermark = imagecreatefrompng('KimoDEV.png');
    imagealphablending($watermark, false);
    imagesavealpha($watermark, true);
    $watermarkWidth = imagesx($watermark);
    $watermarkHeight = imagesy($watermark);
    
    if (file_exists('book/portada.jpg')) {
        $image = imagecreatefromjpeg('book/portada.jpg');
    } else {
        $image = imagecreatefrompng('book/portada.png');
    };

    imagecopy($image, $watermark, 10, 10, 0, 0, $watermarkWidth, $watermarkHeight);
    

    header('content-type: image/jpg');
    imagejpeg($image);
    imagedestroy($image);
    imagedestroy($watermark);
    

?>