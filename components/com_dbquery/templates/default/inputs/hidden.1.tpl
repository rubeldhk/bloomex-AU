<?php

$value = $this->getPreviousInput();
// Currently, only a single value can be passed

$name = $this->name;
?>
<input	type="hidden" 
		name="<?php echo $name ?>" 
		value="<?php echo $value ?>" 
		size="<?php echo $this->getSize() ?>" 
		maxlength="<?php echo $this->getSize() ?>" 
		class="inputbox" 
/>