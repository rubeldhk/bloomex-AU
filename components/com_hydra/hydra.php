<?php
/**
* $Id: hydra.php 29 2007-04-17 08:12:43Z eaxs $
* @package   Project Fork
* @copyright Copyright (C) 2006-2007 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Project Fork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ('_VALID_MOS') OR die();


// added in 0.6.5 - load the debugger
require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/debug.class.php');
$GLOBALS['hydra_debug'] = $hydra_debug = new HydraDebugger();


// added in 0.6.5 - load the updater
require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/installer.class.php');
$GLOBALS['hydra_updater'] = $hydra_updater = new HydraUpdater();



require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/session.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/protector.class.php');

$GLOBALS['hydra_sess'] = $hydra_sess = new HydraSession($my, $database);
$GLOBALS['protect'] = $protect    = new HydraProtector($hydra_sess, $database);


require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/configuration.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/template.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_hydra/classes/system.class.php');

$GLOBALS['hydra_cfg'] = $hydra_cfg       = new HydraConfiguration($mosConfig_live_site, $mosConfig_absolute_path);
$GLOBALS['hydra_template'] = $hydra_template  = new HydraTemplate();
$GLOBALS['hydra'] = $hydra           = new HydraSystem(); 

require_once($hydra->load('language', $hydra_sess->profile['language']));


// content only?
if ($hydra_template->print_page) {
    $hydra_template->drawInterface();
    exit();
}


// file download?
$data_type = mosGetParam($_REQUEST, 'data_type', '');
if ( $data_type == 'data' AND ($protect->current_command == 'read_data')) {
	$hydra_template->drawInterface();
	exit();
}


echo $hydra_template->drawForm('adminForm');


// load template
echo $hydra->load('js', 'system');


require_once($hydra->load('theme', $hydra_sess->profile['theme']));


// added missing Itemid in 0.6.1
echo $hydra_template->drawInput('hidden', 'Itemid', intval($Itemid));


echo $hydra_template->drawForm('adminForm','',true);


$hydra_debug->printLog();
?>



