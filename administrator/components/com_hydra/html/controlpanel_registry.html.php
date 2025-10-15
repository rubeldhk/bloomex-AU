<?php
/**
* $Id: controlpanel_registry.html.php 16 2007-04-15 12:18:46Z eaxs $
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

global $hydra, $hydra_template;

$registry = $hydra->loadRawRegistry();
$total    = count($registry);
?>
<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_REG_WARNING;?></legend>
<table class="formTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2" width="100%" valign="top"><strong><?php echo HL_REG_WARNING_TXT;?></strong></td>
  </tr>
  <tr>
    <td colspan="2" width="100%" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_AREA);?></td>
    <td width="80%" valign="top"><?php echo HL_REG_AREA_HLP;?></td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_CMD);?></td>
    <td width="80%" valign="top"><?php echo HL_REG_CMD_HLP;?></td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_USER_TYPE);?></td>
    <td width="80%" valign="top">
      <?php echo HL_REG_USER_TYPE_HLP;?>
      <br/><br/>
      <?php echo HL_USER_TYPE;?>:
      <ul>
        <li><strong>0</strong> = <?php echo HL_USER_TYPE_CLIENT;?> <?php echo HL_OR_HIGHER;?></li>
        <li><strong>1</strong> = <?php echo HL_USER_TYPE_MEMBER;?> <?php echo HL_OR_HIGHER;?></li>
        <li><strong>2</strong> = <?php echo HL_USER_TYPE_GROUPLEADER;?> <?php echo HL_OR_HIGHER;?></li>
        <li><strong>3</strong> = <?php echo HL_USER_TYPE_ADMINISTRATOR;?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_AREA_LABEL);?></td>
    <td width="80%" valign="top"><?php echo HL_REG_AREA_LABEL_HLP;?></td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_CMD_LABEL);?></td>
    <td width="80%" valign="top"><?php echo HL_REG_CMD_LABEL_HLP;?></td>
  </tr>
  <tr>
    <td width="20%" valign="top"><?php echo $hydra_template->drawLabel(HL_REG_INHERIT);?></td>
    <td width="80%" valign="top"><?php echo HL_REG_INHERIT_HLP;?></td>
  </tr>
</table> 
</fieldset>
&nbsp;

<fieldset class='formFieldset'>
<legend class='formLegend'><?php echo HL_REG_ADD_ENTRY;?></legend>
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th align="left"><?php echo HL_REG_AREA;?></th>
    <th align="left"><?php echo HL_REG_CMD;?></th>
    <th align="center"><?php echo HL_REG_USER_TYPE;?></th>
    <th align="left"><?php echo HL_REG_AREA_LABEL;?></th>
    <th align="left"><?php echo HL_REG_CMD_LABEL;?></th>
    <th align="left"><?php echo HL_REG_INHERIT;?></th>
  </tr>
  <tr class="row0">
    <td align="left"><?php echo $hydra_template->drawInput("text", "new_reg[area]", '', '', "size='14' maxlength='56'") ;?></td>
  	 <td align="left"><?php echo $hydra_template->drawInput("text", "new_reg[command]", '', '', "size='14' maxlength='56'");?></td>
  	 <td align="center"><?php echo $hydra_template->drawInput("text", "new_reg[user_type]", '0', '', "size='5' maxlength='1'");?></td>
  	 <td align="left"><?php echo $hydra_template->drawInput("text", "new_reg[area_label]", '', '', "size='14' maxlength='124'");?></td>
  	 <td align="left"><?php echo $hydra_template->drawInput("text", "new_reg[command_label]", '', '', "size='14' maxlength='124'");?></td>
  	 <td align="left"><?php echo $hydra_template->drawInput("text", "new_reg[inherit]", '', '', "size='14' maxlength='56'");?></td>
  </tr>
</table>
<a class="boxButton" style="cursor:pointer; width:10%" onclick="addEntry();"><?php echo $hydra->load('img', '16_tick.gif', "alt='".HL_ADD."' align='left'")."&nbsp;&nbsp;".HL_ADD;?></a>
  
</fieldset>
&nbsp;

<div class="tableContainer">
<div class="tableContainer_header"><?php echo HL_REGISTRY;?></div>
<div class="tableContainer_body">
<table class="listTable" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center">#</th>
    <th align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" /></th>
    <th align="left"><?php echo HL_REG_AREA;?></th>
    <th align="left"><?php echo HL_REG_CMD;?></th>
    <th align="center"><?php echo HL_REG_USER_TYPE;?></th>
    <th align="left"><?php echo HL_REG_AREA_LABEL;?></th>
    <th align="left"><?php echo HL_REG_CMD_LABEL;?></th>
    <th align="left"><?php echo HL_REG_INHERIT;?></th>
  </tr>
  <?php
  $k = 0;
  for($i = 0; $i < $total; $i++)
  {
  	   $reg = $registry[$i];
  	   ?>
  	   <tr class="row<?php echo $k;?>">
  	     <td align="center"><?php echo ($i+1);?></td>
  	     <td align="center"><?php echo mosHTML::idBox($i, $reg->id);echo $hydra_template->drawInput("hidden", "reg[$i][id]", $reg->id) ; ?></td>
  	     <td align="left"><?php echo $hydra_template->drawInput("text", "reg[$i][area]", $reg->area, '', "size='14'") ;?></td>
  	     <td align="left"><?php echo $hydra_template->drawInput("text", "reg[$i][command]", $reg->command, '', "size='14'");?></td>
  	     <td align="center"><?php echo $hydra_template->drawInput("text", "reg[$i][user_type]", $reg->user_type, '', "size='5'");?></td>
  	     <td align="left"><?php echo $hydra_template->drawInput("text", "reg[$i][area_label]", $reg->area_label, '', "size='14'");?></td>
  	     <td align="left"><?php echo $hydra_template->drawInput("text", "reg[$i][command_label]", $reg->command_label, '', "size='14'");?></td>
  	     <td align="left"><?php echo $hydra_template->drawInput("text", "reg[$i][inherit]", $reg->inherit, '', "size='14'");?></td>
  	   </tr>
  	   <?php
  	   $k = 1 - $k;
  }
  ?>
</table>
</div>
<div class="tableContainer_footer" align="center">&nbsp;</div>
</div>
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'controlpanel');
echo $hydra_template->drawInput('hidden', 'cmd');
echo $hydra_template->drawInput('hidden', 'boxchecked');
?>
<script type="text/javascript" language="javascript">
function updateRegistry()
{
	document.adminForm.cmd.value = 'update_registry';
	document.adminForm.submit();
}

function addEntry()
{
	document.adminForm.cmd.value = 'add_registry';
	document.adminForm.submit();
}

function deleteRegistry()
{
	if (document.adminForm.boxchecked.value.length < 1) {
		alert("<?php echo HL_REG_DEL_ENTRIES_WARN;?>");
		return false;
	}
	if (confirm("<?php echo HL_REG_DEL_ENTRIES_CONFIRM;?>")) {
	   document.adminForm.cmd.value = 'del_registry';
	   document.adminForm.submit();
	}   
}
</script>