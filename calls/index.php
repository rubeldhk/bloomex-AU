<?php
if (substr($_SERVER['HTTP_HOST'], 0, 3) != "adm") {
    $actual_link = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('Location: ' . $actual_link);
}
include_once './config.php';

if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    setcookie('extension', '');
    $mysqli->close();
    header('Location: ?');
    exit;
}

if (isset($_POST['extension'])) {

    $query = "SELECT 
        *  
    FROM  `extensions` 
    WHERE  
        `ext`='".(int)$_POST['extension']."'
        AND 
        `access`=1
    ";
    
    $result = $mysqli->query($query);
   
    if ($result->num_rows > 0) {
        $obj = $result->fetch_object();
        
        setcookie('extension', (int)$_POST['extension'], time() + 3600 * 10);
        setcookie('access_abandonment', (int)$obj->abandonment, time() + 3600 * 10);
        setcookie('access_occassion', (int)$obj->occassion, time() + 3600 * 10);
        setcookie('call_back', (int)$obj->call_back, time() + 3600 * 10);
        setcookie('call_type', $_POST['call_type'], time() + 3600 * 10);
        setcookie('project', $_POST['project'], time() + 3600 * 10);

    }
    
    $result->close();
    $mysqli->close();
    
    header('Location: ?not_allowed_extension');
    exit;
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="./css/style.css" media="all" />
        <script src="./js/jquery-1.12.1.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../templates/bloomex7/css/bootstrap.min.css" media="all" />
        <title>Auto dial</title>
    </head>

    <body>
        <div class="wrapper">
            <?php
            if (!isset($_COOKIE['extension'])) {
                ?>
                <div class="header_div">
                    CALLING INFO
                </div>
                <form action="?" method="post">
                    <div class="extension_div">
                        <p>Your extension:</p>
                        <input type="text" class="form-control" name="extension" />
                        <p>Site:</p>
                        <select name="project" class="form-control">
                            <option value="AUS">bloomex.com.au</option>
                            <option value="NZL">bloomex.co.nz</option>
                            <option value="BOTH">bloomex.com.au / bloomex.co.nz</option>
                        </select>
                        <p>Calls type:</p>
                        <select name="call_type" class="form-control">
                            <option value="abandonment">Abandonment</option>
                            <option value="ocassion">Ocassion</option>
                        </select>

                        <input type="submit" value="OK" class="extension_button"/>
                    </div>
                </form>

                <?php
            } else {
                ?>
                <script src="./js/functions.js?rel=18122017_2"></script>
                <div class="header_div_left">

                </div>
                <div class="header_div_right">
                    Extension: <?php echo $_COOKIE['extension']; ?>
                    <a href="?logout" style=""><img align="right" style="margin-right: 30px;" src="./images/logout1.png"></a>
                </div>
                <div style="clear: both"></div>
                <div class="no_calling_div">NO CALLING</div>
                <div class="no_loader_div" style="display: none;"><img src="./images/loader.gif" alt="loading..." /></div>
                <div class="left_div" style="display: none;">
                    <div id="countdown"><span id="countdown_text">NEXT CALL IN</span><span id="countdown_seconds"></span></div>
                    <div class="in_left_div" id="left_div">

                    </div>
                </div>
                <div class="right_div" style="display: none;">
                    <div class="in_right_div" id="right_div">

                    </div>
                </div>
                <div style="clear: both"></div>
                <?php
            }
            ?>
        </div> 
    </body>
</html>
