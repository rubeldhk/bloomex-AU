<?php

function showscv ($rows, $title) {

    header("Content-type:    text/csv charset=utf-8 ");
    header("Content-Disposition: attachment; filename=jeports.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    $out = fopen("php://output", 'w');

  $flag = false;
 foreach ($rows as $row) {     
    if(!$flag) {

      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;

}
function notempty($var) {
if($var=="0" || !empty($var)){
        return true;
    }else{
        return false;
    }
    
}
  function cleanData(&$str)
  {
      $str = html_entity_decode ($str);
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

if (isset($_FILES['file_numbers']) && $_FILES['file_numbers']['size']!=0) {
    require_once ('../configuration.php');
    require_once ($mosConfig_absolute_path.'/includes/PHPExcel.php');
    $Reader = PHPExcel_IOFactory::createReaderForFile($_FILES['file_numbers']['tmp_name']);
    $Reader->setReadDataOnly(true); 
    $objXLS = $Reader->load($_FILES['file_numbers']['tmp_name']);


        $n = 0;
    do {
        $n ++;
    } while (notempty($objXLS->getSheet(0)->getCellByColumnAndRow(0, $n)->getValue()));


        for ($j = 1; $j < $n; $j++) {
      $sheet_column_data[] = $objXLS->getSheet(0)->getCellByColumnAndRow(0, $j)->getValue();
        }

    $objXLS->disconnectWorksheets();
    unset($objXLS);

   
    if(count($sheet_column_data) == 0){
        die('File Is Empty. Please Upload Another File');
    }
     
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }
    if (!mysql_select_db($mosConfig_db)) {
        die('Could not select database: ' . mysql_error());
    }
    $output = array();
    
       foreach($sheet_column_data as $k=>$d){
        $query = "SELECT id,email FROM `jos_users`  WHERE id='".$d."'";
        
        $result = mysql_query($query, $link);
        if (!$result) {
            die('Select error: ' . mysql_error());
        }

        if (mysql_num_rows($result) > 0) {
            $output[] = mysql_fetch_assoc($result);
        }else{
            $output[] = Array("id" => $d,"email" => "");
            
        }
       }   
showscv($output, 'Report');    
    }  
?>
