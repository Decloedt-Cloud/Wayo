<?php

namespace App\Libraries;
$vendorAutoload = ROOTPATH . 'vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    
    class Pdf extends \Mpdf\Mpdf
    {
        public function __construct(array $config = [])
        {
            $defaultConfig = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
            ];

            $config = array_merge($defaultConfig, $config);

            parent::__construct($config['mode'], $config['format'], $config['margin_top'], $config['margin_right'], $config['margin_bottom'], $config['margin_left']);
        }
    }
} else {
    require_once ROOTPATH . 'app/Libraries/dompdf/autoload.inc.php';
    
    class Pdf extends \Dompdf\Dompdf
    {
        public function __construct(array $config = [])
        {
            parent::__construct();
        }
    }
}
