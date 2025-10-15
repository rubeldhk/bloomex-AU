
<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

if (isset($_POST['submit']))
{
    include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');

    function readExelFile($filepath)
    {
        include './Classes/PHPExcel.php';
        $ar = array();
        $inputFileType = PHPExcel_IOFactory::identify($filepath);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filepath);
        $ar = $objPHPExcel->getActiveSheet()->toArray();
        return $ar;
    }

    $ordersList = readExelFile($_FILES['ordersList']['tmp_name']);
    if(!$ordersList){
        die('wrong file');
    }
    $table_array = array();
    foreach ($ordersList as $m=>$line)
    {
        $order_id = (int)$line[0];

        $sql = "SELECT zip FROM `jos_vm_order_user_info`
            WHERE `address_type`='ST' AND order_id= $order_id";

        $result = $mysqli->query($sql);


        if ($result->num_rows > 0) {
            $obj = $result->fetch_object();

            $table_array[$m] = $line;
            $table_array[$m][] = $obj->zip;
        }
    }


    $phpexcel = new PHPExcel();
    $page = $phpexcel->setActiveSheetIndex(0);
    $i = 1;
    $alphabet = range('A', 'Z');
    foreach ($table_array as $k=>$table_tr)
    {
        $p = 0;

        while($p < count($table_tr))
        {
            $page->setCellValue($alphabet[$p].$i, $table_tr[$p]);
            $p++;
        }

        $i++;

    }
    $page->setTitle('Orders List');

    ob_end_clean();

    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');

    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=orders_list.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');


}
else
{
    ?>
    <html>
    <head>

    </head>
    <body>
    <form action="?" enctype="multipart/form-data" method="post">
        Upload file
        <input type="file" name="ordersList">
        <input type="submit" name="submit" value="upload">
    </form>
    </body>
    </html>
    <?php
}
?>
