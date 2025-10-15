<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* mambo-phphop Product Categories Module
* NOTE: THIS MODULE REQUIRES AN INSTALLED MAMBO-PHPSHOP COMPONENT!
*
* @version $Id: mod_product_categories.php,v 1.4 2005/10/27 16:09:13 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
* 
* @copyright (C) 2004-2005 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
global $jscook_type;

$category_id = mosGetParam( $_REQUEST, 'category_id');

/* Get module parameters */
$class_sfx = $params->get( 'class_sfx', "" );
$menutype = $params->get( 'menutype', "links" );
$jscookMenu_style = $params->get( 'jscookMenu_style', 'ThemeOffice' );
$jscookTree_style = $params->get( 'jscookTree_style', 'ThemeXP' );
$jscook_type = $params->get( 'jscook_type', 'menu' );
$menu_orientation = $params->get( 'menu_orientation', 'hbr' );
$_REQUEST['root_label'] = $params->get( 'root_label', 'Shop' );

$class_mainlevel = "mainlevel".$class_sfx;

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

global $VM_LANG, $sess;

if ( $menutype == 'links' ) {
	/* MENUTPYE LINK LIST */
	require_once(CLASSPATH.'ps_product_category.php');
	$ps_product_category = new ps_product_category();

	echo $ps_product_category->get_category_tree( $category_id, $class_mainlevel );

}
elseif( $menutype == "transmenu" ) {
	/* TransMenu script to display a DHTML Drop-Down Menu */
	include( $mosConfig_absolute_path . '/modules/vm_transmenu.php' );

}
elseif( $menutype == "dtree" ) {
	/* dTree script to display structured categories */
	include( $mosConfig_absolute_path . '/modules/vm_dtree.php' );

}
elseif( $menutype == "jscook" ) {
	/* JSCook Script to display structured categories */
	include( $mosConfig_absolute_path . '/modules/vm_JSCook.php' );

}
 ?>