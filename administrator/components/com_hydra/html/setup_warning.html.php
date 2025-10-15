<?php
/**
* $Id: setup_warning.html.php 16 2007-04-15 12:18:46Z eaxs $
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

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );

global $mosConfig_live_site;
?>
<table width="640" cellpadding="0" cellspacing="0" align="center" style="border:1px solid #cccccc">
  <tr>
    <td align="left" valign="top" style="height:45px;padding:2px">
    <img src="<?php echo $mosConfig_live_site;?>/administrator/components/com_hydra/themes/default/images/hydra_logo.gif" align="left" alt="Hydra"/>
    </td>
    <td width="100%" align="right" valign="middle"><span style="height:45px;color:#349800;font-weight:bold;font-size:16px;padding:2px">
    Installation</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="background:#f4f4f4;padding:10px;border-top:2px solid #349800" align="left" valign="top">
    
    <table cellpadding="1" cellspacing="1" width="100%" align="left" style="background:#FFFFFF;border:1px solid #349800;margin-bottom:5px;">
      <tr>
        <td align="center"><strong>Please Remove the Setup-File!</strong></td>
      </tr>
      <tr>
        <td align="center" style="color:darkred">administrator/components/com_hydra/setup.php</td>
      </tr>
      <tr>
        <td align="center"><a href="index2.php?option=com_hydra">Go to your Controlpanel</a></td>
      </tr>
    </table>
    
    </td>
  </tr>
</table> 