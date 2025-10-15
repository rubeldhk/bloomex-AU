<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en-US"
    lang="en-US">
    <body>
        <?php
        require_once 'configuration.php';

        $manifest_id = $_REQUEST['manifest_id'];
        $sender = $_REQUEST['sender'];

        $cfg = new FastWayCfg();

        $mysqli = new mysqli($cfg->host, $cfg->user, $cfg->pw, $cfg->db);
        $mysqli->set_charset('utf8');

        $curl_options = array(
            CURLOPT_URL => $cfg->api_host . 'closemanifest',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'UserID=' . $cfg->user_id . '&ManifestID=' . $manifest_id . '&api_key=' . $cfg->api_key,
            CURLOPT_HTTP_VERSION => 1.0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        );

        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $result_curl = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Error : ' . curl_error($curl);
            die();
        } else {
            $rs = json_decode($result_curl, true); // json to object
        }

        if ($rs['error']) {
            echo ('Error : ' . $rs['error']);
            die();
        } else {

            $file = $rs['result']['pdf'];
            $newfile = 'manifest/' . $cfg->user . '/runsheet_' . $manifest_id . '.pdf';

            if (!copy($file, $newfile)) {
                echo "Eror when copying file $file...\n";
                die();
            }
            date_default_timezone_set('Australia/Sydney');
            $timestamp = time(); 
            $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
            $q = "INSERT INTO `jos_vm_order_history`
            (
                `order_id`,
                `order_status_code`,
                `date_added`,
                `comments`,
                `user_name`
            ) 
            VALUES (
                '$manifest_id', 
                '" . $cfg->status_cancel_fast_label . "', 
                '$mysqlDatetime', 
                '".htmlspecialchars("Manifest_ID: " . $manifest_id, ENT_QUOTES)."', 
                '$sender'
            )";
            $mysqli->query($q);
            ?>
            <h2>Manifest closed</h2>
            <br><center><a href="javascript: w=window.open('<?php print $file; ?>'); w.print(); ">Print runsheet.</a></center>
                <?php
            }
            ?>
    </body>
</html>

<?php
$mysqli->close();
?>