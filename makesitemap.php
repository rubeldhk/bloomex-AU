<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'configuration.php';
require_once ($mosConfig_absolute_path.'/includes/Sitemap.php');
$sitemap = new Sitemap(false);
DEFINE('BASE_URI',$mosConfig_absolute_path.'/');
DEFINE('BASE_FOLDER','sitemap/');
DEFINE('BASE_URL', $url = str_replace(array('stage1.amazon.', 'test:HPwm&212W@'),'',$mosConfig_live_site).'/');
echo "<br>BASE_URI ".BASE_URI;
echo "<br>BASE_URL ".BASE_URL;
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
} else {
 echo "db connection established";
}
/* check connection */
$sitemap->page('bloomex');
$date = array();
$i = 0;
$my_array = array(0.6,0.7,0.8,0.9,1.0);
$query_cat = "SELECT category_id,mdate FROM  `jos_vm_category` WHERE  `category_publish` = 'Y'";

    if ($result = $mysqli->query($query_cat)) {
        $res = $result->fetch_row();
        $cat = array();
            while($res = $result->fetch_row()){
                $url_cat = "index.php?option=com_virtuemart&category_id=".$res[0]."&page=shop.browse";

                $dtms011 = new DateTime();
                $dtms011->setTimestamp($res[1]);
                $date_url = $dtms011->format('Y-m-d');

                $cat = array($url_cat,$date_url);
                list($url, $date) = $cat;
                $i ++;
                shuffle($my_array);
                $sitemap->url($url, $date, 'weekly',$my_array[0]);
            }
            $result->close();
    }
$query_prod = "SELECT p.product_id,x.category_id,p.mdate
FROM jos_vm_product as p
left join jos_vm_product_category_xref as x
on x.product_id = p.product_id where p.product_id is not null AND p.product_publish = 'Y'
group by p.product_id";

    if ($result = $mysqli->query($query_prod)) {
        $res = $result->fetch_row();
        $prod = array();
            while($res = $result->fetch_row()){
                $url_prod = "index.php?option=com_virtuemart&category_id=".$res[1]."&flypage=shop.flypage&page=shop.product_details&product_id=".$res[0]."";

                $dtms011 = new DateTime();
                $dtms011->setTimestamp($res[2]);
                $date_url = $dtms011->format('Y-m-d');

                $prod = array($url_prod,$date_url);
                list($url, $date) = $prod;
                $i ++;
                shuffle($my_array);
                $sitemap->url($url, $date, 'weekly',$my_array[0]);
            }
            $result->close();
    }
$query_landing = "SELECT  DISTINCT  newurl
FROM jos_redirection
WHERE newurl LIKE  '%com_landingpages%'
OR newurl LIKE  '%com_landingbasketpages%'
OR newurl LIKE  '%com_companies%'";


    if ($result = $mysqli->query($query_landing)) {
        $res = $result->fetch_row();
        $landing = array();
            while($res = $result->fetch_row()){
                $url_landing = $res[0];

                $date_url = date('Y-m-d');

               $landing = array($url_landing,$date_url);
                list($url, $date) = $landing;
                $i ++;
                shuffle($my_array);
                $sitemap->url($url, $date, 'weekly',$my_array[0]);
            }
            $result->close();
    }

$query_content = "SELECT id,modified  FROM jos_content WHERE state=1";

    if ($result = $mysqli->query($query_content)) {
        $res = $result->fetch_row();
        $content = array();
            while($res = $result->fetch_row()){
                $url_content = "index.php?option=com_content&id=".$res[0]."&task=view";

                if((int)$res[1]!= 0) {

                $date_url = date('Y-m-d',  strtotime($res[1]));
               }
               else {

                $date_url = date('Y-m-d');
               }

                $content = array($url_content,$date_url);
                list($url, $date) = $content;
                $i ++;
                shuffle($my_array);
                $sitemap->url($url, $date, 'weekly',$my_array[0]);
            }
            $result->close();
    }


$mysqli->close();
$sitemap->close();
echo "<br/>$i done";
unset($sitemap);
unset($mysqli);
?>
