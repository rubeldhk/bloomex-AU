<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

if (isset($_POST['submit']))
{
    include_once '../configuration.php';
    require_once "simplexlsx.class.php";
    $xlsx = new SimpleXLSX($_FILES['user_list_file']['tmp_name']);
    if (!$mosConfig_host)
    {
        die('no config');
    }

    $link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

    if (!$link)
    {
        die('Could not connect: ' . mysqli_error($link));
    }

    if (!mysqli_select_db($link,$mosConfig_db))
    {
        die('Could not select database: ' . mysqli_error($link));
    }
    mysqli_set_charset($link,'utf8');
    $list = array();
    if ($xlsx->rows(2)) {
        foreach ($xlsx->rows(2) as $m => $r) {
            if($m<2){
                continue;
            }
            if($r[7]!=''){

                if(!in_array($r[7],$list)){
                    $list[] = $r[7];
                }
            }
        }
    }

    $where_in = "'".implode("','",$list)."'";
    $where_in =  "'" . implode("','", array_map(array($link, 'real_escape_string'), $list)) . "'";
    $query_get_user_id_list="SELECT u.id,u.email 
from  jos_users as u
left join jos_vm_user_options as o on o.user_id = u.id where (u.email in ($where_in) OR u.username in ($where_in)) and o.id is null";
    $users_id_resources =  mysqli_query($link,$query_get_user_id_list);

    $user_id_list = array();
    $user_updated= array();

    if( mysqli_num_rows($users_id_resources)>0) {
        while ($our = mysqli_fetch_array($users_id_resources)) {
            $user_id_list[]= $our[0];
            $user_updated[]=$our[1];
        }
    }
    $k= 0;
    if($user_id_list){
        $query_add ="INSERT INTO  jos_vm_user_options (user_id,corp_stakeholder) VALUES ";
        foreach($user_id_list as $user_id){
            $k++;
            $query_add .="('".$user_id."','1'),";
        }
        $query_add = rtrim($query_add,',');
        $result = mysqli_query($link,$query_add);
        if (!$result) {
            echo $result;
            echo('Insert Error: ' . mysqli_error($link));
            mysqli_close($link);
            die;
        }
    }
    mysqli_close($link);
    echo "we Updated $k users<br>";
    echo "<pre>".print_r($user_updated)."</pre>";

}
else
{
    ?>
    <html>
    <head>

    </head>
    <body>
    <form action="?" enctype="multipart/form-data" method="post">
        Upload user List File<br/><br/>
        <input type="file" name="user_list_file">
        <input type="submit" name="submit" value="Upload">
    </form>
    </body>
    </html>
    <?php
}
?>
