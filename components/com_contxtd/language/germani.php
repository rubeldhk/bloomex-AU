<?php
/**
* ==================================================================
* ContXTD - Contacts Extended
* 
* Author: Kurt Banfi
* Email: mambo@clockbit.com
* Website: www.clockbit.com
* Version: 1.0.1
* 
* ContXTD is a component for Mambo 4.5.2 and 
* derived from the Mambo Contacts Component.
* ==================================================================
* $Id: germani.php, v1.0 2005/06/06
* @package ContXTD
* @copyright (C) 2005 Kurt Banfi
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* ==================================================================
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direkter Zugriff zu diesem Bereich ist nicht erlaubt.' );

/** check if this file has already been included */
DEFINE("_CONTXTD_LANG_INCLUDED", 1);

/** add new definitions */
DEFINE('_CONTACT_HEADER_COMPANY','Unternehmen');
DEFINE('_CONTACT_HEADER_MOBILE','Mobil');

DEFINE('_CONTACT_COMPANY','Unternehmen:');
DEFINE('_CONTACT_WEBSITE','Internet:');
DEFINE('_CONTACT_MISC2','Information:');

DEFINE('_CONTACT_BLOG_EMAIL','Ein E-Mail an diesen Kontakt senden');
?>