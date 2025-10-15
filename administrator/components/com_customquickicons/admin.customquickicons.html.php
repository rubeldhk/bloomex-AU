<?php

/**
* @version 1.0
* @package Custom QuickIcons
* @copyright (C) 2005 Halil Köklü <halilkoklu at gmail dot com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or
  die( 'Direct Access to this location is not allowed.' );

/**
 * @package Custom QuickIcons
 */
class QuickIcons {

	function show(&$rows, $option) {

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
				Custom QuickIcons
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
				#
			</th>
			<th width="20" class="title">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th width="43%" class="title">
				<?php echo _QI_NAME; ?>
			</th>
			<th width="7%" nowrap="true">
				<?php echo _QI_PUBLISHED; ?>
			</th>
			<th colspan="2" nowrap="nowrap" width="7%">
				<?php echo _QI_REORDER; ?>
			</th>
			<th width="2%">
				<?php echo _QI_ORDER; ?>
			</th>
			<th width="1%">
				<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" title="<?php echo _QI_SAVE_ORDER; ?>"><img src="images/filesave.png" border="0" width="16" height="16" alt="<?php echo _QI_SAVE_ORDER; ?>" /></a>
			</th>
			<th width="43%" class="title">
				<?php echo _QI_TARGET; ?>
			</th>
		</tr>
		<?php
		$k=0;
		for ($i=0; $i<count($rows); $i++ )
		{
			$row = $rows[$i];
			$editLink 	= 'index2.php?option=com_customquickicons&task=edit&id='. $row->id;
			$link 	= 'index2.php?option=com_customquickicons&task=';

			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _QI_UNPUBLISH : _QI_PUBLISH;

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $editLink; ?>" title="Edit QuickIcon"><?php echo $row->title; ?></a>
				</td>
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img; ?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td align="center">
					<a href="<?php echo $link . "orderUp&id=". $row->id; ?>" title="<?php echo _QI_ORDER_UP; ?>"><img src="images/uparrow.png" width="12" height="12" border="0" alt="<?php echo _QI_ORDER_UP; ?>" /></a>
				</td>
				<td align="center">
					<a href="<?php echo $link . "orderDown&id=". $row->id; ?>" title="<?php echo _QI_ORDER_DOWN; ?>"><img src="images/downarrow.png" width="12" height="12" border="0" alt="<?php echo _QI_ORDER_DOWN; ?>" /></a>
				</td>
				<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
					<?php echo $row->target; ?>
				</td>
			</tr>
		<?php
			$k = 1-$k;
		}
		?>
		</table>
		<br />
		<div align="center">
			Copyright &copy; 2005 - Custom QuickIcons by <a href="mailto:halilkoklu@gmail.com">Halil K&ouml;kl&uuml;</a>
		</div>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	}
	
