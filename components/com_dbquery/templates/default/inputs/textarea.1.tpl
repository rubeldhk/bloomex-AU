<?php
$cw = $this->getTextAreaWidth(); // column width
$cwmax = $this->getTextAreaHeight(); // Max number of rows for a textbox
$rows = ($colsize = floor($this->getSize() / $cw)) < $cwmax ? $colsize : $cwmax;

?>
<textarea class="<?php echo $this->getCSSClass() ?>" 
	id="<?php echo $this->name ?>"
	name="<?php echo $this->name; ?>" 
	cols="<?php echo $cw; ?>" 
	rows="<?php echo $rows; ?>" 
	style="text-align: left"><?php echo $this->getPreviousInput() ?></textarea>