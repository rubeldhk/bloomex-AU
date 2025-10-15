<?php
define('_VALID_MOS', 'true');
include "configuration.php";

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

if (isset($_COOKIE['MC_code']) AND !empty($_COOKIE['MC_code'])) {
    $query = "SELECT 
        `c`.`id`,
        `c`.`type`
    FROM `tbl_code_for_restricted_categories` AS `c`
    WHERE 
        `c`.`code`='".$mysqli->escape_string($_COOKIE['MC_code'])."' 
    ";
    $res = $mysqli->query($query);
    if (($res->num_rows > 0) || $_COOKIE['MC_code'] == 'MCD32XX') {
        if ($_COOKIE['MC_code'] == 'MCD32XX') {
            $obj = new stdClass();
            $obj->code = "MCD32XX";
            $obj->type = "389";
        }
        else {
            $obj = $res->fetch_object();
        }

        header("Location: " . $mosConfig_live_site . "/mcdonalds-monopoly");
        die();
    }
}

if ($_POST['return_url']) {
    $return_url = $_POST['return_url'];
} elseif ($_GET['return_url']) {
    $return_url = $_GET['return_url'] . "&Itemid=" . $_GET['Itemid'] . "&category_id=" . $_GET['category_id'] . "&product_id=" . $_GET['product_id'] . "&lang=" . $_GET['lang'] . "&page=" . $_GET['page'];
} else {
    $return_url = 'https://bloomex.com.au';
}

$placeholder = 'Enter Activation Code Here';
$checkcode = 'Verify Code';

$codeErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["code"])) {
        $codeErr = "code is required";
    } else {
        $code =$mysqli->escape_string(test_input($_POST["code"]));
        // check if name only contains letters and whitespace
        if (empty($codeErr)) {

            $query = "SELECT 
                `c`.`id`,
                `c`.`code`,
                `c`.`type`
            FROM `tbl_code_for_restricted_categories` AS `c`
            WHERE 
                `c`.`code`='" . $code . "' 
                AND 
                `c`.`activation_time`=0
                AND
                `c`.`expiration_date`>'" . date('Y-m-d') . "'
            ";
            $res = $mysqli->query($query);
            if (($res->num_rows > 0) || $code == 'MCD32XX') {
                if ($code == 'MCD32XX') {
                    $obj = new stdClass();
                    $obj->code = "MCD32XX";
                    $obj->type = "389";
                } else {
                    $obj = $res->fetch_object();
                    $queery = "UPDATE `tbl_code_for_restricted_categories`
                SET 
                    `activation_time`='1'
                WHERE `id`=" . $obj->id . "
                ";
                }

                $t = time() + 60 * 60 * 24 * 30;
                $mysqli->query($query);
                setcookie('MC_code', $obj->code, $t, '/; SameSite=Strict',"",true,true);
                //    var_dump(array('MC_code', $obj->code, $t, "/"));
                header("Location: " . $mosConfig_live_site . "/mcdonalds-monopoly");
                die();
            } else {
                $codeErr = "Wrong code. try again";
            }
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$mysqli->close();
?>

<!DOCTYPE HTML>
<html>
<head>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="login">
        <img class="mcdonalds_icon" src="/images/mc_logo.png">
        <input type="text" name="code" placeholder=" <?php echo $placeholder; ?>">
        <input type="submit" value=" <?php echo $checkcode; ?>">
        <input type="hidden" name="return_url" value="<?php echo $return_url; ?>">
        <span class="error"><?php echo $codeErr; ?></span>
    </div>
    <div class="shadow"></div>
</form>


<style>
    html { height: 100% }
    ::-moz-selection { background: #fe57a1; color: #fff; text-shadow: none; }
    ::selection { background: #fe57a1; color: #fff; text-shadow: none; }
    body { background: #B0C4DE}
    .login {
        background: #ff0000;
        border: 1px solid #42464b;
        border-radius: 6px;
        height: 380px;
        margin: 20px auto 0;
        width: 420px;
    }
    .mcdonalds_icon{
        margin: 10px auto;
        display: block;
        width: 200px;
    }
    .error{
        height: 36px;
        display: block;
        color: white;
        margin-top: 30px;
        text-align: center;
        font-size: 23px;
    }
    input[type="text"] {
        background: url('http://i.minus.com/ibhqW9Buanohx2.png') center left no-repeat, linear-gradient(top, #d6d7d7, #dee0e0);
        border: 1px solid #a1a3a3;
        border-radius: 4px;
        box-shadow: 0 1px #fff;
        box-sizing: border-box;
        color: #696969;
        height: 39px;
        margin: 31px 0 0 29px;
        transition: box-shadow 0.3s;
        width: 360px;
        border-radius: 10px;
    }
    input[type="text"]:focus {
        box-shadow: 0 0 4px 1px rgba(55, 166, 155, 0.3);
        outline: 0;
    }

    input[type="submit"] {
        width:360px;
        height:35px;
        display:block;
        font-family:Arial, "Helvetica", sans-serif;
        font-size:16px;
        font-weight:bold;
        color:#fff;
        text-decoration:none;
        text-transform:uppercase;
        text-align:center;
        text-shadow:1px 1px 0px #37a69b;
        padding-top:6px;
        margin: 29px 0 0 29px;
        position:relative;
        cursor:pointer;
        border: none;
        background-color: #37a69b;
        background-image: linear-gradient(top,#3db0a6,#3111);
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius:5px;
        box-shadow: inset 0px 1px 0px #2ab7ec, 0px 5px 0px 0px #497a78, 0px 10px 5px #999;
        border-radius: 10px;
    }

    .shadow {
        background: #000;
        border-radius: 12px 12px 4px 4px;
        box-shadow: 0 0 20px 10px #000;
        height: 12px;
        margin: 30px auto;
        opacity: 0.2;
        width: 410px;
    }


    input[type="submit"]:active {
        top:3px;
        box-shadow: inset 0px 1px 0px #2ab7ec, 0px 2px 0px 0px #31524d, 0px 5px 3px #999;
    }
</style>


</body>
</html>