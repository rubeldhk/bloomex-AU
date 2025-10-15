<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

if($_POST['option']){
    $task = $_POST['option'];
    switch ($task) {
        case 'get_domains_list':
            get_domains_list();
            break;
        case 'update_users':
            update_users();
            break;
    }
}

function  get_domains_list() {
    global $mysqli;
    
    $query = "SELECT 
        `company_domain`, 
        `company_group_id`
    FROM `company_groups`
    ";
    
    $result = $mysqli->query($query);

    if (!$result) {
        echo $query;
        
        die('Select Error: '.$mysqli->error);
    }

    while ($obj = $result->fetch_object()) {
        $groups[$obj->company_group_id] .= "@".$obj->company_domain."|";
    }
    
    $result->close();
    
    $arr=array();
    if ($groups) {
        $_SESSION['groups'] = $groups;
        $arr['result']='success';
        $arr['groups']=$groups;
        $arr['count']=count($groups);
    }
    else {
        $arr['result']='there is not domains list';
    }
    
    $mysqli->close();
    die(json_encode($arr));
}

function update_users() {
    global $mysqli;
    
    $domain = $_POST['domains'];
    $shop_group_id = $_POST['group_id'];
    $domain_arr=explode("|",$domain);
    $where = '';
    
    foreach ($domain_arr as $d) {
        if($d){
            $where.= " u.email LIKE '%".$d."' OR u.username LIKE '%".$d."' OR";
        }
    }
    $where = rtrim($where, "OR");
    $query = "SELECT 
        `u`.`id`,
        `u`.`email`,
        `x`.`shopper_group_id` 
    FROM `jos_users` AS `u`
    LEFT JOIN `jos_vm_shopper_vendor_xref` AS `x` 
        ON `x`.`user_id`=`u`.`id`
    WHERE (".$where.") 
        AND (x.shopper_group_id!=".$shop_group_id." or x.shopper_group_id is null)
    GROUP BY `u`.`id`";

    set_time_limit(60);
    
    $result = $mysqli->query($query);

    if (!$result) {
        echo $query;
        
        die('Select Error: '.$mysqli->error);
    }

    $users = '';
    $users_emails = '';
    $count_users = $result->num_rows;

    while ($obj = $result->fetch_object()) {
        if ($obj->shopper_group_id == '') {
            $query  = "INSERT INTO `jos_vm_shopper_vendor_xref`
            (
                `user_id`,
                `vendor_id`,
                `shopper_group_id`
            )
            VALUES (
                '".$obj->id."',
                '1',
                '".$shop_group_id."'
            )";
            
            if ($mysqli->query($query)) {
                echo $query;
                die('Insert Error: '.$mysqli->error);
            }
        }
        else {
            $users .= $obj->id.",";
        }
        $users_emails .= $obj->id ." -- ".$obj->email . ". Old Shopper Group id -- ".$obj->shopper_group_id . "<br>";
    }
    
    $result->close();
    
    if ($users != '') {
        set_time_limit(60);
        $users = rtrim($users, ",");
        $users = "(".$users.")";
                
        $query = "UPDATE `jos_vm_shopper_vendor_xref`
        SET 
            `shopper_group_id`='".$shop_group_id."'
        WHERE `user_id` IN ".$users."
        ";

        if ($mysqli->query($query)) {
            echo $query;
            die('Update Error: '.$mysqli->error);
        }
    }
    
    echo "<br>We update ". $count_users." users groups to ".$shop_group_id."<br>".$users_emails."";
    $mysqli->close();
    die();
}