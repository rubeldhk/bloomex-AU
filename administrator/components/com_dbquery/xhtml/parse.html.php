<?php

defined('_VALID_MOS') or die(_LANG_NO_ACCESS);

$colors = & DBQ_Settings::getCSSColors();

function createInputName($id, $field) {
			return "cid[{$id}][$field]";
}


		function parsePrintTextArea(& $line, & $columnInfo, $field, $colors) {
			//global $line, $field, $columnInfo;
			$formname = createInputName($line->id, $field);
			$largeFieldSize = 50;
			$rows = floor(@ $columnInfo[$field]->Size / $largeFieldSize) + 1;
			$rows = ($rows > 4) ? 4 : $rows;
			$class = $line->getInvalidInput($field) ? 'error' : '';
			$style = $columnInfo[$field]->Required ? ' background: '.$colors->required .' ;': '';
			$value = @ htmlspecialchars($line->$field);
?>
<textarea
	name="<?php echo  $formname ?>"
    class="<?php echo  $class ?>"
    style="<?php echo $style ?>"    	
	size="<?php echo  $columnInfo[$field]->Size?>"
	cols="<?php echo  $largeFieldSize ?>" 
	rows="<?php echo  $rows ?>" 
	style="text-align: left"><?php echo @ $value; ?></textarea>
<?php


		} // end parsePrintTextArea
		
		function parsePrintDropDownList(& $line, & $list, $columnInfo, $field, $colors) {
			//global $line, $field;

			$formname = createInputName($line->id, $field);
			$options = array ();
			$class = $line->getInvalidInput($field) ? 'error' : '';
			$style = $columnInfo[$field]->Required ? ' background: '.$colors->required .' ;': '';
			foreach ($list as $k => $v) {
				$options[] = mosHTML :: makeOption($k, $v);
			}
			echo mosHTML :: selectList($options, $formname, ' class="'.$class.'" style="'.$style.'"size="1" onchange="showFields(this);"', 'value', 'text', $line-> $field);

		} // end parsePrintDropDownList()
		
		function parsePrintTextBox(& $line, & $columnInfo, $field, &$colors) {
			//global $colors;
			//global $line, $field, $columnInfo;
			$formname = createInputName($line->id, $field);
			// Default: display a normal input box
			$value = @ htmlspecialchars($line-> $field);
			$class = $line->getInvalidInput($field) ? 'error' : '';
			$style = $columnInfo[$field]->Required ? 'background: '.$colors->required .' ;': '';
			
?>
<input type="text" 
       name="<?php echo  $formname ?>"
       class="<?php echo  $class ?>"
       style="<?php echo $style ?>"
       size="<?php echo  @ $columnInfo[$field]->Size?>" 
       maxlength="<?php echo  @ $columnInfo[$field]->Size?>" 
       value="<?php echo $value; ?>" 
       class='inputbox' >
<?php


		} // end parsePrintTextBox()
		
?>
  <div>
<?php
		
		if ( $this->hasErrors() ) 
			echo $this->getLastErrorMsgHTML().'<br/>';
		if ( $obj->hasErrors() )
			echo $obj->getLastErrorMsgHTML();	

?>
  </div>

  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr><td width="100%"><span class="sectionname"><?php echo   _LANG_PARSE . ' ' .  _LANG_QUERY ?></span></td></tr>
  </table>

  <form action="index2.php" method="post" name="adminForm" id="adminForm">
	<table summary="wrapper for params" width="80%">
		<tr><td>  
<?php

		$tabs = NULL;
		$field = NULL;

		// create the tabs object if this version of Joomla supports it
		if (class_exists('mosTabs')) {
			$tabs = new mosTabs(0);
			$tabs->startPane('Info');
		}

		if (@ $tabs)
			$tabs->startTab(_LANG_YOUR_SQL, 'SQL');
?>
  <table width="75%">
    <tr><td><?php echo  _LANG_YOUR_SQL ?> </td></tr>
    <tr><td><pre><?php echo htmlspecialchars($obj->sql) ?> </pre></td></tr>
  </table>
<?php


		if (@ $tabs)
			$tabs->endTab();

		// For each line of the display, create a tab and the necessary fields
		foreach ($display as $line) {

			$variableName = $obj->displayName($line->name);
			//$input = array_key_exists($colname, $_REQUEST) ? $input = $_REQUEST[$colname] : NULL;
			if (@ $tabs)
				$tabs->startTab($variableName, $variableName);
?>
<table cellpadding="4" cellspacing="1" border="1" width="80%" class="adminform">
      <tr class="menubackgr" >
        <td width="10%">
           <?php echo $line->type; ?>
        </td>
        <td><?php echo _LANG_REPRESENTED_BY . htmlspecialchars($line->_origregex); ?> 
            <?php echo @ $line->_instatement ? _LANG_IN_STATEMENT . $line->_instatement : '&nbsp;'; ?> 
          <input type="hidden" name="<?php echo createInputName($line->id, 'name'); ?>" value="<?php echo  $line->name ?>">
          <input type="hidden" name="<?php echo createInputName($line->id, 'type'); ?>" value="<?php echo  $line->type ?>">
          <input type="hidden" name="<?php echo createInputName($line->id, 'id'); ?>" value="<?php echo  $line->id ?>">
        </td>
      </tr>
      <tr><?php $field = 'display_name'; ?>
        <td><?php echo $obj->displayName($field); ?></td>
      	<td><?php parsePrintTextBox($line,$columnInfo,$field,$colors); ?></td>
      </tr>
      <tr><?php $field = 'type'; ?>
      	<td><?php echo $obj->displayName($field); ?></td>
      	<td><?php parsePrintDropDownList($line,$selectBoxes['type'],$columnInfo,$field,$colors); ?></td>
      </tr>
    </table>
   
<?php

			if (@ $tabs)
				$tabs->endTab();
		} // end foreach ( $line )
		if (@ $tabs)
			$tabs->endPane();
?>
	</td></tr>
	</table>

    <input type="hidden" name="qid" value="<?php echo  $obj->id ?>"> 
    <input type="hidden" name="option" value="<?php echo  $option ?>"> 
    <input type="hidden" name="task" value="">
    <input type="hidden" name="act" value="<?php echo  $act ?>">

  </form>
