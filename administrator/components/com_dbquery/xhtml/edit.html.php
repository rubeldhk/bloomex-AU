<?php

/***************************************
 * $ver$
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @ $version$
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $option, $task, $act;
//echo "file to edit is $fileToEdit";
?>

<form action="index2.php" method="post" name="adminForm">
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td width="50%"><table class="adminheading"><tr><th class="templates"><?php echo _LANG_FILE_EDITOR; ?></th></tr></table></td>
	<td width="200">
		<span class="componentheading"><?php echo _LANG_FILE_IS; ?>
		<b><?php echo is_writable($fileToEdit) ?  _LANG_IS_WRITABLE :  _LANG_IS_NOT_WRIABLE; ?></b>
		</span>
	</td>
<?php

// Make a drop down menu to allow users to select other files
if ( count($lists) ) {
	echo '<td>&nbsp;<br/>'._LANG_FILTER.'</td>';
	foreach ( $lists as $listname => $list ) {
		echo '<td align="left">'.$listname.'<br/>'.$list.'</td>';
	}
}
?>
</tr>
</table>
<table class="adminform">
	<tr><th><?php echo $fileToEdit ?></th></tr>
	<tr><td><textarea style="width:99%;height:500px" name="filecontent" class="inputbox"><?php echo htmlspecialchars($content, ENT_NOQUOTES); ?></textarea></td></tr>
</table>
<input type="hidden" name="<?php echo $directoryIdentifier; ?>" value="<?php echo $directory; ?>" />
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="act" value="<?php echo $act;?>" />
<input type="hidden" name="task" value="edit" />
</form>	
hi