<?php
include "configuration.php";
 $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }
    if (!mysql_select_db($mosConfig_db)) {
        die('Could not select database: ' . mysql_error());
    }
    
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST['user_id']) && isset($_REQUEST['user_email'])){
$user_id = $_REQUEST['user_id']?$_REQUEST['user_id']:'';
$user_id_md = $_REQUEST['user']?$_REQUEST['user']:'';
$user_email = $_REQUEST['user_email']?$_REQUEST['user_email']:'';
$action = $mosConfig_live_site."/unsubscribe.php";


if(md5($user_id."BLOOMEX") == $user_id_md){

    
        ?>
                    <form action="<?php echo $action  ; ?>" method='post'>
                        <div class="paragraph" style="width: 644px; margin: 5px 3px; font-family: Arial; font-size: 12px; color: #000000; font-weight: normal; text-decoration: none; font-style: normal; ">As a Bloomex account holder, you are eligible for various benefits and promotions, of which you may be notified via e-mail.</div>
                        <input type="hidden" name="user_email" value="<?php echo $user_email  ; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $user_id  ; ?>">
                        <p>please tell us why do you want to unsubscribe </p>
                        <textarea rows="4" cols="50" name='comment'></textarea><br><br>
                       <input value="unsubscribe" type="submit" style=" background-color: red;padding: 10px;border-radius: 10px;cursor: pointer;color: white;text-decoration: none;font-weight: bold;">
                       <a href='<?php echo $mosConfig_live_site  ; ?>'><input value="re subscribe" type="button" style=" background-color: red;padding: 10px;border-radius: 10px;cursor: pointer;color: white;text-decoration: none;font-weight: bold;"></a>
                    </form>
                            <?php
    
                   
    
        }else{

            ?>

        <P>   we are sorry but you need to come back to this page from your email </p>
         <a href='<?php echo $mosConfig_live_site  ; ?>'>home</a>

            <?PHP
        }
}elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['user_id']) && isset($_REQUEST['user_email'])){
        $user_id = $_REQUEST['user_id']?$_REQUEST['user_id']:'';
        $user_id_md = $_REQUEST['user']?$_REQUEST['user']:'';
        $user_email = $_REQUEST['user_email']?$_REQUEST['user_email']:'';
        $date = date("Y-m-d");
        $comment = mysql_real_escape_string($_REQUEST['comment'])?$_REQUEST['comment']:'';
        $action = $mosConfig_live_site."/unsubscribe.php";
     
         $query = "INSERT INTO tbl_unsubscribe_comments (user_id, comment, email,date) VALUES ('$user_id', '$comment', '$user_email','$date')";
                            $result = mysql_query($query, $link);
                            if (!$result) {
                                die('Select error: ' . mysql_error());
                            }

                        ?>
                        <P> you unsubscribed successfully</p>
                         <a href='<?php echo $mosConfig_live_site  ; ?>'>home</a>
                        <?php

}else{
    ?>
    
<P>   we are sorry but you need to come back to this page from your email </p>
 <a href='<?php echo $mosConfig_live_site  ; ?>'>home</a>
  
    <?PHP
    
}