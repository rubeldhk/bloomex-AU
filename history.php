<?php
 require_once('fedexconfig.php');
        $cfg = new JConfig();
        $link = mysql_connect($cfg->host, $cfg->user, $cfg->password);

        function filter_($data) {
            $data = trim(htmlentities(strip_tags($data)));
            if (get_magic_quotes_gpc())
                $data = stripslashes($data);
            $data = mysql_real_escape_string($data);
            return $data;
        }
 ?>
<!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml" 
    xml:lang="en-US"
    lang="en-US">
    <head>
        <link rel="icon" 
              type="image/png" 
              href="resourses/corpLogo.gif" />
        <link rel="stylesheet" href="resourses/style_t.css" />
        <script src="resourses/jquery.min.js"></script> <!-- 1.4.4 -->
        <script type="text/javascript" src="resourses/jquery.printElement.min.js"></script>
        </head>
    <body>
  <?php      
        $cfg = new JConfig();
        $link = mysql_connect($cfg->host, $cfg->user, $cfg->password);
        if (!$link) {
            echo 'Could not connect: ' . mysql_error();
            die();
        }
        $id=$_GET['id'];
        mysql_select_db($cfg->db, $link);
        $q = "SELECT * FROM jos_users WHERE id='$id'";
        $result = mysql_query($q);
        if (!$result) {
            echo $q . "<br/>";
            echo 'Invalid query: ' . mysql_error();
            die();
        }
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $history = $row;
        }
        $q = "SELECT * from jos_vm_order_user_info WHERE user_id='" . $order['user_id'] . "' AND order_id='" . $order['order_id'] . "' AND address_type = 'ST' ORDER BY address_type ASC";
        $result = mysql_query($q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysql_error());
        }
        while ($row = mysql_fetch_object($result)) {
            $shipping = $row;
            echo $shipping->user_name." ".$shipping->date." ".$shipping->coment;
            echo "<br>";
        }
        
      
?>
    </body>
</html>