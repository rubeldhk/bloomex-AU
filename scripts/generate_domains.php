<?php
include ("../configuration.php");

if (!$mosConfig_host)
    die('no config');
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) {
    die('Could not select database: ' . mysql_error());
}

if($_FILES && $_FILES['fileToUpload']['size']>0){
    require_once "simplexlsx.class.php";
    $xlsx = new SimpleXLSX( $_FILES['fileToUpload']['tmp_name'] );

    list($num_cols, $num_rows) = $xlsx->dimension(2);
    $k=0;
    foreach( $xlsx->rows(2) as $r ) {
        $a=array();
        for( $i=0; $i < $num_cols; $i++ ){
            $a[]=mysql_real_escape_string($r[$i]);
        }
        $sql_check = "SELECT *
                        FROM `company_groups`
                        WHERE `company_domain` LIKE '".$a[0]."'";
        $result_check = mysql_query($sql_check, $link);
        if (!$result_check) {
            echo $sql_check;
            die(__LINE__ . 'Select error: ' . mysql_error());
        }
        if(mysql_num_rows($result_check)==0){

            $sql_insert = "INSERT INTO company_groups  (company_name, company_domain,company_group_id) VALUES ('" . $a[1] . "','" . $a[0] . "','16') ";
            $result_insert = mysql_query($sql_insert, $link);
            if (!$result_insert) {
                echo $sql_insert;
                die(__LINE__ . 'Select error: ' . mysql_error());
            }
            $k++;
        }

    }


    echo 'Added  '. $k .' new domens ';

}

?>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload" name="submit">
</form>
<?php