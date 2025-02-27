<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Zend\Barcode\Barcode;

class BarcodeController extends Controller
{
    public function generate($barcode)
    {
        // Pastikan response dalam format gambar PNG
        header('Content-Type: image/png');
        
        // Validasi barcode hanya berisi angka (untuk EAN13) atau gunakan Code128
        $barcodeOptions = [
            'text' => $barcode,
            'barHeight' => 50,
            'factor' => 2
        ];
        
        $rendererOptions = [];

        try {
            // Gunakan format yang sesuai
            $image = Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions)->render();
            imagepng($image);
            imagedestroy($image);
        } catch (\Exception $e) {
            die('Error generating barcode: ' . $e->getMessage());
        }
    }
}
