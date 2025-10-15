
        <?php
        ini_set("display_errors", "1"); 
        error_reporting(E_ALL);
        
        if (isset($_POST['submit']))
        {
            include_once '../configuration.php';

            if (!$mosConfig_host)
            {
                die('no config');
            }

            $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

            if (!$link) 
            {
                die('Could not connect: ' . mysql_error());
            }

            if (!mysql_select_db($mosConfig_db)) 
            {
                die('Could not select database: ' . mysql_error());
            }

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

            $fastway_file = readExelFile($_FILES['fastway_file']['tmp_name']);

            $sql = mysql_query("SELECT `o`.`order_id`,  `h1`.`date_added`, `h1`.`comments`
            FROM `jos_vm_order_history` AS `h1`
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`h1`.`order_id`
            LEFT JOIN `jos_vm_order_history` AS `h2` ON `h2`.`order_id`=`h1`.`order_id` AND `h2`.`order_status_code`='J'
            WHERE `h1`.`order_status_code`='H' 
            AND (
            (`h1`.`date_added`>`h2`.`date_added`) OR ( `h2`.`date_added` IS NULL )
            )
            AND
            `h1`.`date_added` BETWEEN '".mysql_real_escape_string($_POST['date_start'])." 00:00:00' and '".mysql_real_escape_string($_POST['date_end'])." 23:59:59'
            ORDER BY `h1`.`date_added`");
            
            $our_array = array();
            
            while ($out = mysql_fetch_array($sql))
            {
                $our_array[$out['order_id']] = $out;
            }

            $table_array = array();
            
            foreach ($fastway_file as $fastway_line)
            {
                $order_id = (int)$fastway_line[7];
                
                if ($order_id > 0)
                {
                    if (array_key_exists($order_id, $our_array))
                    {
                        $our_have = 'Y';
                        $our_have_n = 1;
                        $our_date = $our_array[$order_id]['date_added'];
                        $our_comment = $our_array[$order_id]['comments'];
                    }
                    else
                    {
                        $our_have = 'N';
                        $our_have_n = 0;
                        $our_date = '';
                        $our_comment = '';
                    }
                    
                    $table_array[] = array(
                        'order_id' => $order_id, 
                        'creation_date' => $fastway_line[2], 
                        'printed_date' => $fastway_line[3], 
                        'label' => $fastway_line[6], 
                        'our_have' => $our_have, 
                        'our_date' => $our_date, 
                        'our_comment' => $our_comment,
                        'our_have_n' => $our_have_n
                        );
                }   
            }
            
            function cmp($a, $b)  
            { 
                return strnatcmp($a["label"], $b["label"]); 
            } 
            
            usort($table_array, "cmp"); 
            /*
            ?>
                    
            <table border="1" cellpadding="10">
                <tr>
                    <th>
                        Order id
                    </th>
                    <th>
                        Create date (fastway)
                    </th>
                    <th>
                        Date printed (fastway)
                    </th>
                    <th>
                        Label numbers (fastway)
                    </th>
                    <th>
                        We have Y/N
                    </th>
                    <th>
                        Date
                    </th>
                    <th>
                        Comment
                    </th>
                </tr>
            <?php
             * 
             */
                //include './Classes/PHPExcel.php';
                
                $phpexcel = new PHPExcel(); 
                $page = $phpexcel->setActiveSheetIndex(0);
                
                $page->setCellValue('A1', 'Order id');
                $page->setCellValue('B1', 'Create date (fastway)'); 
                $page->setCellValue('C1', 'Date printed (fastway)');
                $page->setCellValue('D1', 'Label numbers (fastway)');
                $page->setCellValue('E1', 'We have Y/N'); 
                $page->setCellValue('F1', 'Date');
                $page->setCellValue('G1', 'Comment');
                
            
                //$data = "Order id \t Create date (fastway) \t Date printed (fastway) \t Label numbers (fastway) \t We have Y/N \t Date \t Comment";
                
                $i = 2;
                
                foreach ($table_array as $table_tr)
                {
                    
                    //$data .= "".$table_tr['order_id']." \t  ".$table_tr['creation_date']." \t ".$table_tr['printed_date']." \t ".$table_tr['label']." \t ".$table_tr['our_have']." \t ".$table_tr['our_date']." \t ".$table_tr['our_comment'].""
                      
                    
                    $page->setCellValue('A'.$i, $table_tr['order_id']);
                    $page->setCellValue('B'.$i, $table_tr['creation_date']); 
                    $page->setCellValue('C'.$i, $table_tr['printed_date']);
                    $page->setCellValue('D'.$i, $table_tr['label']);
                    $page->setCellValue('E'.$i, $table_tr['our_have']); 
                    $page->setCellValue('F'.$i, $table_tr['our_date']);
                    $page->setCellValue('G'.$i, $table_tr['our_comment']);
                    
                    $i++;
                    /*
                    ?>
                    <tr>
                        <td>
                            <?php echo $table_tr['order_id']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['creation_date']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['printed_date']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['label']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['our_have']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['our_date']; ?>
                        </td>
                        <td>
                            <?php echo $table_tr['our_comment']; ?>
                        </td>
                    </tr>
                    <?php
                     * 
                     */
                }
                $page->setTitle('fastway');
                /*
            ?>
            </table>
            <?php
            */
            
            ob_end_clean();
            
            $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');                                                                                                           

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename=fastway_test.xlsx');
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
                Date range:
                <input type="date" name="date_start"> <input type="date" name="date_end"><br/>
                Fastway report file<br/>
                <input type="file" name="fastway_file"><br/><br/>
                <input type="submit" name="submit" value="go">
            </form>
    </body>
</html>
        <?php
        }
        ?>
