<?php
/**
* $Id: calendar_week.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra_template, $hydra, $protect;

$cal = new HydraCalendar();

$day = $cal->day;

if ($day == 0) { $day = $cal->today; }

?>
<div class="tableContainer">
<div class="tableContainer_header"><?php echo HL_CALENDAR;?></div>
<div class="tableContainer_body">
<table class="formTable" width="100%" cellpadding="0" cellspacing="0"> 
  <tr>
    <td><input type="text" size="3" class="formInput" name="day" value="<?php echo $day;?>" /></td>
    <td>&nbsp;</td>
    <td><?php echo $cal->dropMonths('month', intval(mosGetParam($_REQUEST, 'month', 0)));?></td>
    <td>&nbsp;</td>
    <td><input type="text" size="5" class="formInput" name="year" value="<?php echo $cal->year;?>" /></td>
    <td>&nbsp;</td>
    <td nowrap><?php echo HL_DISPLAY;?></td>
    <td></td>
    <td nowrap><?php echo $cal->dropDisplay('display');?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td nowrap><?php echo $cal->showSharedList('shared', intval(mosGetParam($_REQUEST, 'shared', 0)));?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td nowrap><a href='javascript:document.adminForm.submit();'><?php echo $hydra->load('img', '16_calendar_browse.gif', "alt='".HL_SHOW_DATE."' title='".HL_SHOW_DATE."'");?></a></td>
    <td width="100%">&nbsp;</td>
  </tr>
</table>
</div>
<div class="tableContainer_footer" align="center">&nbsp;</div>
</div>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'calendar');
echo $hydra_template->drawInput('hidden', 'cmd', '');
?>