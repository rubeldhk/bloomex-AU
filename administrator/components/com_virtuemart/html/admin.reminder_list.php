<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: admin.country_list.php,v 1.7 2005/05/08 09:02:24 soeren_nb Exp $
* @package mambo-phpShop
* @subpackage HTML
* Contains code from PHPShop(tm):
* 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
*	Community: www.phpshop.org, forums.phpshop.org
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* mambo-phpShop is Free Software.
* mambo-phpShop comes with absolute no warranty.
*
* www.mambo-phpshop.net
*/
mm_showMyFileName( __FILE__ );
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (!empty($keyword)) {
	$list  = "SELECT * FROM #__{vm}_reminder WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_reminder WHERE ";
     $q  = "(recip_name LIKE '%$keyword%' OR ";
     $q .= "recip_email LIKE '%$keyword%' OR ";
     $q .= "occasion LIKE '%$keyword%' ";
	$q .= ") ";
	$q .= "ORDER BY reminder_id ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
else  {
	$list  = "SELECT * FROM #__{vm}_reminder ";
	$list .= "ORDER BY reminder_id ";
	$list .= "LIMIT $limitstart, " . $limit;
	$count = "SELECT count(*) as num_rows FROM #__{vm}_reminder ";
}

$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");
  
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_REMINDER_LIST_MNU, IMAGEURL."ps_image/reminder.png", $modulename, "reminder_list");

// start the list table
$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
      					Name=> '',
					$VM_LANG->_PHPSHOP_REMINDER_LIST2 => '',
					'Reminder data'=> '',
					'Send' => '',
                                        $VM_LANG->_PHPSHOP_REMINDER_LIST5 => '',
					$VM_LANG->_PHPSHOP_REMINDER_LIST7 => '',
					_E_REMOVE => "width=\"5%\""
				);
$listObj->writeTableHeader( $columns );

$db->query($list);
$i = 0;
while ($db->next_record()) {

	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
	
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("reminder_id"), false, "reminder_id" ) );
	

	$url = $_SERVER['PHP_SELF']."?page=$modulename.reminder_form&limitstart=$limitstart&keyword=$keyword&reminder_id=". $db->f("reminder_id");
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">".$db->f("recip_name")."</a><br />";
	$listObj->addCell( $tmp_cell );

    
//	$tmp_cell = "<a href=\"". $sess->url($_SERVER['PHP_SELF']."?page=admin.reminder_form&limitstart=$limitstart&keyword=$keyword&reminder_id=". $db->f("reminder_id")) ."\">".$db->f("recip_name")."</a>";
//	$listObj->addCell( $tmp_cell );
	
        $listObj->addCell( $db->f('recip_email'));

        $tmp_cell = $db->f('recip_day').'/'.$db->f('recip_month');
	$listObj->addCell( $tmp_cell );
	if ($db->f("sdate")<>"") {
        $listObj->addCell(date("j-m-Y",$db->f("sdate")));
        } else {
        $listObj->addCell("-");
        }
        $listObj->addCell( $db->f("subject"));
        $listObj->addCell( $db->f("occasion"));
	$listObj->addCell( $ps_html->deleteButton( "reminder_id", $db->f("reminder_id"), "deleteReminder", $keyword, $limitstart ) );

	$i++;

}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword );


?>

