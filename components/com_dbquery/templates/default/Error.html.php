<?php

/***************************************
 * $Id: ExecuteQuery.Failure.html.php,v 1.4 2005/05/10 14:47:32 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.4 $
 **/

defined( '_VALID_MOS' ) or die( _LANG_TEMPLATE_NO_ACCESS );

global $dbq;

echo $dbq->getDescriptionError();

?>

