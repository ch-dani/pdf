<?php

namespace App;

require_once '../vendor/phpexcel/Classes/PHPExcel.php';
require_once '../vendor/phpexcel/Classes/PHPExcel/Writer/Excel5.php';
require_once '../vendor/phpexcel/Classes/PHPExcel/Style/Fill.php';
require_once '../vendor/phpexcel/Classes/PHPExcel/Style/Border.php';
require_once '../vendor/phpexcel/Classes/PHPExcel/IOFactory.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use PHPExcel;
use PHPExcel_Writer_Excel5;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_IOFactory;

class Excel extends Model {

    public static function generate($data, $type)
    {
        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle('Export '.ucfirst($type));
        $end_letter = 'A';

        $small_cells = [];

        switch ($type) {
            case "pages":
                $small_cells = ['A', 'B', 'C'];
                break;
            case "faq":
                $small_cells = ['A'];
                break;
            case "guides":
                $small_cells = ['A', 'B'];
                break;
            case "menu":
                $small_cells = ['A', 'B'];
                break;
            case "footer-menu":
                $small_cells = ['A', 'B'];
                break;
        }

        foreach ($data as $number_line => $line) {
            foreach ($line as $letter => $val) {
                $sheet->setCellValue($letter.$number_line, $val);

                if ($number_line == 1) {
                    $sheet->getStyle($letter.$number_line)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet->getStyle($letter.$number_line)->getFill()->getStartColor()->setRGB('dedddd');

                    if (!in_array($letter, $small_cells))
                        $sheet->getColumnDimension($letter)->setWidth(60);

                    $end_letter = $letter;
                }

                if ($letter == 'A' and $number_line > 1) {
                    $sheet->getStyle($letter.$number_line.':'.$end_letter.$number_line)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet->getStyle($letter.$number_line.':'.$end_letter.$number_line)->getFill()->getStartColor()->setRGB('EEEEEE');
                }
            }
        }

        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export_".$type."_".date('c').".xls");

        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
    }

    public static function XlsToArray($filepath, $type)
    {
        $xls = PHPExcel_IOFactory::load($filepath);
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $data = [];

        $rowIterator = $sheet->getRowIterator();
        foreach ($rowIterator as $key => $row) {
            $cellIterator = $row->getCellIterator();

            foreach ($cellIterator as $cell) {
                $data[$key][] = $cell->getCalculatedValue();
            }
        }

        $array = [];
        $error = false;
        //var_dump($data);

        switch ($type) {
            case 'pages':
                $headers = ['ID', 'Title Page', 'Field'];

                foreach ($headers as $key => $header) {
                    if ($data[1][$key] != $header)
                        $error = true;
                }

                if (!$error) {
                    $headers = $data[1];
                    unset($data[1]);

                    foreach ($data as $row) {
                        foreach ($row as $key => $cell) {

                            switch (intval($key)) {
                                case 0:
                                    if (!is_null($cell)) {
                                        $id = $cell;
                                        $array[$id] = [];
                                    }
                                    break;
                                case 1:
                                    if (!is_null($cell)) {
                                        $array[$id]['title'] = $cell;
                                    }
                                    break;
                                case 2:
                                    if (!is_null($cell)) {
                                        $array[$id][$cell] = [];
                                        $current_cell = $cell;
                                    }
                                    break;
                            }

                            if ($key > 2)
                                $array[$id][$current_cell][$headers[$key]] = $cell;

                        }
                    }
                }

                break;
            case 'faq':
                $headers = ['ID', 'Field'];

                foreach ($headers as $key => $header) {
                    if ($data[1][$key] != $header)
                        $error = true;
                }

                if (!$error) {
                    $headers = $data[1];
                    unset($data[1]);

                    foreach ($data as $row) {
                        foreach ($row as $key => $cell) {

                            switch (intval($key)) {
                                case 0:
                                    if (!is_null($cell)) {
                                        $id = $cell;
                                        $array[$id] = [];
                                    }
                                    break;
                                case 1:
                                    if (!is_null($cell)) {
                                        $array[$id][$cell] = [];
                                        $current_cell = $cell;
                                    }
                                    break;
                            }

                            if ($key > 1)
                                $array[$id][$current_cell][$headers[$key]] = $cell;

                        }
                    }
                }

                break;
            case 'guides':
                $headers = ['ID', 'Field'];

                foreach ($headers as $key => $header) {
                    if ($data[1][$key] != $header)
                        $error = true;
                }

                if (!$error) {
                    $headers = $data[1];
                    unset($data[1]);

                    foreach ($data as $row) {
                        foreach ($row as $key => $cell) {

                            switch (intval($key)) {
                                case 0:
                                    if (!is_null($cell)) {
                                        $id = $cell;
                                        $array[$id] = [];
                                    }
                                    break;
                                case 1:
                                    if (!is_null($cell)) {
                                        $array[$id][$cell] = [];
                                        $current_cell = $cell;
                                    }
                                    break;
                            }

                            if ($key > 1)
                                $array[$id][$current_cell][$headers[$key]] = $cell;

                        }
                    }
                }

                break;
            case 'menu':
                $headers = ['ID', 'Field'];

                foreach ($headers as $key => $header) {
                    if ($data[1][$key] != $header)
                        $error = true;
                }

                if (!$error) {
                    $headers = $data[1];
                    unset($data[1]);

                    foreach ($data as $row) {
                        foreach ($row as $key => $cell) {

                            switch (intval($key)) {
                                case 0:
                                    if (!is_null($cell)) {
                                        $id = $cell;
                                        $array[$id] = [];
                                    }
                                    break;
                                case 1:
                                    if (!is_null($cell)) {
                                        $array[$id][$cell] = [];
                                        $current_cell = $cell;
                                    }
                                    break;
                            }

                            if ($key > 1)
                                $array[$id][$current_cell][$headers[$key]] = $cell;

                        }
                    }
                }

                break;
            case 'footer-menu':
                $headers = ['ID', 'Field'];

                foreach ($headers as $key => $header) {
                    if ($data[1][$key] != $header)
                        $error = true;
                }

                if (!$error) {
                    $headers = $data[1];
                    unset($data[1]);

                    foreach ($data as $row) {
                        foreach ($row as $key => $cell) {
                            if ($key == 0 and is_null($cell))
                                continue 2;

                            switch (intval($key)) {
                                case 0:
                                    if (!is_null($cell)) {
                                        $id = $cell;
                                        $array[$id] = [];

                                    }
                                    break;
                                case 1:
                                    if (!is_null($cell)) {
                                        $array[$id][$cell] = [];
                                        $current_cell = $cell;
                                    }
                                    break;
                            }

                            if ($key > 1)
                                $array[$id][$current_cell][$headers[$key]] = $cell;
                        }
                    }
                }

                break;
            default:
                $error = true;
        }

        return $error ? false : $array;
    }

}