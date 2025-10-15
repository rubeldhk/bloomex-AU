<?php

include "configuration.php";
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
if (!mysql_select_db($mosConfig_db)) {
    die('Could not select database: ' . mysql_error());
}
switch ($_REQUEST['manager']) {
    
        case 'corporateapp':
        $return = array();
        $return['result'] = false;
        
        $query = "DELETE FROM `jos_redirection`
        WHERE `newurl` LIKE '%com_corporateapp%' OR `newurl`=''";
        
        if (!mysql_query($query)) {
            $return['error'] = 'DELETE|'.mysql_error();
        }
        else {
            $query = "SELECT 
                `c`.*
            FROM `jos_corporateapp` AS `c`";
            
            if ($result = mysql_query($query)) {
                $inserts = array();
                
                while ($row = mysql_fetch_object($result)) {
                    $inserts[] = "('index.php?option=com_corporateapp&url=".mysql_real_escape_string($row->url)."&lang=en', 'Partnership-Account/".mysql_real_escape_string($row->url)."')";
                }
                
                if (sizeof($inserts) > 0) {
                    $query = "INSERT INTO `jos_redirection`
                    (
                        `newurl`,
                        `oldurl`
                    )
                    VALUES 
                        ".implode(',', $inserts)."
                    ";
                    
                    if (!mysql_query($query)) {
                        $return['error'] = 'INSERT|'.mysql_error();
                    }
                    else {
                        $return['result'] = true;
                    }
                }
                else {
                    $return['error'] = 'Empty.';
                }
            }
        }
        
        mysql_close();
        echo json_encode($return);
        die;
    break;
    
    case 'landing':

        mysql_set_charset('utf8');

        $qwerty = "DELETE FROM jos_redirection WHERE ( newurl LIKE '%com_landingpages%'  or  newurl LIKE '%com_landingbasketpages%' or newurl LIKE '') AND can_not_be_deleted = '0' ";
        $result = mysql_query($qwerty, $link);
        if (!$result) {
            mysql_close();
            die('Delete error: ' . mysql_error());
        }

        $qwerty = "DELETE FROM jos_sh404sef_aliases WHERE newurl LIKE '%com_landingpages%'  or  newurl LIKE '%com_landingbasketpages%' or newurl LIKE ''";
        $result = mysql_query($qwerty, $link);
        if (!$result) {
            mysql_close();
            die('Delete alias error: ' . mysql_error());
        }
        $cities = 0;


        while (1) {
            $sql = "SELECT url FROM tbl_landing_pages WHERE enable_location=1 ORDER BY url LIMIT $cities, 500";
            $result = mysql_query($sql, $link);
            if (!$result) {
                mysql_close();
                die('Select error: ' . mysql_error());
            }
            if (mysql_affected_rows() == 0) {
                break;
            }
            $query = "INSERT INTO `jos_redirection` (`newurl`,`oldurl`) VALUES ";
            $query2 = "INSERT INTO `jos_sh404sef_aliases` (`newurl`,`alias`) VALUES ";
            while ($row =  mysql_fetch_object($result)) {
                $l++;
                $row->url = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', trim($row->url)));
                $query .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift-hamper-basket/" . $row->url . "/'),";
                $query2 .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift-hamper-basket/" . $row->url . "'),";
                $query2 .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift-basket/" . $row->url . "/'),";
                $query2 .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift_basket/" . $row->url . "/index.html'),";
                $query2 .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift_basket/" . $row->url . "/'),";
                $query2 .= " ('index.php?option=com_landingpages&type=basket&lang=en&Itemid=1&url=" . $row->url . "', 'gift_basket/" . $row->url . "'),";

                $query .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', 'florist/" . $row->url . "/'),";
                $query2 .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', 'florist/" . $row->url . "'),";
                $query2 .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', 'florist/" . $row->url . "/index.html'),";
                $query2 .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', '" . $row->url . "-florist/" . $row->url . "-flowers'),";
                $query2 .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', '" . $row->url . "-florist/" . $row->url . "-flowers/'),";
                $query2 .= " ('index.php?option=com_landingpages&lang=en&Itemid=1&url=" . $row->url . "', '" . $row->url . "-florist/" . $row->url . "-flowers/index.html'),";
                
                $query2 .= " ('index.php?option=com_landingpages&type=sympathy&lang=en&Itemid=1&url=" . $row->url . "', 'sympathy-flowers/" . $row->url . "/'),";
                $query2 .= " ('index.php?option=com_landingpages&type=sympathy&lang=en&Itemid=1&url=" . $row->url . "', 'sympathy-flowers/" . $row->url . "/index.html'),";
                $query2 .= " ('index.php?option=com_landingpages&type=sympathy&lang=en&Itemid=1&url=" . $row->url . "', 'sympathy-flowers/" . $row->url . "/'),";
                $query .= " ('index.php?option=com_landingpages&type=sympathy&lang=en&Itemid=1&url=" . $row->url . "', 'sympathy-flowers/" . $row->url . "'),";

                $cities++;
            }
            $query = rtrim($query, ',');
            $query2 = rtrim($query2, ',');

            if (!$result = mysql_query($query, $link)) {
                echo $result;
                die('There was an error running the ' . $cities . ' insert query [' . $db->error . ']');
            }

            if (!$result2 = mysql_query($query2, $link)) {
                echo $result2;
                die('There was an error running the ' . $cities . ' insert query [' . $db->error . ']');
            }
        }

        break;
    case 'landing_geo':
        $qwerty = "SELECT id,city,province,location_country FROM tbl_landing_pages where  enable_location=1 and lat like '' ";
        $result = mysql_query($qwerty, $link);
        if (!$result) {
            mysql_close();
            die('Select error: ' . mysql_error());
        }
        if (mysql_affected_rows() == 0) {
            break;
        }
        $rows = array();
        while ($row = mysql_fetch_object($result)) {
            $rows[]=$row;
        }
        foreach($rows as $row){
            set_time_limit (60);
            $city = urlencode(htmlentities(trim($row->city), ENT_QUOTES, "UTF-8"));
            $province = urlencode(htmlentities(trim($row->province), ENT_QUOTES, "UTF-8"));
            $location_country = urlencode(htmlentities(trim($row->location_country), ENT_QUOTES, "UTF-8"));
            $address = $city.",".$province.",".$location_country;
            $geocode=file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false&key=AIzaSyCDlnvxmoLg7G0t1AHz5DZ9vIlDnRD-Mxg");
            if($geocode){
                $output= json_decode($geocode);

                if($output && $output->status=='OK'){
                    $lat = $output->results[0]->geometry->location->lat;
                    $lng = $output->results[0]->geometry->location->lng;
                    if(!$lat && !$lng){
                        $lat = 45.4215296;
                        $lng = -75.697193;
                    }

                    $qwerty = "UPDATE  tbl_landing_pages SET lat='".$lat."',lng='".$lng."'  where  id='".$row->id."'";
                    $result = mysql_query($qwerty, $link);
                    if (!$result) {
                        die('Select error: ' . mysql_error());
                    }

                }
            }
        }
        break;
    default:
        mysql_close();
        die('underfined manager');
        break;
}
die('success manager ' . $_REQUEST['manager']);
?>
