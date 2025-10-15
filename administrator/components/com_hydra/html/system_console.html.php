<?php
/**
* $Id: system_console.html.php 21 2007-04-16 10:47:37Z eaxs $
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

global $hydra_template;

$tabs = new mosTabs(1);
?>
<br style="clear:both"/>
<?php
echo $hydra_template->drawForm('hydra_debug');
?>
    <?php $tabs->startPane('hydra_debugger'); ?>  
    
    <!-- System START -->
    <?php 
    $tabs->startTab("System", 'tab_system'); 
    ?>
    <div class='debug_log'>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">General Information</legend>
    <table width="100%" cellpadding="2" cellspacing="0" >
      <tr>
        <td width="15%"><strong>Project Fork Version</strong></td>
        <td width="30%"><?php echo $hydra_version;?></td>
        <td width="15%"><strong>SEO</strong></td>
        <td width="30%"><?php echo $seo;?></td>
      </tr>
      <tr>
        <td width="15%"><strong>Joomla Version</strong></td>
        <td width="30%"><?php echo $joomla_version;?></td>
        <td width="15%"><strong>Server</strong></td>
        <td width="30%"><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
      </tr>
    </table>
    </fieldset>
    <br/>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">Performance</legend>
    <table width="100%" cellpadding="2" cellspacing="0" >
      <tr>
        <td width="15%" align="left"><strong>Runtime</strong></td>
        <td width="35%" align="left"><?php echo $runtime;?></td>
        <td width="50%" align="left">Script runtime in seconds</td>
      </tr>
    </table>
    </fieldset>
    </div>
    <?php 
    $tabs->endTab(); 
    ?>
    <!-- System END -->
    
    
    <!-- User START -->
    <?php 
    $tabs->startTab("User", 'tab_user'); 
    ?>
    <div class='debug_log'>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">General Information</legend>
    <table width="100%" cellpadding="2" cellspacing="0" >
      <tr>
        <td width="15%" align="left"><strong>Joomla ID</strong></td>
        <td width="30%" align="left"><?php echo $jid;?></td>
        <td width="15%" align="left"><strong>Usertype</strong></td>
        <td width="30%" align="left"><?php echo $usertype;?></td>
      </tr>
      <tr>
        <td width="15%" align="left"><strong>Project Fork ID</strong></td>
        <td width="30%" align="left"><?php echo $hid;?></td>
        <td width="15%" align="left"><strong>Permissions</strong></td>
        <td width="30%" align="left"><?php echo $my_total_perms."/".$total_perms;?></td>
      </tr>
      <tr>
        <td width="15%" align="left"><strong>Operating System</strong></td>
        <td width="30%" align="left"><?php echo $os;?></td>
        <td width="15%" align="left"><strong>Browser</strong></td>
        <td width="30%" align="left"><?php echo $browser;?></td>
      </tr>
    </table>
    </fieldset>
    <br/>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">Profile</legend>
    <table class="listTable" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <th width="10%" align="left">#</th>
        <th width="40%" align="left">Parameter</th>
        <th width="50%" align="left">Value</th> 
      </tr>
      <?php echo $profile;?>
    </table>
    </fieldset>
    </div>
    <?php
    $tabs->endTab(); 
    ?> 
    <!-- User END -->
    
    
    <!-- Environment START -->
    <?php 
    $tabs->startTab("Environment", 'tab_environment'); 
    ?>
    <div class='debug_log'>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">My usergroups</legend>
      <table class="listTable" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th width="10%" align="left">ID</th>
          <th width="90%" align="left">Name</th>
        </tr>
        <?php echo $groups;?>
      </table>
    </fieldset>
    <br/>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">Known users</legend>
      <table class="listTable" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th width="10%" align="left">ID</th>
          <th width="90%" align="left">Name</th>
        </tr>
        <?php echo $users;?>
      </table>
    </fieldset>
    <br/>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">My Projects</legend>
      <table class="listTable" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th width="10%" align="left">ID</th>
          <th width="90%" align="left">Name</th>
        </tr>
        <?php echo $projects;?>
      </table>
    </fieldset>
    <br/>
    <fieldset class='formFieldset'>
    <legend class='formLegend' style="background:#FFFFFF">My Permissions</legend>
      <table class="listTable" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th width="40%" align="left">Area</th>
          <th width="60%" align="left">Command</th>
        </tr>
        <?php echo $my_perms;?>
      </table>
    </fieldset>
    </div>
    <?php
    $tabs->endTab(); 
    ?> 
    <!-- Environment END -->
    
    
    <!-- Notices START -->
    <?php 
    $tabs->startTab("Notices($total_notices)", 'tab_notices'); 

    echo $output_notices;

    $tabs->endTab(); 
    ?> 
    <!-- Notices END -->
    
    
    <!-- Warnings START -->
    <?php 
    $tabs->startTab("Warnings($total_warnings)", 'tab_warnings'); 

    echo $output_warnings;

    $tabs->endTab(); 
    ?> 
    <!-- Warnings END -->
    
    
    <!-- Errors START -->
    <?php 
    $tabs->startTab("Errors($total_errors)", 'tab_errors'); 

    echo $output_errors;

    $tabs->endTab(); 
    ?> 
    <!-- Errors END -->
    
    
    <!-- Violations START -->
    <?php 
    $tabs->startTab("Violations($total_violations)", 'tab_violations'); 

    echo $output_violations;

    $tabs->endTab();
     
    ?> 
    <!-- Violations END -->
    <?php 
    $tabs->startTab('Save log', 'tabe_save');
    ?>
    
    <table class="hydra_debugger" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
    <tr>
    <td align="center">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center"><a class="boxButton" style="text-align:center" href="javascript:document.hydra_debug.submit()">Save Logfile</a></td>
      </tr>
    </table>
    
    <?php $tabs->endTab();$tabs->endPane(); ?> 
     
    </td>
  </tr>
</table>
<input type="hidden" name="option" value="com_hydra" />
<input type="hidden" name="cmd" value="store_log" />
<?php
echo $hidden_fields;
echo $hydra_template->drawForm('hydra_debug', '', true);
?>