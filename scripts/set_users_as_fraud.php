
<?php

ini_set("display_errors", "1");
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    include_once '../configuration.php';

    if (!$mosConfig_host) {
        die('no config');
    }

    $link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

    if (!$link) {
        die('Could not connect: ' . mysqli_error());
    }

    if (!mysqli_select_db($link, $mosConfig_db)) {
        die('Could not select database: ' . mysqli_error());
    }



    function csv_to_array($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $data[] = $row[0];
            }
            fclose($handle);
        }

        return $data;
    }



    $orders_list_file = csv_to_array($_FILES['orders_list_file']['tmp_name']);
    if($orders_list_file){

        $query_users = "SELECT GROUP_CONCAT(DISTINCT  user_id) as users FROM jos_vm_orders WHERE order_id in (".implode(",", $orders_list_file).")";
        $result_users = mysqli_query($link, $query_users);
        $users_str = $result_users->fetch_assoc();

        echo "<pre>";print_r($query_users);
        echo "<pre>";print_r($users_str);

        $query_update = "UPDATE jos_users SET block='1' WHERE id in (".$users_str['users'].")";
        mysqli_query($link, $query_update);

        echo "<pre>";print_r($query_update);



        $query = "INSERT INTO tbl_users_block_history (user_id, username, reason,block,datetime)
        SELECT DISTINCT  user_id,'script','chargeback','1',now() FROM jos_vm_orders WHERE order_id in (".implode(",", $orders_list_file).")";
        mysqli_query($link, $query);

        echo "<pre>";print_r($query);
        echo "<pre>";print_r($orders_list_file);
    }



} else {
    ?>
    <html>
        <head>

        </head>
        <body>
            <form action="?" enctype="multipart/form-data" method="post">
                Upload File<br/><br/>
                <input type="file" name="orders_list_file">
                <input type="submit" name="submit" value="Upload">
            </form>
        </body>
    </html>
    <?php
}
?>
