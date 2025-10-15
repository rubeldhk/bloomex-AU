<?php
/**
* $Id: index.php 21 2007-04-16 10:47:37Z eaxs $
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

if (!$hydra->backend AND($hydra->raw_output)) {
	
	$mainframe->loadEditor = 1;
    require_once($mosConfig_absolute_path."/editor/editor.php");
    initEditor();
    
}

// load the content editor
if (!defined( '_JOS_EDITOR_INCLUDED' )) {
	initEditor();
}
?>
<script language="JavaScript" src="<?php echo $hydra_cfg->hydra_url;?>/themes/default/js/theme.js" type="text/javascript"></script>
<link type="text/css" media="screen"  rel="stylesheet" href="<?php echo $hydra_cfg->hydra_url;?>/themes/default/css/custom.css" />
<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $hydra_cfg->hydra_url;?>/themes/default/css/hydra.css" />
<?php if ($hydra->raw_output AND (!$hydra->backend)) { ?>
<style type="text/css">
body
{
   padding             : 10px !important;
}
</style>
<?php } ?>
<table width="100%" cellpadding="0" cellspacing="0" align="left" class="hydraContainer">

  <tr>
  
    <td align="left" class="hydra_navtitle"><?php echo HL_NAV_BOX;?></td>
    
    <td align="left" class="hydra_navpanel"><?php echo $hydra_template->drawNavigation('2', '-nav'); ?></td>
  
  </tr>
  
  <tr>
  
    <td align="left" class="hydra_msgtitle"><?php echo HL_SYS_MESSAGE;?></td>
      
    <td align="right" class="hydra_msgpanel"><?php $hydra_template->drawInterface('hydra_msg'); ?></td>
  
  </tr>
  
  <tr>
  
    <td class="hydraLeft" valign="top"><?php $hydra_template->drawInterface('right'); $hydra_template->drawInterface('left');?></td>
    
    <td class="hydraCenter" valign="top"><?php $hydra_template->drawInterface();?></td>
    
  </tr>
  
  <tr>
  
    <td align="center" colspan="2"><div class="hydraFooter" style="text-align:center !important"><small>Powered by Project Fork <?php echo $hydra_updater->c_version;?> - <a target="_blank" href="http://www.projectfork.net">http://www.projectfork.net</a></small></div></td>
  
  </tr>
  
</table>

