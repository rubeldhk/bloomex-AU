<?php
/**
* @version $Id: mod_mainmenu.php 3592 2006-05-22 15:26:35Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

//SETUP MENU
global $mosConfig_absolute_path, $mosConfig_shownoauth, $mosConfig_live_site;
?>

<form name="productSearchForm" method="post" action="index.php?page=shop.browse&option=com_virtuemart">
	<div class="search">
		<input type="text" onfocus="if(this.value=='/Search') this.value='';" onblur="if(this.value=='') this.value='/Search';" value="<?php echo ( !empty($_REQUEST['keyword1']) ? $_REQUEST['keyword1'] : "/Search" ); ?>" size="15" name="keyword1" class="inputbox" alt="search">
		<input type="submit" class="button" value="ok">
	</div>
</form>