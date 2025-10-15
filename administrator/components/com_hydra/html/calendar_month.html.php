<?php
/**
* $Id: calendar_month.html.php 16 2007-04-15 12:18:46Z eaxs $
* @package   Hydra
* @copyright Copyright (C) 2006 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
*
* Hydra is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
**/

defined ( '_VALID_MOS' ) OR DIE( 'Direct access is not allowed' );

global $hydra_template, $hydra, $protect;

mosCommonHTML::loadOverlib();

$cal = new HydraCalendar();

$year           = date("Y");
$month          = date("n");
$today          = date("j");
$days_of_month  = date("t", mktime(0,0,0,$month,1,$year));
$first_day      = date("w",mktime(0,0,0,$month,1,$year)) - 1;
$counter        = 0;
$current_day    = 1;
$day            = $cal->day;

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
<table class="listTable" width="100%" cellpadding="1" cellspacing="1">
  <tr>
    <th align="left" width="14%"><?php echo HL_DAY_MONDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_TUESDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_WEDNESDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_THURSDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_FRIDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_SATURDAY;?></th>
    <th align="left" width="14%"><?php echo HL_DAY_SUNDAY;?></th>
  </tr>
  <?php
  for ($i = 0; $i < $cal->days + $cal->month_start; $i++)
  {
  	 if ($counter == 0) { echo "\n <tr>"; } 
  	 
  	 $we = '';
  	 
  	 if ($counter >= 5) { $we = '_we'; }
  	 
  	 if ($i < $cal->month_start) {
  	 	echo "\n <td>&nbsp;</td>";
  	 }
  	 else {
  	   if ($cal->today == $current_day) { 	   	
         ?>
         <td align='left' valign='top'>
           <div class='cal_block<?php echo $we;?>' onclick="showDay(<?php echo $current_day;?>);" onmouseover="blockHover(this);" onmouseout="blockOut(this);">
             <div class="cal_day_active<?php echo $we;?>"><?php echo $current_day;?></div>
             <?php echo $cal->countEvents($current_day);?>
           </div>
         </td>
         <?php 
  	   }
  	   
  	   else {
  	   	 ?>
         <td align='left' valign='top'>
           <div class='cal_block<?php echo $we;?>' onclick="showDay(<?php echo $current_day;?>);" onmouseover="blockHover(this);" onmouseout="blockOut(this);">
             <div class="cal_day<?php echo $we;?>"><?php echo $current_day;?></div>
             <?php echo $cal->countEvents($current_day);?>
           </div>
         </td>
         <?php
  	   }
       $current_day++;
  	 }  
  	 
  	 if ($counter == 6) { 
        $counter = -1;
     	echo "\n </tr>"; 
     }    
     $counter++;
  }
  ?>
  </table>
</div>
<div class="tableContainer_footer" align="center">&nbsp;</div>
</div>  
<?php
echo $hydra_template->drawInput('hidden', 'option', 'com_hydra');
echo $hydra_template->drawInput('hidden', 'area', 'calendar');
echo $hydra_template->drawInput('hidden', 'cmd', '');
?>

