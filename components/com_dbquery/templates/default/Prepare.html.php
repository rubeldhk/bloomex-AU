<?php

defined('_VALID_MOS') or die(_LANG_TEMPLATE_NO_ACCESS);

global $dbq, $task;

// If the previous submission didn't pass variable criteria, the user will be pushed back to this form
$firsthit =  ( $task == 'PrepareQuery' ) ? true : false;
$task = 'PrepareQuery';

// Load the overlib library
mosCommonHTML::loadOverlib();

// Retrieve our variables
$variables = $this->getVariablesUsedInForm();
$hiddenInput = array ();

// Load the javascript file that will be used with this template
$dbq->loadTemplateJavaScript();

// Load the theme to be used with this query
$dbq->loadTemplateTheme();

// Make the link to the next query
$link = $dbq->makeLinkToNextTask();

?>
<div align="center" style="float:center;"><br/><?php echo $dbq->getDescriptionFormAbove() ?></div>
	<div id="DBQForm">
	<form enctype="multipart/form-data" action="<?php echo $link ?>" method="post" name="prepareQuery" id="prepareQuery">
	<div class="" id="alertMessagePlaceHolder"></div>
	<?php

	// Cycle through all the variables to display
	foreach ($variables as $variable) {
	
	// Skip variables that do not need an input form
	if ( $variable->isHidden() ) {
		// Don't show hidden input - display at the end
		$hiddenInput[] = $variable;
		continue;
	} 

	// Get the basic information about the variable
	$description = $variable->getDescription();
	$displayName = $variable->getDisplayName();
	$name = $variable->getName();
	$errorClass = 'errMsgHidden';

	// Check if should display a comment or error message about the input
	$messages = array();
	$messages[] = $variable->getCommentAboutInput();

	if ( !$firsthit && $variable->requiresUserAttention() && ( $this->displayRegexOnError() || $this->displayRegexNormally() ) ) {
		$messages = array_merge($messages, $variable->getInvalidInput());
		$errorClass = 'errMsg';

	} elseif ( $this->displayRegexNormally()) {
		// Always show the comment - don't hide the comment class
		$errorClass = '';
	}
	
	// Star any required fields
	if ( $variable->isRequiredWithinContext() )
		$displayName .= '*';
	
	$message = implode('<br>', $messages);

	?>
	<span class="oneField">
	<div class="field-hint-inactive" id="<?php echo $name ?>-H"><?php echo $description ?></div>
	<label for="<?php echo $name ?>" class="label preField"><?php echo $displayName ?></label>
	<?php $variable->printInputField() ?>

	<div class=""><span class="<?php echo $errorClass ?>" id="<?php echo $name ?>-E"><?php echo $message ?></span></div>
	<br/>
	</span>
	<?php	
} // end foreach($variables)
?>


<div align="center" style="text-align=center;"><?php echo _LANG_TEMPLATE_STAR_INDICATES; ?></div>

<?php
																							 // Don't forget to print the hidden variables
foreach ($hiddenInput as $variable) 
$variable->printInputField();
?>
<div align="center">
  	<input type="submit" value="<?php echo _LANG_TEMPLATE_SUBMIT?>" class="button primaryAction" id="submit-prepareQuery" name="DBQSubmit" />
  	<input type="reset" value="<?php echo _LANG_TEMPLATE_CLEAR ?>" onclick="clearForm('prepareQuery'); return false;" class="button" />
	</div>
	</form>
	<div align="center" style="float:center;"><br/><?php echo $dbq->getDescriptionFormBelow() ?></div>
	</div>

