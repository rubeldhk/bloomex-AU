<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

//CATEGORY

$query = "SELECT 
    `m`.`id`,
    `m`.`link`,
    `m`.`type`,
    `m`.`name`,
    `r`.`newurl`
FROM `jos_menu` AS `m`
INNER JOIN `jos_redirection` AS `r` ON
    `r`.`oldurl`=REPLACE(`m`.`link`, '/index.php/', '')
WHERE `m`.`published`='1' AND `m`.`menutype`='Bloomex_top' AND `m`.`new_type`!='vm_category'
GROUP BY `m`.`id`
ORDER BY `m`.`parent`, `m`.`ordering` ASC";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        
        if ($obj->type == 'url') {
            preg_match('/category_id=([0-9]+)/siu', $obj->newurl, $category_id);
            
            if ((int)$category_id[1] > 0) {
                $query = "UPDATE `jos_menu`
                SET
                    `new_type`='vm_category',
                    `link`=".(int)$category_id[1]."
                WHERE `id`=".$obj->id."";
                
                $mysqli->query($query);
            }
        }
    }
}

$query = "SELECT 
    `m`.`id`,
    `m`.`link`,
    `m`.`type`,
    `m`.`name`
FROM `jos_menu` AS `m`
WHERE `m`.`published`='1' AND `m`.`menutype`='Bloomex_top' AND `m`.`type`='content_section'
GROUP BY `m`.`id`
ORDER BY `m`.`parent`, `m`.`ordering` ASC";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $inserts = array();

    while ($obj = $result->fetch_object()) {
        
      
        preg_match('/id=([0-9]+)/siu', $obj->link, $content_id);

        if ((int)$content_id[1] > 0) {
            $query = "UPDATE `jos_menu`
            SET
                `new_type`='blog_section',
                `link`=".(int)$content_id[1]."
            WHERE `id`=".$obj->id."";

            echo $query.'<br/>';

            $mysqli->query($query);
        }
    }
}

?>
