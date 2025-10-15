<?php


defined('_VALID_MOS') or die(_LANG_NO_ACCESS);

$colors =&  DBQ_Settings::getCSSColors();

$option = mosGetParam($_REQUEST, 'option', null);
$task = mosGetParam($_REQUEST, 'task');
$act = mosGetParam($_REQUEST, 'act');

// Create the display for the parameters field
$obj_class = get_class($obj);
$class_vars = get_class_vars($obj_class);

// Get the data from a previous post
// Applicable if there was a problem previosly while checking or saving the data
global $mainframe;
if ( $postinput = $mainframe->getUserStateFromRequest('POSTDATA',NULL) ) {
	if ( $this->debug() ) {
		$this->debugWrite('Using Post Data for input');
	}
	echo "using post data for input";
	$invalidInput =& $mainframe->getUserStateFromRequest('invalidInput',NULL);
	// Superimpose the previous input and errors onto the relevent line
	$obj->bind($postinput);
	//mosBindArrayToObject($postinput, $obj);
	if (isset($invalidInput) )$obj->_invalid_input = $invalidInput;
}
//echo "postinput is: "; print_r($postinput); echo "<br/>";
//echo "obj is :"; print_r($obj); echo "<br/>";

$js = NULL;
$hiddenInput = array ();
$invalidInput = $obj->getInvalidInput();

// Print the warning message
// Use the input provided by the post
if (@ invalidInput && count($invalidInput)) {
	include_once ($dbq_xhtml_path.'invalidInput.html.php');
	$obj->bind($_POST);
	//mosBindArrayToObject($_POST, $obj);
}

// Load the overlib library
mosCommonHTML::loadOverlib();

?>

  <style type="text/css">
    .required { background: <?php echo  $colors->required ?> ;}
    .disabled { background: <?php echo  $colors->disabled ?> ;}
    .normal   { background: <?php echo  $colors->normal   ?> ;}
    .error    { border-color: <?php echo  $colors->error    ?> ;}
  </style>
    
  <script language="javascript" type="text/javascript">
  <!--      


function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if ( false ){
<?php


// Skip the first if() structure so that we can loop through the columns 
foreach ($columnInfo as $colname => $colinfo) {
	if (in_array($colname, $colsToSkipInDisplay)) {
		// Skip These Columns
		//} elseif ( $colinfo->Null !== 'YES' ) {
	}
	elseif ((@ $colinfo->Required == true) && $colname != 'description') {
		// 
?>
	} else if (form.<?php echo  $colname ?>.value == ""){
		alert( "<?php echo  _LANG_FIELD_REQUIRED . ' ' . $colname?>" );
<?php

	}
}
?>
	} else {
		if (pressbutton == 'apply') {
			form.hidemainmenu.value = 1;
		} else {
			form.hidemainmenu.value = 0;
		}
		submitform( pressbutton );
	}
}
      -->
  </script>
  <table title="" summary="<?php echo $displaytask ;?>" cellpadding="4" cellspacing="0" border="0" width="80%">
   <tr>
    <td width="80%">
     <span class="sectionname">
      <?php echo  $displaytask ?>
     </span>
    </td>
   </tr>
   <tr>
    <td>
     <form action="index2.php" method="post" name="adminForm" id="adminForm">
      <?php
// Optionally create tabs
$tabs = NULL;
if (class_exists('mosTabs')) {
	$tabs = new mosTabs(0);
	$tabs->startPane('Info');
}

