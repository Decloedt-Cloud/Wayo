<?php
$vendorAutoload = APPPATH . 'vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;

    class PHPExcel
    {
        public $security;
        public $documentSecurity;
        private $spreadsheet;
        private $activeSheet;
        private $excel2007;

        public function __construct()
        {
            $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $this->activeSheet = $this->spreadsheet->getActiveSheet();
            $this->security = new \stdClass();
            $this->documentSecurity = new \stdClass();
        }

        public function getProperties()
        {
            return $this->spreadsheet->getProperties();
        }

        public function setProperties($properties)
        {
            foreach ($properties as $key => $value) {
                $setter = 'set' . ucfirst($key);
                if (method_exists($this->spreadsheet->getProperties(), $setter)) {
                    $this->spreadsheet->getProperties()->$setter($value);
                }
            }
            return $this;
        }

        public function getActiveSheet()
        {
            return $this->activeSheet;
        }

        public function setActiveSheetIndex($index)
        {
            $this->spreadsheet->setActiveSheetIndex($index);
            $this->activeSheet = $this->spreadsheet->getActiveSheet();
            return $this;
        }

        public function getSheetCount()
        {
            return $this->spreadsheet->getSheetCount();
        }

        public function getWorksheetIterator()
        {
            return $this->spreadsheet->getWorksheetIterator();
        }

        public function getSheet($index)
        {
            return $this->spreadsheet->getSheet($index);
        }

        public function createSheet($i = null)
        {
            return $this->spreadsheet->createSheet($i);
        }

        public function addExternalSheet($sheet)
        {
            return $this->spreadsheet->addExternalSheet($sheet);
        }

        public function getAllSheets()
        {
            return $this->spreadsheet->getAllSheets();
        }

        public function setTitle($title)
        {
            $this->activeSheet->setTitle($title);
            return $this;
        }

        public function getDefaultStyle()
        {
            return $this->spreadsheet->getDefaultStyle();
        }

        public function setDefaultStyle($style)
        {
            $this->spreadsheet->setDefaultStyle($style);
            return $this;
        }

        public function setLocale($locale)
        {
            return true;
        }

        public function __call($method, $args)
        {
            if (method_exists($this->activeSheet, $method)) {
                return call_user_func_array([$this->activeSheet, $method], $args);
            }
            throw new \Exception("Method {$method} does not exist");
        }
    }

    class PHPExcel_IOFactory
    {
        public static function createReader($type)
        {
            return \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
        }

        public static function createWriter($object, $type = 'Excel2007')
        {
            if ($object instanceof PHPExcel) {
                $object = $object->spreadsheet;
            }
            
            if ($type === 'CSV') {
                return new \PhpOffice\PhpSpreadsheet\Writer\Csv($object);
            }
            
            return new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($object);
        }

        public static function identify($filename, $type = null)
        {
            return \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
        }

        public static function load($filename, $type = null)
        {
            $reader = $type ? \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type) : \PhpOffice\PhpSpreadsheet\IOFactory::createReader(\PhpOffice\PhpSpreadsheet\IOFactory::identify($filename));
            $spreadsheet = $reader->load($filename);
            
            $phpExcel = new PHPExcel();
            $phpExcel->spreadsheet = $spreadsheet;
            $phpExcel->activeSheet = $spreadsheet->getActiveSheet();
            
            return $phpExcel;
        }

        public static function write($object, $filename, $type = 'Excel2007')
        {
            if ($object instanceof PHPExcel) {
                $object = $object->spreadsheet;
            }
            
            $writer = self::createWriter($object, $type);
            $writer->save($filename);
        }
    }

    class PHPExcel_Style
    {
        public static function apply($style, $styleArray)
        {
        }
    }

    class PHPExcel_Cell
    {
        public static function coordinateFromString($coordinate)
        {
            return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);
        }
    }
} else {
    require_once APPPATH . 'third_party/PHPExcel/IOFactory.php';
}
