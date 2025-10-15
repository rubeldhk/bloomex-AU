<?php
/**
* @version $Id: mod_tabmodul.php,v 3.0
* @package Joomla
* @copyright (C) 2005 Soner Ekici - www.sonerekici.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mainframe, $database, $acl, $my, $mosConfig_absolute_path, $mosConfig_live_site;

$position	= $params->get( 'position', 'left');
$cookies	= $params->get( 'cookies', '0');
$css		= $params->get( 'css','0');
$cssfile	= $params->get( 'cssfile','');

include_once($mosConfig_absolute_path.'/modules/mod_tabs/tabfunc.php');

$mod_id = rand(5, 15);

$query = "SELECT id, title, module, position, content, showtitle, params"
		. "\n FROM #__modules AS m"
		. "\n INNER JOIN #__modules_menu AS mm ON mm.moduleid = m.id"
		. "\n WHERE m.published = 1"
		. "\n AND m.position='$position'"
		. "\n AND m.access <= '". $my->gid ."'"
		. "\n AND m.client_id != 1"
		. "\n AND ( mm.menuid = '". $Itemid ."' OR mm.menuid = 0 )"
		. "\n ORDER BY ordering";

	$database->setQuery( $query );
	$modules = $database->loadObjectList();
	if($database->getErrorNum()) {
		echo "MA ".$database->stderr(true);
		return;
	}
	
if (!$css) {
?>
<link href="<?php echo $mosConfig_live_site;?>/modules/mod_tabs/tabcontent.css" rel="stylesheet" type="text/css" />
<?php } else {
?>	
<link href="<?php echo $mosConfig_live_site;?><?php echo $cssfile;?>" rel="stylesheet" type="text/css" />
<?php	
}
?>
<div id="maintab<?php echo $mod_id;?>" class="shadetabs">
<?php
foreach ($modules as $module) {
?>
<span class="selected"><a href="#" rel="content<?php echo $module->id;?>"><?php echo $module->title;?></a></span>
<?php } ?>
</div>

<div class="tabcontentstyle">
<?php
foreach ($modules as $module) {
$params =new  mosParameters( $module->params );	
?>
<div id="content<?php echo $module->id;?>" class="tabcontent">
<?php
if ((substr("$module->module",0,4))=="mod_") {
			LoadModule( substr( $module->module, 4 ), $params );
		} else {
			echo $module->content;
		}
?>
</div>
<?php } ?>

</div>

<script type="text/javascript">
initializetabcontent("maintab<?php echo $mod_id;?>")
</script>
