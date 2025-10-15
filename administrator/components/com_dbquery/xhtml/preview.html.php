<?php

/***************************************
 * $Id: preview.html.php,v 1.2 2005/05/25 14:16:38 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.2 $
 **/

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');

global $dbq;

echo $dbq->getLastErrorMsgHTML() . '<br/>';
echo htmlspecialchars($dbq->getQuery()) . '<br/>';
echo htmlspecialchars($dbq->_sql) . '<br/>';
?>

