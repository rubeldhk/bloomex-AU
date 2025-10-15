<?php
   $numbers = ''; 
if (isset($_FILES['file_numbers']) && $_FILES['file_numbers']['size']!=0) {
    $dir = "../../../scripts/simplexlsx.class.php";
    require_once $dir;
    $xlsx = new SimpleXLSX( $_FILES['file_numbers']['tmp_name'] );
    $parsed_result_num = $xlsx->rows(2);
        if($parsed_result_num){
            $count = count($parsed_result_num);
                foreach($parsed_result_num as $k=>$em){
                    if($k==$count-1){
                        $numbers .=  $em[0];
                    }else{
                        $numbers .=  $em[0].",\xA";
                    }
                }
        }
    }
    die($numbers);

