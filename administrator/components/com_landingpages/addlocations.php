<?php

if (isset($_FILES['file_data']) && $_FILES['file_data']['size'] != 0) {
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
    
    require_once ($mosConfig_absolute_path.'/includes/PHPExcel.php');
    $Reader = PHPExcel_IOFactory::createReaderForFile($_FILES['file_data']['tmp_name']);
    $Reader->setReadDataOnly(true); 
    $objXLS = $Reader->load($_FILES['file_data']['tmp_name']);


    

        $n = 2;
    do {
        $n ++;
    } while ($objXLS->getSheet(0)->getCellByColumnAndRow(0, $n)->getValue() != '');
    //get result
    for ($i = 0; $i < 5; $i++) {
        $columns_names[] = $objXLS->getSheet(0)->getCellByColumnAndRow($i, 1)->getValue();

        for ($j = 2; $j < $n; $j++) {
            $sheet_column_data[$objXLS->getSheet(0)->getCellByColumnAndRow($i, 1)->getValue()][] = $objXLS->getSheet(0)->getCellByColumnAndRow($i, $j)->getValue();
        }
    }

    $data = array();
    for ($i = 0; $i < count($sheet_column_data[$columns_names[0]]); $i++) {
        $data[$i] = array();
        foreach ($sheet_column_data as $key => $val) {
            $data[$i][] = $val[$i];
        }
    }
    $objXLS->disconnectWorksheets();
    unset($objXLS);


    if(count($data) == 0){
        die('File Is Empty. Please Upload Another File');
    }
  
    

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');
    
    $i = 0;
    foreach($data as $d){
        
        $city = $d[0];
        $url = strtolower(preg_replace("/[^A-Za-z0-9-]/", '', $d[1]));
        $province = $d[2];
        $telephone = $d[3];
        $location_postcode = $d[4];
        $query_check = "SELECT city FROM tbl_landing_pages WHERE url='".$url."'";
        
        $result = $mysqli->query($query_check);

        if (!$result) {
            die('Select error: ' . $mysqli->error);
        }

        if ($result->num_rows == 0) {
            
            $query = "INSERT INTO `tbl_landing_pages` 
            (
                `city` ,
                `province` ,
                `telephone` ,
                `lang` ,
                `url` ,
                `enable_location` ,
                `location_address` ,
                `location_country` ,
                `location_postcode` ,
                `location_telephone` ,
                `category_id`
            )
            VALUES ( 
                '".$city."',
                '".$province."',
                '".$telephone."',
                '1', 
                '".$url."', 
                '1', 
                '".$city."', 
                'Australia', 
                '".$location_postcode."', 
                '".$telephone."', 
                '81'
            );";
            
            $result = $mysqli->query($query);
            
            if (!$result) {
                die('Select error: ' . $mysqli->error);
            }
            else{
                $i++;
            }
        }
        $result->close();
        $mysqli->close();
    }
    if ($i > 0) {
        die('We Add  '.$i.' New Locations');
    }
    else {
        die('Locations Have Already Exist. Please Upload Another File ');
    }
}

function make_url_format($str){
    
}