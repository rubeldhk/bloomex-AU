<?php
require_once 'CreateExcel.php';
$CreateExcel = new CreateExcel();
$CreateExcel->check();
?>
<script type="text/javascript" src="http://<?php echo $_SERVER["SERVER_NAME"] ?>/templates/bloomex7/js/jquery.js"></script>
<script>
var $j = jQuery.noConflict();
</script>
<?php
$path = "http://flowersinfo.org/bloomex.ca/spider/SpiderLegacy.html";
$file = "http://flowersinfo.org/bloomex.ca/spider/result.txt";

require_once 'components/com_virtuemart/classes/htmlTools.class.php';

$tabs = new mShopTabs(0, 1, "_main");
$tabs->startPane("content-pane");

$tabs->startTab( "View data", "info-page1");
require_once 'ViewData.php';
$tabs->endTab();

$tabs->startTab( "Download data", "info-page2");
require_once 'DownloadData.php';
$tabs->endTab();

$tabs->startTab( "Save data", "info-page3");
require_once 'SaveData.php';
$tabs->endTab();

$tabs->endPane();
?>