	function edit(&$row, $list, $option) {
		mosMakeHtmlSafe( $row, ENT_QUOTES );
	?>
	
		<script language="JavaScript" type="text/javascript">
		<!--
			function string_replace(string, search, replace) {
   			var new_string = "";
   			var i = 0;
   		
   			while(i < string.length) {
      		if(string.substring(i, i + search.length) == search) {
        		new_string = new_string + replace;
         		i = i + search.length - 1;
      		}
      		else
         		new_string = new_string + string.substring(i, i + 1);
  				i++;
  			}
				return new_string;
			}

			function applyTag(tag, obj) {
				var pre = document.adminForm.prefix;
				var post = document.adminForm.postfix;
				
				if (!obj.checked) {
					pre.value = string_replace(pre.value, '<'+tag+'>', '');
					post.value = string_replace(post.value, '</'+tag+'>', '');
				}
				else 
				{
					pre.value = '<'+tag+'>' + pre.value;
					post.value = post.value + '</'+tag+'>';
				}
			}
			
			function changeIcon(path) {
				if (document.all)
					document.all.iconImg.src = '<?php echo $GLOBALS['mosConfig_live_site']; ?>/administrator/images/'+path;
				else
					document.getElementById('iconImg').src = '<?php echo $GLOBALS['mosConfig_live_site']; ?>/administrator/images/'+path;
			}
		//-->
		</script>	
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
				<?php echo $row->id ? _QI_DETAIL_EDIT : _QI_DETAIL_NEW;?> QuickIcon
			</th>
		</tr>
		</table>
		
		<table class="adminform">
			<tr>
				<th>
					<?php echo _QI_DETAIL; ?>
				</th>
			</tr>
			<tr>
				<td style="padding-left: 25px">
					<table border="0" cellpadding="0" cellspacing="0" width="75%">
						<tr>
							<td width="20%" align="right">
								<?php echo _QI_PUBLISHED; ?>
							</td>
							<td width="80%">
								<input type="radio" id="published1" name="published" value="1"<?php echo $row->published ? ' checked="checked"' : ''; ?> /><label for="published1"><?php echo _QI_DETAIL_YES; ?></label>
								&nbsp;&nbsp;
								<input type="radio" id="published2" name="published" value="0"<?php echo $row->published ? '' : ' checked="checked"'; ?> /><label for="published2"><?php echo _QI_DETAIL_NO; ?></label>
							</td>
						</tr>
						<tr>
							<td align="right">
								<?php echo _QI_DETAIL_ORDER; ?>
							</td>
							<td>
								<?php echo $list; ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td width="20%" align="right">
								<?php echo _QI_DETAIL_PREFIX; ?>
							</td>
							<td width="80%">
								<input class="inputbox" type="text" name="prefix" size="30" maxlength="100" value="<?php echo $row->prefix; ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">
								<?php echo _QI_DETAIL_TITLE; ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo $row->title; ?>" />
								&nbsp;&nbsp;
								<input type="Checkbox" name="bold" id="bold" onclick="applyTag('b', this)" <?php if (strpos(($row->prefix), "&lt;b&gt;")!== false) echo 'checked="checked"'; ?> /><label for="bold"><b>Bold</b></label>
								<input type="Checkbox" name="italic" id="italic" onclick="applyTag('i', this)" <?php if (strpos(($row->prefix), "&lt;i&gt;")!== false) echo 'checked="checked"'; ?> /><label for="italic"><i>Italic</i></label>
								<input type="Checkbox" name="underlined" id="underlined" onclick="applyTag('u', this)" <?php if (strpos(($row->prefix), "&lt;u&gt;")!== false) echo 'checked="checked"'; ?> /><label for="underlined"><u>Underlined</u></label>
							</td>
						</tr>
						<tr>
							<td align="right">
								<?php echo _QI_DETAIL_POSTFIX; ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="postfix" size="30" maxlength="100" value="<?php echo $row->postfix; ?>" />
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td align="right">
								<?php echo _QI_TARGET; ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="target" size="75" maxlength="255" value="<?php echo $row->target; ?>" />
								&nbsp;&nbsp;
								<input type="checkbox" name="new_window" value="1" id="new_window"<?php echo $row->new_window ? ' checked="checked"' : ''; ?>/> <label for="new_window"><?php echo _QI_DETAIL_NEW_WINDOW; ?></label>
							</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td align="right">
								<?php echo _QI_DETAIL_ICON; ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="icon" size="40" maxlength="75" value="<?php echo $row->icon; ?>" onblur="changeIcon(this.value)" />
								&nbsp;&nbsp;
								<a href="index2.php?option=<?php echo $option; ?>&task=chooseIcon" target="_blank"><b><?php echo _QI_DETAIL_CHOOSE_ICON; ?></b></a>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="padding-top:10px">
								<?php 
									if (empty($row->icon))
										$iconLink = 'blank.png';
									else
										$iconLink = $row->icon;
								?>
								<img id="iconImg" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/administrator/images/<?php echo $iconLink; ?>" />
							</td>
						</tr>
					</table>	
				</td>
			</tr>
		</table>
		<br />
		<div align="center">
			Copyright &copy; 2005 - Custom QuickIcons by <a href="mailto:halilkoklu@gmail.com">Halil Köklü</a>
		</div>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		
	<?php
		
	}
	
	function quickiconButton( $image ) {
		
		$js_action = "window.opener.document.adminForm.icon.value='$image'; window.opener.changeIcon('$image'); window.close()"
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="javascript:void(0)" onclick="<?php echo $js_action; ?>">
					<?php echo mosAdminMenus::imageCheckAdmin( $image, '/administrator/images/', NULL, NULL, $image ); ?>
					<span><?php echo $image; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
	
	function chooseIcon($imgs, $option) {
		
	?>
		<table class="adminheading">
		<tr>
			<th>
				Choose Icon
			</th>
		</tr>
		</table>

		<table class="adminform">
			<tr>
				<th>
					<?php echo _QI_MSG_CHOOSE_ICON; ?>
				</th>
			</tr>
			<tr>
				<td style="padding:30px">
					<div id="cpanel">
					<?php 
						for($i=0;$i<count($imgs);$i++) {
							QuickIcons::quickiconButton($imgs[$i]);
					 	} 
					?>
					</div>
				</td>
			</tr>
		</table>
		<br />
		<div align="center">
			Copyright &copy; 2005 - Custom QuickIcons by <a href="mailto:halilkoklu@gmail.com">Halil K&ouml;kl&uuml;</a>
		</div>
	<?php
		
	}

}

?>
