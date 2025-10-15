<?php
/**
 * Enter description here...
 *
 * @package JL
 */

/**
 * Enter description here...
 *
 * @package JL
 */
class JoomlaLibHTML {
	
	/**
	 * View application list.
	 *
	 * @param array $list
	 */
	function viewApp($list, $pageNav, $search=''){
		global $mosConfig_live_site;
		print "<link rel=\"stylesheet\" href=\"$mosConfig_live_site/administrator/components/com_joomlalib/admin.joomlalib.css\" type=\"text/css\"/>";
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th class="joomlalib">
				Applications
				</th>
				<td>
				Filter:
				</td>
				<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="2%" class="title">
			#
			</th>
			<th width="3%" class="title">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($list); ?>);" />
			</th>
			<th width="10%" class="title">
			Handle
			</th>
			<th width="15%" class="title">
			Short Description
			</th>
			<th class="title" >
			Long Description
			</th>
			<th width="1%" class="title">
			ID
			</th>
		</tr>
		<?php
		$k = 0;
		$i = 0;
		foreach($list as $obj){
			print '<tr class="row'.$k.'">';
			print '<td>'.($i+1).'</td>';
			print '<td>'.mosHTML::idBox( $i, $obj->jlappid ).'</td>';
			print '<td>'.$obj->apphandle.'</td>';
			print '<td>'.$obj->appdescshort.'</td>';
			print '<td>'.$obj->appdesclong.'</td>';
			print '<td>'.$obj->jlappid.'</td>';
			print '</tr>';
			/* loop vars */
			$k = 1 - $k;
			$i++;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		
		<input type="hidden" name="act" value="jlapp" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="option" value="com_joomlalib" />
		</form>
		<?php
	}
	
	function editApp($row){
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminlist">
			<tr>
				<th class="title" colspan="2">
				Set Decription for <?php echo $row[1]; ?>
				</th>
			</tr>
			<tr>
				<td width="10%" class="title">
				Short Description:
				</td>
				<td>
					<input type="text" class="adminForm" id="appdescshort" name="appdescshort" maxlength="50" value="<?php echo $row[2]; ?>">
				</td>
			</tr>
			<!--<tr>
				<td width="15%" class="title">
				Long Description
				</td>
				<td>
					<textarea class="adminForm" id="appdesclong" name="appdesclong" cols="50" rows="2"><?php echo $row[3]; ?></textarea>
				</td>
			</tr>-->
		</table>
		<input type="hidden" name="cid[]" value="<?php echo $row[0]; ?>" />
		<input type="hidden" name="act" value="jlapp" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_joomlalib" />
		</form>
		<?php
	}

