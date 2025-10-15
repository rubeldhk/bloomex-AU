<?php
if ($argc < 2) {
    echo "Usage: php parser.php <filename>\n";
    exit(1);
}

$filepath = $argv[1];

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__) . '/';
include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

try {

//    $filepath = $_SERVER['DOCUMENT_ROOT'].'/calls-occasion/BLX_AUS_DIALER_OCCASION_2022_for_2024.xlsx';
    $file = array();
    $inputFileType = PHPExcel_IOFactory::identify($filepath);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($filepath);
    $file = $objPHPExcel->getActiveSheet()->toArray();

    unset($file[0]);

    $gmt_offsets = [
        'AUSTRALIA' => array(
            'AUSTRALIAN CAPITAL TERRITORY' => '+10',
            'NEW SOUTH WALES' => '+10',
            'NORTHERN TERRITORY' => '+10',
            'QUEENSLAND' => '+10',
            'SOUTH AUSTRALIA' => '+9',
            'TASMANIA' => '+10',
            'VICTORIA' => '+10',
            'WESTERN AUSTRALIA' => '+8'
        ),
        'CAN' => array(
            'AB' => '-7',
            'BC' => '-8',
            'MB' => '-6',
            'NB' => '-5',
            'NL' => '-3.5',
            'NT' => '-7',
            'NS' => '-4',
            'NU' => '-5',
            'ON' => '-5',
            'PE' => '-4',
            'QC' => '-5',
            'SK' => '-6',
            'YT' => '-8'
        ),
        'AUS' => array(
            'AT' => '+10',
            'NW' => '+10',
            'NT' => '+10.5',
            'QL' => '+10',
            'SA' => '+9.5',
            'TA' => '+10',
            'VI' => '+10',
            'WA' => '+8'
        )
    ];


    $query_occasion = "INSERT INTO `tbl_calls_occasion` (
        `order_id`,
        `number`,
        `country`,
        `state`,
        `gmt_offset`
    ) VALUES ";

    $occasion_includes = [];

    foreach ($file as $item) {
        set_time_limit (60);
        if($item[1] == '')
            break;


        $only_d = preg_replace("/\D/", '', $item[9]);
        if (isset($only_d)) {
            $occasion_includes[] = "(
                " . $item[1] . ",
                '" . $only_d . "',
                '" . $item[8] . "',
                '" . $item[7] . "',
                '" . ($gmt_offsets[strtoupper($item[8])][strtoupper($item[7])] ?? 0) . "'
            )";
        }
    }

    $query_occasion .= implode(',', $occasion_includes);
    echo $query_occasion . "\n";
    
    $result = $mysqli->query($query_occasion);
    if (!$result) {
        printf(__LINE__ . "query: %s\n", $query_occasion);
        printf(__LINE__ . "Error message: %s\n", $mysqli->error);
    }
    $mysqli->close();
} catch (Exception $e) {
    print_r($e->getMessage());
    print_r($e->getLine());
}