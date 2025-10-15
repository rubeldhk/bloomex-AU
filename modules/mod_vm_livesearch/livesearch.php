<?php

//we can access this file from everywhere: security issue?
define( '_VALID_MOS', 1 );

//usual files:
define( '_BASEPATH', dirname(__FILE__) );
include_once( _BASEPATH.'/../../globals.php' );
require_once( _BASEPATH.'/../../configuration.php' );
require_once( _BASEPATH.'/../../includes/joomla.php' );
require_once( _BASEPATH.'/../../includes/sef.php' );

global $database;
global $mosConfig_offset;

//grab the virtuemart's itemid:
$query="SELECT componentid FROM #__menu WHERE menutype='mainmenu' AND link='index.php?option=com_virtuemart'";
$database->setQuery( $query );
$itemid=$database->loadResult('componentid');

//charset definition(according to those of joomla):
include_once( $mosConfig_absolute_path .'/language/' . $mosConfig_lang . '.php' );
$iso = explode( '=', _ISO );
header('Content-type: text/html; charset='. $iso[1]);

$keyword = $_POST["keyword"];

if(!empty($keyword))
{
	//the database request(don't hesitate to modify):
	$query='SELECT DISTINCT * FROM jos_vm_product AS p'
	. ' LEFT JOIN jos_vm_product_category_xref AS xcat ON xcat.product_id = p.product_id'
	. ' LEFT JOIN jos_vm_product_price AS pp ON pp.product_id = p.product_id'
	. ' LEFT JOIN jos_vm_category AS cat ON cat.category_id = xcat.category_id'
	. ' LEFT JOIN jos_vm_product_files AS pf ON pp.product_id = pf.file_product_id'
	
	. ' WHERE ( product_publish=\'Y\' AND category_publish=\'Y\' ) '
	
	. ' AND ( product_name LIKE \'%'.$keyword.'%\' '
	. ' OR product_sku LIKE \'%'.$keyword.'%\' '
	. ' OR product_url LIKE \'%'.$keyword.'%\' ' //date
	. ' OR product_s_desc LIKE \'%'.$keyword.'%\' '
	. ' OR product_desc LIKE \'%'.$keyword.'%\' '
	. ' OR file_title LIKE \'%'.$keyword.'%\' '
	
	. ' OR category_name LIKE \'%'.$keyword.'%\' '
	. ' OR category_description LIKE \'%'.$keyword.'%\' '
	
	. ' ) '
	
	
	. ' GROUP BY product_name'
	. ' ORDER BY category_name'
	
	. ' LIMIT 0 , '.$_POST["limit"];
	
	$database->setQuery( $query );
	$resultats = $database->loadObjectList();

  if (!empty($resultats)) {
		echo "\n<ul>";
		foreach ($resultats as $resultat) {
			echo '<li>'.'<a href="'.sefRelToAbs("index.php?page=shop.product_details&flypage=shop.flypage&product_id=".$resultat->product_id."&category_id=".$resultat->category_id."&option=com_virtuemart&Itemid=".$itemid).'">'.$resultat->product_name.'</a></li>';
		}
		echo "\n</ul>";
  }
  else {
  	echo '<b><p>no result';
  }
}
?>
