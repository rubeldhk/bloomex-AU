<?php
/**
* VirtueMart Search Module
* NOTE: THIS MODULE REQUIRES THE PHPSHOP COMPONENT FOR MOS!
*
* @version $Id: mod_virtuemart_search.php,v 1.4.2.1 2005/12/07 20:10:10 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

global $VM_LANG, $mm_action_url, $sess,$mosConfig_lang;

?>
<!--BEGIN Search Box --> 
<!--MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM-->
<form action="<?php $sess->purl( $mm_action_url."index.php?page=shop.browse" ) ?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" style="background-color: white;">
 <tr>
   <td align="left"><img src="modules/images/searchRt.gif" border="0" width="13" height="24" alt=""></td>
   <td align="center"><input title="<?php echo $$VM_LANG->_PHPSHOP_SEARCH_TITLE ?>" type="text" size="18" maxlength="256" name="keyword"></td>
   <td align="center">
   <?php switch ($mosConfig_lang) {	
      case 'french':  ?>
   <input type="image" src="modules/images/search_fr.gif" border="0" width="43" height="17" alt="" type="Submit" name="Search">
   <?php break;
      case 'english': ?>	
   <input type="image" src="modules/images/search.gif" border="0" width="43" height="17" alt="" type="Submit" name="Search">
   <?php break;
	 default: ?>
   <input type="image" src="modules/images/search.gif" border="0" width="43" height="17" alt="" type="Submit" name="Search">
	<?php break;
	 } ?>	

</td>
   <td align="right"><img src="modules/images/searchLt.gif" border="0" width="11" height="24" alt=""></td>
 </tr>
    <input type="hidden" name="Itemid" value="<?php echo intval(@$_REQUEST['Itemid']) ?>">
    <input type="hidden" name="option" value="com_virtuemart">
    <input type="hidden" name="page" value="shop.browse">
  </form>

</table>

<!-- End Search Box --> 