
<?php

ini_set("display_errors", "1");
error_reporting(E_ALL);

function create_url($string) {
    $string = str_replace(' ', '-', trim($string)); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($string)); // Removes special chars.
}

if (isset($_POST['submit']))
{
    include_once '../configuration.php';
    require_once "simplexlsx.class.php";
    $xlsx = new SimpleXLSX($_FILES['location_list_file']['tmp_name']);
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];
    if (!$mosConfig_host)
    {
        die('no config');
    }

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);


    $columns = array('city','province');

    $province_numbers = array(
        'nsw'=>'(02) 7201 9070',
        'act'=>'(02) 7201 9070',
        'wa'=>'(08) 6255 5629',
        'tas'=>'(03) 8820 5724',
        'qld'=>'(07) 3608 5316',
        'sa'=>'(08) 7130 3981',
        'nt'=>'1-800-905-147',
        'vic'=>'(03) 8820 5724'
    );

    $list = array();

    if ($xlsx->rows($sheet_num)) {
        foreach ($xlsx->rows($sheet_num) as $m=>$r) {
            $a = array();
            for ($i = 0; $i < count($columns); $i++) {
                $a[$columns[$i]] = $mysqli->escape_string(trim($r[$i]));
            }
            $a['url']= create_url($a['city']);
            $a['enable_location']=1;
            if(isset($province_numbers[strtolower($a['province'])])){
                $a['telephone']= $province_numbers[strtolower($a['province'])];
            }else{
                $a['telephone']='1-800-905-147';
            }
            $list[]=$a;
        }
    }

    $query_add ="INSERT INTO  tbl_landing_pages (city,province,url,telephone,enable_location) VALUES ";
    $k= 0;
    foreach($list as $l){
        $l['city'] = iconv("utf-8", "utf-8//ignore", $l['city']);
        $l['url'] = iconv("utf-8", "utf-8//ignore", $l['url']);
        $l['url'] = str_replace(" ","-",str_replace("'","",strip_tags(strtolower($l['url']))));

        $result = $mysqli->query("SELECT * FROM tbl_landing_pages WHERE url='".$l['url']."' limit 1");

        if( $result->num_rows == 0){

            $k++;
            $query_add .="('".$l['city']."','".$l['province']."','".$l['url']."','".$l['telephone']."','".$l['enable_location']."'),";

        }

    }
    $query_add = rtrim($query_add,',');
    if($k>0){
        $mysqli->query($query_add);
        $mysqli->close();
        echo "we inserted $k new landings";
    }else{
        echo "no new items to add";
    }


}
else
{
    ?>
    <html>
    <head>

    </head>
    <body>
    <form action="?" enctype="multipart/form-data" method="post">
        Upload Location List File<br/><br/>
        <input type="file" name="location_list_file">
        <input type="submit" name="submit" value="Upload">
    </form>
    </body>
    </html>
    <?php
}
?>
