<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$start = $_GET['start'];

$users_sql = $mysqli->query("SELECT `id` FROM `jos_users` LIMIT ".$start.", 10000");
//$users_sql = $mysqli->query("SELECT `id` FROM `jos_users`");

if ($users_sql->num_rows > 0)
{
    $inserts = array();
    
    while($users_obj = $users_sql->fetch_object())
    {
        $inserts[] = "(".$users_obj->id.", 3)"; 
    }
    
    $users_sql->close();
    
    $mysqli->query("INSERT INTO `jos_vm_users_rating` (`user_id`, `rate`) VALUES ".implode(',', $inserts)."");
    printf("Errormessage: %s\n", $mysqli->error);
}

$mysqli->close();

echo 'ok';

?>
