<!-- Define your HTML code here -->
<input	type="text" 
		id="<?php echo $this->name ?>"
		name="<?php echo $this->name ?>" 
		value="<?php echo $this->getPreviousInput() ?>" 
		size="<?php echo $this->getSize() ?>" 
		maxlength="<?php echo $this->getSize() ?>" 
		class="<?php echo $this->getCSSClass() ?>" 
/>