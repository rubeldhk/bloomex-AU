<?php
/**
 * SEF module for Joomla!
 * Originally written for Mambo as 404SEF by W. H. Welch.
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF
 * @version     $Id: install.sef.php 338 2008-04-08 19:31:38Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_VALID_MOS')) die('Direct Access to this location is not allowed.');

// V 1.2.4.t retrieve stored modules config if needed
function shGetModuleSavedParams( $modName, &$shConfig, &$shPub, $sefConfig) {
  global $mosConfig_absolute_path, $mosConfig_live_site;
  if (empty($sefConfig)) return;
  $moduleFile = $mosConfig_absolute_path.'/media/sh404_upgrade_'.$modName.'_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php'; 
  if (!$sefConfig->shKeepModulesSettingsOnUpgrade) {  // we've been told not to preserve modules settings so erase file
    @unlink($moduleFile);
  } else {  // we want to read settings for this module
    if (file_exists($moduleFile))
      include($moduleFile);
  }
}

function shInsertModule( $modName, $shConfig, $shPub, $sefConfig ) {
  global $database;
  
  shGetModuleSavedParams( $modName, $shConfig, $shPub, $sefConfig );
  $sql = "INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`) VALUES ('".$shConfig['title']."', '', ".$shConfig['ordering'].", '".$shConfig['position']."', 0, '0000-00-00 00:00:00', ".$shConfig['published'].", '".$modName."', 0, ".$shConfig['access'].", ".$shConfig['showtitle'].", '".$shConfig['params']."', 0, 0);";
  $database->setQuery( $sql);
  $database->query();
  $moduleID = $database->insertid();
  // set pages where module is published
  foreach ($shPub as $pub) {
    $database->setQuery( "INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ($moduleID, $pub);");
    $database->query();
  }  
}

function com_install()
{
    global $database, $mosConfig_absolute_path, $mosConfig_live_site;
    
    // V 1.2.4.t improved upgrade data preservation
    // V 1.2.4.q Copy existing config file from /media to current component. Used to recover configuration when upgrading
    // V 1.2.4.s check if old file exists before deleting stub config file
    $adminDir = dirname(__FILE__);
    $oldConfigFile = $mosConfig_absolute_path.'/media/sh404_upgrade_conf_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.php';
    if (is_readable($oldConfigFile)) {
      @unlink($adminDir. '/config/config.sef.php');
      @copy( $oldConfigFile, $adminDir. '/config/config.sef.php' );
    }
      // restore log files
	  if ($handle = @opendir($mosConfig_absolute_path.'/media/sh404_upgrade_conf_logs')) {
    	while (false !== ($file = readdir($handle))) {
        	if ($file != '.' && $file != '..') 
          		@copy($mosConfig_absolute_path.'/media/sh404_upgrade_conf_logs/'.$file,
          			$adminDir.'/logs/'.$file);
    	}
    	closedir($handle);
	  }
      // restore black/white lists
      if ($handle = @opendir($mosConfig_absolute_path.'/media/sh404_upgrade_conf_security')) {
    	while (false !== ($file = readdir($handle))) {
        	if ($file != '.' && $file != '..') {
        		@unlink($adminDir.'/security/'.$file);
          		@copy($mosConfig_absolute_path.'/media/sh404_upgrade_conf_security/'.$file,
          			$adminDir.'/security/'.$file);
        	}	
    	}
    	closedir($handle);
	  }
      // restore customized default params 
      $oldDefaultConfigFile = $mosConfig_absolute_path.'/media/sh404_upgrade_conf_'
                  .str_replace('/','_',str_replace('http://', '', $mosConfig_live_site)).'.default.php';
      if (is_readable($oldDefaultConfigFile) && filesize($oldDefaultConfigFile) > 1000) {
      	@unlink($adminDir. '/default.sef.php');
      	@copy( $oldDefaultConfigFile, $adminDir. '/default.sef.php' );
      }	
    $sef_config_class = $GLOBALS['mosConfig_absolute_path']."/administrator/components/com_sef/sh404sef.class.php";
    // Make sure class was loaded.
    if (!class_exists('SEFConfig')) {   // V 1.2.4.T was wrong variable name $SEFConfig_class instead of $sef_config_class
        if (is_readable($sef_config_class)) require_once($sef_config_class);
        else die(_COM_SEF_NOREAD."( $sef_config_class )<br />"._COM_SEF_CHK_PERMS);
    }
    $sefConfig = new SEFConfig();
        
    ob_start();
    //this code is adapted from the install file of Joomfish 1.7

    // meta module install
    $shConfig = array('title'=>'sh404SEF Custom tags module', 'ordering'=>2,'position'=>'user3','published'=> 0,'access'=>0,'showtitle'=>0,'params'=>'');  // V 1.2.4.T preserve config
    $shPub = array('0');
	shInsertModule( 'mod_shCustomTags', $shConfig, $shPub, $sefConfig);
    	  
    @rename( $adminDir. '/modules/mod_shCustomTags.php', 
      $mosConfig_absolute_path.'/modules/mod_shCustomTags.php');
    @rename( $adminDir. '/modules/mod_shCustomTags.tmp', 
      $mosConfig_absolute_path.'/modules/mod_shCustomTags.xml');

    // end of code adapted from Joomfish 1.7 install file
    
    // success !
    echo '<div style="text-align: justify;">';
    echo '<h1>sh404SEF installed succesfully! Please read the following</h1>';
    echo 'If it is the first time you use sh404SEF, it has been installed but is <strong>disabled</strong> right now. You must first edit sh404SEF configuration, <strong>enable it and save</strong> before it will become active. You must also <strong>activate SEF URL in Joomla Site configuration</strong> screen, under the SEO tab. Before you do so, please read the next paragraphs which have important information for you.  If you are upgrading from a previous version of sh404SEF, then all your settings have been preserved, the component is activated and you can start browsing your site frontpage right away.';
    echo '<br /><br />';
    echo '<strong><font color="red">IMPORTANT</font></strong> : sh404SEF can operate under two modes : <strong><font color="red">WITH</font></strong> or <strong><font color="red">WITHOUT .htaccess</font></strong> file. The default setting is now to work <strong>without .htaccess file</strong>. I recommend you use it, as it is generally difficult to find the right content for a .htaccess file.<br /><br />';
    echo '<strong>Without .htaccess file</strong> : simply go to sh404SEF configuration screen, review parameters, and save config. Remember also to set SEF URL to Yes in Joomla General site configuration. When you do so, you will be reminded that you need to rename htaccess.txt to .htaccess. This is not required when using sh404SEF in the "no .htaccess" mode (all other SEF components so far require a .htaccess file). You can now browse the frontpage of your site to start generating SEF URL.<br />';
    echo '<strong>With .htaccess</strong> : you must activate this operating mode. To do so, go to sh404SEF configuration, select the Advanced tab, locate the "Rewrite mode" drop-down list and select \'with .htaccess\'. Then Save configuration and answer Ok when prompted to erase URl cache. However, before you can activate sh404SEF, you have to setup a .htaccess file. This file content depends on your hosting setup, so it is nearly impossible to tell you what should be in it. Joomla comes with the most generic .htaccess file. It will probably work right away on your system, or may need adjustments. The Joomla supplied file is called htaccess.txt, is located in the root directory of your site, and must be renamed into .htaccess before it will have any effect. You will find additional information about .htaccess at <a href="http://extensions.siliana.net/en/sh404SEF-and-url-rewriting/.htaccess-files-information.html">extensions.Siliana.net</a>. Please also remember to activate SEF URL in Joomla General site configuration.<br /><br />';
    echo '<strong><font color="red">IMPORTANT</font><strong>: sh404SEF can build SEF URL for many Joomla components. It does it through a <strong>"plugin" system</strong>, and comes with a dedicated plugin for each of Joomla standard components (Contact, Weblinks, Newsfeed, Content of course,...). It also comes with native plugins for common components such as Community Builder, Fireboard, Virtuemart, Sobi2,... (<a href="http://extensions.siliana.net/en/sh404SEF-and-url-rewriting/list-of-available-plugins-for-sh404SEF-SEF-URL-rewriting-component.html">full list on our web site</a>). sh404SEF can also automatically make use of plugins designed for other SEF components such as OpenSEF or SEF Advanced. Such plugins are often delivered and installed automatically when you install a component. Please note that when using these "foreign" plugins, you may experience compatibility issues.<br />However, Joomla having several hundreds extensions available, not all of them have a plugin to tell sh404SEF how its URL should be built. When it does not have a plugin for a given component, sh404SEF will switch back to Joomla standard SEF URL, similar to mysite.com/component/option,com_sample/task,view/id,23/Itemid,45/. This is normal, and can\'t be otherwise unless someone write a plugin for this component (your assistance in doing so is very much welcomed! Please post on the support forum if you have written a plugin for a component.<br />';
    echo '<br />';
    echo 'You will also find more documentation, including <a href="http://extensions.siliana.net/en/sh404SEF-and-url-rewriting/How-to-write-a-plugin-for-sh404SEF.html">on how to write plugins for sh404SEF</a> at <a href="http:/:extensions.siliana.net/en/">extensions.Siliana.net</a>';
    echo '<br />';
    
    echo  '<p class="message">Please scroll down and read this documentation.<br/>It is also available on sh404SEF main control panel</p>';

    include($GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_sef/readme.inc.php');

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
?>