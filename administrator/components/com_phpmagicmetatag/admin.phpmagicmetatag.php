        <?php  
/**
* @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'admin_html' ) );

$task	= mosGetParam( $_REQUEST, "task", "" );

//die($act);
switch ($task) {
	case 'save':
		saveMetaTagConfig( $option );
		break;
	
	default:
		showMetaTagConfig( $option );
		break;
}


//=================================================== POSTAL CODE OPTION ===================================================


function showMetaTagConfig( $option ) {
	global $database, $my, $mosConfig_absolute_path;
        
         $aConfig	= array();
         $types = array("company",'baskets','flowers','sympathy', 'default','product','category');
         foreach($types as $type){
                $sql	= "SELECT * FROM tbl_metatag_all WHERE type='".$type."'";
                $database->setQuery($sql);
                $rows	= $database->loadObjectList();
                $aConfig[$type] = $rows;
        }
	HTML_PhpMagicMetaTag::showMetaTagConfig( $option,$aConfig );
}



function saveMetaTagConfig( $option ) {
	global $database, $mosConfig_absolute_path, $act;
	if( count($_POST) ) {
		foreach ($_POST as $key => $value) {
			$aKey	= explode( "_", $key );			
			$sql	= "UPDATE tbl_metatag_all SET $aKey[1] = '$value' WHERE type = '$aKey[0]' AND  lang = '$aKey[2]'";
                        $database->setQuery($sql);
                        $database->query();
                }
	}			
	mosRedirect( "index2.php?option=$option", "Save MetaTag Configuration Successfully" );
}
?>
