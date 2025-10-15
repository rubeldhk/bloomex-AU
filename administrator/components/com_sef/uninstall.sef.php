<?php

/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: uninstall.sef.php 308 2008-04-01 18:51:45Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20} 
 */


defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// V 1.2.4.t improved upgrading
function shDeletetable( $tableName) {
  global $database;
  $sql = 'DROP TABLE #__'.$tableName;
  $database->setQuery( $sql);
  $database->query();
}

function shDeleteAllSEFUrl( $kind) {
  global $database, $mosConfig_absolute_path;
  
  $sql = 'DELETE FROM #__redirection WHERE ';
  If ($kind == 'Custom')
    $where = '`dateadd` > \'0000-00-00\' and `newurl` != \'\';';
  else 
    $where = '`dateadd` = \'0000-00-00\';';  
  $database->setQuery($sql.$where);
  $database->query();
}

function shWriteModuleConfig( $modName, $shConfig, $pub, $sefConfig) {
  global $mosConfig_absolute_path, $mosConfig_live_site;
  if (empty($shConfig)) return;
  $moduleFile = $mosConfig_absolute_path.'/media/sh404_upgrade_'.$modName.'_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php';
  @unlink($moduleFile);  // remove previous if any                
  $data = '<?php // Module params save file for sh404SEF
//'.$sefConfig->version.'    
if (!defined(\'_VALID_MOS\')) die(\'Direct Access to this location is not allowed.\');
$shConfig = ';
  $data .= var_export( $shConfig, true). ';';
  // save pages the module is published on
  $data .= "\n".'$shPub = '.var_export( $pub, true).';';
  $data .= "\n".'?'.'>';    
  $moduleFileHandle=fopen( $moduleFile,'wb');
  if ($moduleFileHandle) {
    fwrite( $moduleFileHandle, $data);
    fclose( $moduleFileHandle);
  } 
}


function shSaveModuleParam( $modName, $shConfig) {
  global $database, $mosConfig_absolute_path, $mosConfig_live_site;
  
  $sql = 'SELECT id, title, ordering, position, published, access, showtitle, params FROM `#__modules` WHERE `module`= \''.$modName.'\';';
  $database->setQuery($sql);
  $result = $database->loadAssocList();
  $modId = $result[0]['id'];
  unset($result[0]['id']); // we don't want to save DB id
  if ($shConfig->shKeepModulesSettingsOnUpgrade) {
    // find pages the module is published on
    $sql = "SELECT menuid FROM #__modules_menu WHERE moduleid = " . (int) $modId;
  	$database->setQuery( $sql );
  	$rows = $database->loadResultArray();
    $pub = array();
  	foreach($rows as $menuid) {
  	  $pub[] = $menuid;
  	}
    // write everything on disk
    shWriteModuleConfig($modName, $result[0], $pub, $shConfig);
  } else {
    $moduleFile = $mosConfig_absolute_path.'/media/sh404_upgrade_'.$modName.'_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php';
    @unlink($moduleFile);
  }
  // now remove also module publication pages 
  $sql='DELETE FROM #__modules_menu WHERE moduleid = ' . (int) $modId;
  $database->setQuery( $sql );
  $database->query( $sql );
}

function com_uninstall() {
  global $database, $mosConfig_absolute_path, $mosConfig_live_site;
  // V 1.2.4.t before uninstalling modules, save their settings, if told to do so
  $sef_config_class = $GLOBALS['mosConfig_absolute_path']."/administrator/components/com_sef/sh404sef.class.php";
  // Make sure class was loaded.
  if (!class_exists('SEFConfig')) {   // V 1.2.4.T was wrong variable name $SEFConfig_class instead of $sef_config_class
    if (is_readable($sef_config_class)) require_once($sef_config_class);
    else die(_COM_SEF_NOREAD."( $sef_config_class )<br />"._COM_SEF_CHK_PERMS);
  }
  $sefConfig = new SEFConfig();
  if (!$sefConfig->shKeepStandardURLOnUpgrade && !$sefConfig->shKeepCustomURLOnUpgrade) {
    shDeleteTable('redirection');
    shDeleteTable('sh404sef_aliases');
  }  
  elseif (!$sefConfig->shKeepStandardURLOnUpgrade)
    shDeleteAllSEFUrl('Standard');
  elseif (!$sefConfig->shKeepCustomURLOnUpgrade) {
    shDeleteAllSEFUrl('Custom');
    shDeleteTable('sh404sef_aliases');
  }  
  if (!$sefConfig->shKeepMetaDataOnUpgrade)
    shDeleteTable('sh404SEF_meta'); 
     
  shSaveModuleParam( 'mod_shCustomTags', $sefConfig);

	// uninstall modules
	$database->setQuery( "DELETE FROM `#__modules` WHERE `module`= 'mod_shCustomTags';");
	$database->query();
	@unlink( "$mosConfig_absolute_path/modules/mod_shCustomTags.css" );
	@unlink( "$mosConfig_absolute_path/modules/mod_shCustomTags.php" );
	@unlink( "$mosConfig_absolute_path/modules/mod_shCustomTags.xml" );

	// preserve configuration or not ?
	if (!$sefConfig->shKeepConfigOnUpgrade) {
	  @unlink( $mosConfig_absolute_path.'/media/sh404_upgrade_conf_'
                .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php');
      @unlink( $mosConfig_absolute_path.'/media/sh404_upgrade_conf_'
                .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.default.php');          
      @unlink($mosConfig_absolute_path.'/media/sh404_upgrade_mod_shCustomTags_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php');
	  if ($handle = opendir(sh404SEF_ADMIN_ABS_PATH.'logs/')) {
    	while (false !== ($file = readdir($handle))) {
        	if ($file != '.' && $file != '..') 
        	  @unlink($mosConfig_absolute_path.'/media/sh404_upgrade_conf_logs/'.$file);
    	}
    	closedir($handle);
	  } 
	  if ($handle = opendir(sh404SEF_ADMIN_ABS_PATH.'security/')) {
    	while (false !== ($file = readdir($handle))) {
        	if ($file != '.' && $file != '..') 
        	  @unlink($mosConfig_absolute_path.'/media/sh404_upgrade_conf_security/'.$file);
    	}
    	closedir($handle);
	  }                     
	}            
	// must move log files out of the way, otherwise administrator/com_sef/logs will not be deleted
	// and next installation of com_sef will fail
	else { // if we keep config
		// make dest dir
		@mkdir($mosConfig_absolute_path.'/media/sh404_upgrade_conf_logs');
		@mkdir($mosConfig_absolute_path.'/media/sh404_upgrade_conf_security');
		if ($handle = opendir(sh404SEF_ADMIN_ABS_PATH.'logs/')) {
    		while (false !== ($file = readdir($handle))) {
        		if ($file != '.' && $file != '..' && $file != 'index.html') 
        	  	@rename(sh404SEF_ADMIN_ABS_PATH.'logs/'.$file,
        	  		$mosConfig_absolute_path.'/media/sh404_upgrade_conf_logs/'.$file);
    		}
    		closedir($handle);
	    }
		if ($handle = opendir(sh404SEF_ADMIN_ABS_PATH.'security/')) {
    		while (false !== ($file = readdir($handle))) {
        		if ($file != '.' && $file != '..' && $file != 'index.html') 
        	  	@rename(sh404SEF_ADMIN_ABS_PATH.'security/'.$file,
        	  		$mosConfig_absolute_path.'/media/sh404_upgrade_conf_security/'.$file);
    		}
    		closedir($handle);
	    }
	}
	echo '<h3>sh404SEF and its associated modules shCustomtags, have been succesfully uninstalled. </h3>';
	echo '<br />';
	if ($sefConfig->shKeepStandardURLOnUpgrade)
	  echo '- automatically generated SEF url have not been deleted (table #__redirection)<br />';
	else 
    echo '- automatically generated SEF url have been deleted<br />';  
  echo '<br />';
	if ($sefConfig->shKeepCustomURLOnUpgrade)
	  echo '- custom SEF url and aliases have not been deleted (table #__redirection and sh404sef_aliases)<br />';
	else 
    echo '- custom SEF url and aliases have been deleted<br />';  
  echo '<br />';
	if ($sefConfig->shKeepMetaDataOnUpgrade)
	  echo '- Custom Title and META data have not been deleted (table #__sh404SEF_meta)<br />';
	else 
    echo '- Custom Title and META data have been deleted<br />';  
  echo '<br />';
	if ($sefConfig->shKeepModulesSettingsOnUpgrade)
	  echo '- shCustomtags modules parameters have not been deleted<br />';
	else 
    echo '- shCustomtags modules parameters have been deleted<br />';
  echo '<br />';   
   
}