foreach ($colsToDisplay as $header => $columns) {
	if (@ $tabs)
		$tabs->startTab($header, $header);
?>
    <table summary="" cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">

<?php

	foreach ($columns as $colname) {
		$colinfo = & $columnInfo[$colname];

		// ToDo -- generate a name based on the language file
		//$displayname = str_replace('_',' ', ucfirst($colname));
		$displayname = $obj->displayName($colname);
		

		// Determine a class for background display
		$class = $colinfo->Required ? 'required' : NULL;
		$errorMessage = '';
		$input = NULL;
		$value = @ $obj->$colname;
		
		// If the use submitted invalid input, get the previous input and error message
		//  Strip slashes out of the input if PHP magic quotes is enabled
		if (isset ($invalidInput) && array_key_exists($colname, $invalidInput) ) {
			$class = 'error';
			$errorMessage = $invalidInput[$colname]->comment;
			
			// Invalid input exists, so use the posted data instead of the object data
			if (array_key_exists($colname, $_REQUEST)) {
				$input = $_REQUEST[$colname];
				if (isset($input) && get_magic_quotes_gpc())
					$input = stripslashes($input);
				$value = $input;
			}
		}
		

		if (in_array($colname, $colsToHideInDisplay)) {
			// Don't show these inputs
			$hiddenInput[] = '<input name="'.$colname.'" type="hidden" value="'.$obj-> $colname.'"/>';

		} elseif ($colinfo->Size == 1) {
			// This is a Y or N select box
			$selected = NULL;
			if (isset ($input)) {
				$selected = $input;
			} else {
				$selected = $obj-> $colname ? 1 : 0;
			}
?>
	<tr>
	  <td width="30%" align="right">
	    <?php echo  $displayname ?> 
	  </td>
	  <td width="70%">
	    <input type="radio" name="<?php echo  $colname ?>" id="<?php echo  $colname ?>" class="<?php echo  $class ?>" value="0" <?php echo ! $selected ? 'checked="checked"' : ''; ?> ><?php echo _LANG_NO; ?>
	   	<input type="radio" name="<?php echo  $colname ?>" id="<?php echo  $colname ?>" class="<?php echo  $class ?>" value="1" <?php echo   $selected ? 'checked="checked"' : ''; ?> ><?php echo _LANG_YES; ?>
	  </td>
	</tr>
	<?php

		} elseif (array_key_exists($colname, $selectBoxes)) {
			$curvalue = $input ? $input : $obj->$colname;
			//echo "input is $input, current var is $curvalue<br/>";
			// Create the select boxes if we've prepared the info
?>
	<tr>
	  <td width="30%" align="right">
	    <?php echo  $displayname ?> 
	  </td>
	  <td width="70%">
	    <select name="<?php echo  $colname ?>" id="<?php echo  $colname ?>" class="<?php echo  $class ?>" size="1" onchange="showFields(this);">
	      <?php foreach ( $selectBoxes[$colname] as $k => $v) { ?>
	      <option value="<?php echo $k; ?>" <?php echo ( $curvalue == $k ) ? 'selected="selected"' : ''; ?> ><?php echo $v; ?></option>
	      <?php } ?>
	    </select>
	  </td>
	</tr>
<?php
		} elseif ( $colname == 'description' ) {
			$xmlFile = $obj->getXMLDescriptionParamFileName();
			$params = new mosParameters( $obj->description, $xmlFile, 'dbquery' );
			if ( is_object($params) ) {
?>
	<tr>
		<td colspan="2"><?php echo $params->render('descriptions') ?></td>
	</tr>
<?php
			}
		} elseif ($colinfo->Size > 64) {
			// Generically, make large fields textareas
			$cw = 90; // column width
			$cwmax = 10; // Max number of rows for a textbox
			// size="<?php echo  $colinfo->Size ? >"
			//$value = isset($input) ? $input :@ $obj->$colname;
			//print_r($input);
			//print_r($value);
?>
	<tr>
	  <td width="30%" align="right">
	    <?php echo $displayname ?> 
	  </td>
	  <td width="70%">
	    <textarea <?php if ( @ $colinfo->Disabled ) echo ' disabled '?>
	      class="<?php echo $class ?>" 
	      id="<?php echo $colname ?>"
	      name="<?php echo $colname ?>"
	      cols="<?php echo $cw ?>" 
	      rows="<?php echo ( $colsize = floor($colinfo->Size / $cw) ) < $cwmax ? $colsize + 1 : $cwmax?>" 
	      style="text-align: left"
	      ><?php echo htmlspecialchars($value); ?></textarea>
	    <br/> <?php echo _LANG_MAX_SIZE . $colinfo->Size  ?><br/>
		<?php echo @ $errorMessage ? "<br/>$errorMessage" : ''; ?> 
	  </td>
	</tr>
      <?php

	} else {
		// Display previous input or the current field
		//$value = isset($input) ? $input : @ $obj->$colname;
		// If there is a value, encode it for display purposes
		if ( $value ) $value = htmlspecialchars($value);
?>
	<tr>
	  <td width="30%" align="right">
	    <?php echo  $displayname ?> 
	  </td>
	  <td width="70%">

	    <input class="<?php echo  $class ?>" 
	      id="<?php echo  $colname ?>"
	      name="<?php echo  $colname ?>"
	      type="text" 
	      size="<?php echo  $colinfo->Size ?>"
	      maxlength="<?php echo  $colinfo->Size ?>" 
	      value="<?php echo $value; ?>" 
	      />
	      <?php echo @ $errorMessage ? "<br/>$errorMessage" : "&nbsp;"; ?>

	  </td>
	</tr>
    <?php

		}

		} // end foreach columnInfo
?>
    </table>
<?php


	if (@ $tabs)
		$tabs->endTab();

} // end foreach ColsToDisplay


// Display a tab for parameters

$xmlFile = $obj->getXMLParamFileName();

if ( array_key_exists('params', $class_vars) && is_readable($xmlFile)) {
	if (@ $tabs)
		$tabs->startTab(_LANG_DETAILS_PARAMS,_LANG_DETAILS_PARAMS);		

	// Print the parameter list
	$params = new mosParameters( $obj->params, $xmlFile, 'dbquery' );
	if ( is_object($params) ) 
		echo $params->render();

	if (@ $tabs)
		$tabs->endTab();	
}

if (@ $tabs)
	$tabs->endPane();

foreach (@ $hiddenInput as $input) {
	echo $input;
}
?>
      <input type="hidden" name="id" value="<?php echo  $obj->id ?>" /> 
      <input type="hidden" name="option" value="<?php echo  $option ?>" /> 
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="act" value="<?php echo  $act ?>" />
      <input type="hidden" name="hidemainmenu" value="0" />
      <input type="hidden" name="<?php echo $this->getIdentifierForObjectType() ; ?>" value="<?php echo @ $obj->id; ?>" />
     </form>
     <br />
    </td>
   </tr>
  </table>

<?php

if ($js) {
	echo '<script language="javascript" type="text/javascript">'."\n";
	echo "  var element;\n".$js;
	echo '</script>'."\n";
}
?>


