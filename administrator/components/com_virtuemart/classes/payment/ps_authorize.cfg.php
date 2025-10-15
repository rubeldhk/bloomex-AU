<?php
/**
*
* @version $Id: ps_authorize.cfg.php,v 1.3 2005/09/29 20:02:17 soeren_nb Exp $
* @package VirtueMart
* @subpackage payment
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
define ('AN_TEST_REQUEST', 'true');
define ('AN_LOGIN', 'dgi00000');
define ('AN_TYPE', 'AUTH_CAPTURE');
define ('AN_CHECK_CARD_CODE', 'NO');
define ('AN_VERIFIED_STATUS', 'P');
define ('AN_INVALID_STATUS', 'P');
define ('AN_RECURRING', 'NO');
?>
