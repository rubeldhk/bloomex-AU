<?php
$list = & $this->getList();
$name = $this->name;
$multiarray = $this->supportsMultipleSelection() ? '[]' : '';
$multitext = $this->supportsMultipleSelection() ? 'multiple="multiple"' : '';
$input = $this->getPreviousInput();
$size = $this->size ? $this->size : $this->getConfigValue('DEFAULT_SELECT_SIZE');
if ( $size > count($list) ) $size = count($list) +1;

// Setup an onchange select box.  Current this feature is limited to Query Result variables
$onChange = '';
if ( $this->onChangeIsEnabled() ) {
	$functionName = 'setOptionsFor'.ucwords($this->getOnChangeTargetVariable() );
	$onChange = 'onchange="'.$functionName.'(this[this.selectedIndex].value);"';
}

?>
<select <?php echo $multitext; echo $onChange ; ?> 
        id="<?php echo $this->name ?>"
		name="<?php echo $this->name, $multiarray; ?>" 
		class="<?php echo $this->getCSSClass() ?>" 
		size="<?php echo $size ?>"
>
	<option value=""><?php echo _LANG_TEMPLATE_ENTER_OPTION; ?></option>
<?php
// Iterate through the list of options and print the html for each
foreach ( $list as $v ) {
	if (is_array($input)) {
		// Use previous input
		$selected = in_array($v->id, $input) ? ' selected ' : '';
	} else {
		// Use defaults
		$selected = $v->default ? ' selected="selected" ' : '';
	}
?>
	<option value="<?php echo $v->id ?>" <?php echo $selected ?> ><?php echo $v->label ?></option>
<?php } ?>
</select>