	function viewJJLog($objList, $pageNav, $lists, $search='', $aplicationList){
		global $mosConfig_live_site;
		print "<link rel=\"stylesheet\" href=\"$mosConfig_live_site/administrator/components/com_joomlalib/admin.joomlalib.css\" type=\"text/css\"/>";
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th class="joomlalib">
				Log Browser
				</th>
				<td>
				Filter:
				</td>
				<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
				</td>
				<td width="right">
				<?php echo $lists['level'];?>
				</td>
				<td width="right">
				<?php echo $lists['apps'];?>
				</td>
			</tr>
		</table>
		
		<table class="adminlist">
		<tr>
			<th width="1%" class="title">
			#
			</th>
			<th width="3%" class="title">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($objList); ?>);" />
			</th>
			<th width="1%" class="title">
			Level
			</th>
			<th width="10%" class="title" >
			Application
			</th>
			<th width="10%" class="title" nowrap="nowrap">
			Date
			</th>
			<th class="title">
			Messages
			</th>
		</tr>
		<?php
		$k=0;
		$i=0;
		foreach($objList as $obj){
			print '<tr class="row'.$k.'">';
			print '<td>'.($i+1+$pageNav->limitstart).'</td>';
			print '<td>'.mosHTML::idBox( $i, isset($obj->jllogid) ? $obj->jllogid : '' ).'</td>';
			print '<td>'.$obj->level.'</td>';
			print '<td>'.$aplicationList[$obj->jlappid]->apphandle.'</td>';
			print '<td nowrap="nowrap">'.date( 'j-n-Y H:i:s', $obj->whentime).'</td>';
			print '<td>'.$obj->logtext.'</td>';
			print '</tr>';
			$k = 1 - $k;
			$i++;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		
		<input type="hidden" name="act" value="jllog" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="option" value="com_joomlalib" />
		</form>
	<?php
	}
	
	
	function JLLogMaintain($totalCount, $totalSize, $applications, $lists){
		/* global cleaning options */
		?>
		<form action="index2.php" method="post" name="adminForm">
		<fieldset>
		<legend>Select Proces:</legend>
		<table class="adminlist">
			<tr class="row0">
				<td style="font-size: 110%;"><label for="purgeDB"><b>purge database</b>,<br />
				This completely removes all entries, meaning nothing is kept.<br />
				Approximatly<b> <?php echo $totalSize; ?>Kb</b> will be cleared.</label></td>
				<td width="5%"><input type="radio" id="purgeDB" name="type" value="1"></td>
			</tr>
			<tr class="row1">
				<td style="font-size: 110%;"><label for="cleanDB"><b>Clean database</b>,<br />
				This keeps a percentage of the entries and deletes the rest. If one of your applications has been reporting like crazy then there is a fair change only reports from this application are kept as this keeps the % newest entries.<br /></label>
				<?php echo $lists['cleanDatabase']; ?><label for="cleanDB">The size in curly bracers is the aproximate size that will be cleared.		
				</label></td>
				<td width="5%"><input type="radio" id="cleanDB" name="type" value="2"></td>
			</tr>
			<tr class="row0">
				<td style="font-size: 110%;"><label for="purgeApp"><b>purge application</b>,<br />
				This will completely remove all entries from selected application.<br /></label>
				<?php echo $lists['purgeApp']; ?><label for="purgeApp">The size in curly bracers is the aproximate size that will be cleared.
				</label></td>
				<td width="5%"><input type="radio" id="purgeApp" name="type" value="3"></td>
			</tr>
			<!---
			<tr class="row1">
				<td style="font-size: 125%;"><label for="cleanApp"><b>clean application</b>,<br />
				This will keep 25% off newest entries and delete the rest from seleceted application.<br />
				<?php echo $lists['cleanApp']; ?>The size in curly bracers is the aproximate size that will be cleared.
				</label></td>
				<td width="5%"><input type="radio" id="cleanApp" name="type" value="4"></td>
			</tr>
			-->
			<tr class="row1">
				<td style="font-size: 110%;"><label for="removeUnkown"><b>Remove orphaned logs</b>,<br />
				This will remove all orphaned entries.<br />
				</label></td>
				<td width="5%"><input type="radio" id="removeUnkown" name="type" value="5"></td>
			</tr>
		</table>
		<br />
		<input type="submit" value="Perform action">
		</fieldset>
		<br />
		<fieldset>
		<legend>JLLog Statistics</legend>
		<table class="adminlist">
			<tr>
				<th class="title">Application</th>
				<th align="left">Log entries</th>
				<th align="left">Estimate Size</th>
			</tr>
		<?php
		$k = 0;
		foreach($applications as $id => $info){
			$app = empty($info['desc']) ? 'unKnown' : $info['desc'];
			$size = round($info['size']);
			$count = $info['count'];
			print '<tr class="row'.$k.'"><td>'.$app.'</td>';
			print "<td>$count</td>";
			print "<td>$size Kb</td></tr>\n";
			$k = 1-$k;
		}
		?>
			<tr>
				<th class="title">Total</th>
				<th align="left"><?php echo $totalCount; ?></th>
				<th align="left"><?php echo $totalSize; ?> Kb</th>
			</tr>
		</table>
		</fieldset>
		
		<input type="hidden" name="act" value="jllog" />
		<input type="hidden" name="task" value="clean" />
		<input type="hidden" name="option" value="com_joomlalib" />
		</form>
		<?php
	}
	
	/**
	 * Default header, with joomlalib logo
	 *
	 * @param string Title you want to display.
	 */
	function defaultHeader($title){
		global $mosConfig_live_site;
		print "<link rel=\"stylesheet\" href=\"$mosConfig_live_site/administrator/components/com_joomlalib/admin.joomlalib.css\" type=\"text/css\"/>";
		?>
		<table class="adminheading">
			<tr>
				<th class="joomlalib">
				<?php echo $title; ?>
				</th>
			</tr>
		</table>
		<?php
	}
	
	function aboutPage(){
		?>
		<table>
			<tr>
				<td width="70%">
					<p><strong>Joomlalib</strong> is written by Brent Stolle and Michiel Bijland (&copy;) 2006</p>
					<p>This software is FREE. Please distribute it under the terms of GNU/GPL license.<br />
					See <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a> GNU/GPL for details.</p>
					<p>If you want to distribute this software with a closed-source product,<br />
					contact either Brent or Michiel about obtaining a commercial license for <strong>Joomlalib</strong>.<br />
					Please keep in mind that certain 3rd-party packages which we have linked to are incompatible with non-gpl licenses.</p>
					<p>How to contact Michiel or Brent:<br />
					E-mail: <a href="mailto://contact-joomlalib@4theweb.nl">contact-joomlalib@4theweb.nl</a><br />
					<a href="http://joomlalib.4theweb.nl/wiki/">http://joomlalib.4theweb.nl/wiki/</a>
					</p>
				</td>
				<td align="right">
				<img src="components/com_joomlalib/images/fullsize.png">
				</td>
			</tr>
		</table>
		<?php
	}
}
?>