<?php
/**
 * Installation file
 *
 * @package JL
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * @ignore 
 */
require_once($mosConfig_absolute_path . '/components/com_joomlalib/classes/jlcoreapi.class');

/**
 * Function gets executed in install
 *
 * @return string html capepable message for after install
 */
function com_install()
{
	JLCoreApi::install();
	
	/* Save the configuration for the first time */
	JLCoreApi::internalImport('jlcfg');
	$jlCfg = new JLCfg(null, null, null, null);
	$jlCfg->loadFromDB();
	$jlCfg->saveConfiguration(); 
	
	return 'Installed Successfully';
}

?>