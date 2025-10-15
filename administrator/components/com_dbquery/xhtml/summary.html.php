<?php

defined('_VALID_MOS') or die(_LANG_NO_ACCESS);
global  $mosConfig_live_site, $globals;

if ( method_exists(new mosCommonHTML, 'loadOverlib')) {
	mosCommonHTML::loadOverlib();
} else {
	?>
	<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
	<?php
}

$n = count( $rows );
?>
  <form action="index2.php" method="get" name="adminForm" id="adminForm">
    <table class="adminheading" style="" cellpadding="4" cellspacing="0" border="0" width="100%">
    
    
      <tr>
        <th width="100%" align="left" style="background:url(components/com_dbquery/images/dbq_small.jpg) no-repeat center left;">
           <?php echo $screenName; ?>
        </th>
<?php
if ( count($lists) ) {
	echo '<td>&nbsp;<br/>'._LANG_FILTER.'</td>';
	foreach ( $lists as $listname => $list ) {
		echo '<td align="left">'.$listname.'<br/>'.$list.'</td>';
	}
	//echo '<td><input type="reset"></td>'; // doesn't work
}
?>
      </tr>
    </table>

	  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
    	<tr>
<?php

// Retrieve class information so that we know which standard fields we should display
$class = get_class($obj);
$class_vars = get_class_vars($class);

$n = count($rows);

if ( array_key_exists('checked_out', $class_vars) ) {
	// Only for classes supporting checkout
	$this->header('#');
	$this->headerCheckbox($n);
	$this->header(_LANG_ID);
} else if ( array_key_exists('directory', $class_vars) ) {
	// Only for classes that don't the the database but uses the file system
	$this->header('#');
	$this->header('&nbsp;');	// Files cannot be processed in groups
}

foreach ($headers as $header) {
	$this->header($header);
}

// If we support ordering, display a column for this
if ( array_key_exists('ordering', $class_vars) ) {
	$this->headerOrder($n);
}

// Display header for classes that support error reporting
if ( $this->supportsErrorReportingInAdminConsole() ) {
	$this->header(_LANG_ERRORS);
}

echo '</tr>';


$k = 0;

for ($i = 0, $n; $i < $n; $i ++) {
	$row =& $rows[$i];
	$this->setObject($row);
	if (@! $row) continue;
	echo "<tr class=\"row$k\">";
	if ( array_key_exists('checked_out', $class_vars) ) {
		// Only for classes supporting checkout
		//if ( function_exists('mosCommonHTML::CheckedOutProcessing')) {
		if ( method_exists(new mosCommonHTML, 'CheckedOutProcessing')) {
    			$checked = mosCommonHTML::CheckedOutProcessing( $row, $i );
		} else {
			global $my;
			$checked = mosHTML::idBox( $i, $row->id, ($row->checked_out && $row->checked_out != $my->id ) );
		}
		echo '  <td align="center" width="5">'.$pageNav->rowNumber($i).'</td>';
		echo '  <td align="center" width="5">'.$checked.'</td>';
		echo '  <td align="center" width="5">'.$row->id.'</td>';
	} else if ( array_key_exists('directory', $class_vars) ) {
		$checked = mosHTML::idBox( $i, $row->directory );
		echo '  <td align="center" width="5">'.$pageNav->rowNumber($i).'</td>';
		echo '  <td align="center" width="5">'.$checked.'</td>';
	}
	foreach (array_keys($headers) as $field) {
		$this->field($field, $i);
	}
	if ( array_key_exists('ordering', $class_vars) ) {
		// Only for classes supporting ordering
		echo '<td align="center" colspan="2" width="20"><input type="text" name="order[]" size="5" value="'.$row->ordering.'" class="text_area" style="text-align: center" /></td>';
		echo '<td align="center">' . $pageNav->orderUpIcon( $i ) .'</td>';
		echo '<td align="center">' . $pageNav->orderDownIcon( $i, $n ) .'</td>';
	} 
	
	// Print a link to related error messages
	if (  $this->supportsErrorReportingInAdminConsole() ) {
	  if ( strtolower($class) == 'dbq_variable') 
			$class = $class .'_'.$row->type;
		echo '<td align="center"><a href="'.$this->getURL('errors').'&task=show&oid='.$row->id.'&source='.$class.'">'._LANG_ERRORS.'</td>';
	}
	echo '</tr>'."\n";
	$k = 1 - $k;
?>

<?php

}
?>
	</table>
    <?php echo $pageNav->getListFooter(); ?>
      <input type="hidden" name="option" value="<?php echo $globals->option; ?>">
      <input type="hidden" name="task" value="show"> 
      <input type="hidden" name="act" value="<?php echo $globals->act; ?>"> 
      <input type="hidden" name="boxchecked" value="0">
      <input type="hidden" name="hidemainmenu" value="0" />
      <input type="hidden" name="userinput" value=""/>
  </form>