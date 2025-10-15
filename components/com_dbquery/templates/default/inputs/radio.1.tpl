<div class="DBQInputBlock">
<span class="<?php echo $this->getCSSClass() ?>" >
<?php
$list = & $this->getList();
$name = $this->name;
$multiarray = $this->supportsMultipleSelection() ? '[]' : '';
$input = $this->getPreviousInput();

foreach ( $list as $v ) {
	if (is_array($input)) {
		// Use previous input
		$selected = in_array($v->id, $input) ? ' checked ' : '';
	} else {
		// Use defaults
		$selected = $v->default ? ' checked ' : '';
	}
?>
<span class="oneChoise">
<input	type='radio' 
        id="<?php echo $this->name ?>"
		name='<?php echo $name, $multiarray; ?>' 
		value='<?php echo @ $v->id ?>' <?php echo $selected ?>
/>
<label for="<?php echo $name ?>" id="<?php echo $name ?>-L" class="postField"><?php echo $v->label ?></label>
</span>
<?php } ?>
</span>
</div>