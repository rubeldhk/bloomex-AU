<?php
/**
* $Id: calendar_newentry.html.php 16 2007-04-15 12:18:46Z eaxs $
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

mosCommonHTML::loadCalendar();

global $hydra_template;

$cal = new HydraCalendar();

$id = intval(mosGetParam($_REQUEST, 'id', 0));
$edit = null;

$day = $cal->today;
if (strlen($day) == 1) { $day = "0".$day; }

$start_date = $cal->year.'-'.$cal->month.'-'.$day;
$end_date   = $start_date;

if ($id >= 1) {
	$row = $cal->loadEvent($id);

	$day = @date('j', $row->start_date);
   if (strlen($day) == 1) { $day = "0".$day; }
   
	$start_date = @date('Y', $row->start_date).'-'.date('n', $row->start_date).'-'.$day;
	
	$day = @date('j', $row->end_date);
   if (strlen($day) == 1) { $day = "0".$day; }
   
   $end_date   = @date('Y', $row->end_date).'-'.date('n', $row->end_date).'-'.$day;
}

?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_GENERAL_INFO;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%" align="left"><?php echo $hydra_template->drawLabel(HL_TITLE." *", 'title_lbl');?></td>
    <td width="30%" align="left"><input type="text" name="title" class="formInput" size="30" value="<?php echo @stripslashes($row->title);?>"/></td>
    <td width="10%" align="left"><?php echo $hydra_template->drawLabel(HL_COLOR);?></td>
    <td width="40%" align="left"><?php echo @$cal->dropColors('color', $row->color);?></td>
  </tr>
  <tr>
    <td width="20%" align="left"></td>
  </tr>
</table>
&nbsp;
<table class="formTable" width="100%">
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_DETAILS);?></td>
    <td width="80%"><?php editorArea( 'details_editor', @stripslashes($row->details), 'details', '100%;', '250', '30', '20' ) ;?></td>
  </tr>
</table>
&nbsp;
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_START_AND_END_DATE;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_START_DATE." *", 'start_date_lbl');?></td>
    <td width="15%" nowrap>
    <input class="formInput" type="text" name="start_date" id="start_date" size="25" maxlength="19" value="<?php echo $start_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('start_date', 'dd-mm-y');" />
	 </td>
	 <td width="5%" align="center"><?php echo HL_HOUR;?></td>
	 <td width="10%" align="left"><?php echo @$cal->dropHours('start_hour', date('H', $row->start_date));?></td>
	 <td width="5%" align="center"><?php echo HL_MINUTE;?></td>
	 <td width="50%" align="left"><?php echo @$cal->dropMinutes('start_minute', date('i', $row->start_date));?></td>
  </tr>
  <tr>
    <td width="20%"><?php echo $hydra_template->drawLabel(HL_END_DATE." *", 'end_date_lbl');?></td>
    <td width="15%" nowrap>
    <input class="formInput" type="text" name="end_date" id="end_date" size="25" maxlength="19" value="<?php echo $end_date;?>" />
	 <input type="reset" class="button" value="..." onclick="return showCalendar('end_date', 'dd-mm-y');" />
    </td>
    <td width="5%" align="center"><?php echo HL_HOUR;?></td>
	 <td width="10%" align="left"><?php echo @$cal->dropHours('end_hour', date('H', $row->end_date));?></td>
	 <td width="5%" align="center"><?php echo HL_MINUTE;?></td>
	 <td width="50%" align="left"><?php echo @$cal->dropMinutes('end_minute', date('i', $row->end_date));?></td>
  </tr>
</table>
</fieldset>

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_MISC_SETTINGS;?></legend>
<table class="formTable" width="100%">
  <tr>
    <td><input type="checkbox" value="1" name="shared" <?php if (@$row->shared == '1') { echo "checked='checked'"; } ?>/></td>
    <td><?php echo $hydra_template->drawLabel(HL_SHARE_ENTRY);?></td>
  </tr>
  </table>
</fieldset>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'calendar');
echo $hydra_template->drawInput('hidden', 'cmd', 'create_entry');
echo $hydra_template->drawInput('hidden', 'day', $day);
echo $hydra_template->drawInput('hidden', 'id', $id);
?>
<script type="text/javascript" language="javascript">
function validateCreate()
{
	var d     = document.adminForm;
	var valid = 1;
	var newday   = 0;
	
	if (d.title.value == '') {
		valid = 0;
		document.getElementById('title_lbl').style.color = '#FFFFFF';
		document.getElementById('title_lbl').style.background = '#cc0000';
	}
	
	if (d.start_date.value == '') {
		valid = 0;
		document.getElementById('start_date_lbl').style.color = '#FFFFFF';
		document.getElementById('start_date_lbl').style.background = '#cc0000';
	}
	
	if (d.end_date.value == '') {
		valid = 0;
		document.getElementById('end_date_lbl').style.color = '#FFFFFF';
		document.getElementById('end_date_lbl').style.background = '#cc0000';
	}
	
	if (valid == 1) {
		newday      = d.start_date.value.split('-');
		newday      = newday[2];
		d.day.value = newday;
		<?php getEditorContents('details_editor','details');?>
		d.submit();
	}
}
</script>