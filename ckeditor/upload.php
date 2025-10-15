<?php

include "../configuration.php";

function getex($filename) {
    return end(explode(".", $filename));
}

if ($_FILES['upload']) {
    if (($_FILES['upload'] == "none") OR ( empty($_FILES['upload']['name']))) {
        $message = "we did not choose file";
    } else if ($_FILES['upload']["size"] == 0 OR $_FILES['upload']["size"] > 2050000) {
        $message = "wrong file size";
    } else if (($_FILES['upload']["type"] != "image/jpeg") AND ( $_FILES['upload']["type"] != "image/jpeg") AND ( $_FILES['upload']["type"] != "image/png")) {
        $message = "you can upload only images JPG or PNG.";
    } else if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
        $message = "there was an error try again";
    } else {
        $name = rand(1, 1000) . '-' . md5($_FILES['upload']['name']) . '.' . getex($_FILES['upload']['name']);
        ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass);
        $full_path = 'http://' . $mosConfig_email_sender_ftp_host . '/bloomex.com.au/com_email_sender/' . $_FILES['upload']['name'];
        $message = "file " . $_FILES['upload']['name'] . " uploaded";
    }
    $callback = $_REQUEST['CKEditorFuncNum'];
    echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("' . $callback . '", "' . $full_path . '", "' . $message . '" );</script>';
}

function ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass) {
    $ftp = ftp_connect($mosConfig_email_sender_ftp_host);

// login with username and password 
    if (ftp_login($ftp, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass)) {
        ftp_pasv($ftp, true);


        $trackErrors = ini_get('track_errors');
        ini_set('track_errors', 1);
        if (!@ftp_put($ftp, "/bloomex.com.au/com_email_sender/" . $_FILES['upload']['name'], $_FILES['upload']['tmp_name'], FTP_BINARY)) {
            // error message is now in $php_errormsg
            $msg = $php_errormsg;
            ini_set('track_errors', $trackErrors);
            throw new Exception($msg);
            die("error while uploading file");
        } else {
            $message = "";
        }
    } else {
        die("Could not login to FTP account");
    }

    if (!empty($message)) {
        echo $message;
    }

    ftp_close($ftp);
}

?>