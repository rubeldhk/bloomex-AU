<?php
$cw = $this->getTextAreaWidth(); // column width
$cwmax = $this->getTextAreaHeight(); // Max number of rows for a textbox
$rows = ($colsize = floor($this->getSize() / $cw)) < $cwmax ? $colsize : $cwmax;
$rows += 4;

global $mainframe;
$mainframe->set( 'loadEditor', true );

// Initialize the editor if the user has not logged in
global $my;
if (!$my->id)
	initEditor();

?>
<textarea  mce_editable="true" style="width: 100%;  display: none;"
	id="<?php echo $this->name ?>"
	name="<?php echo $this->name ?>" 
	cols="<?php echo $cw ?>" 
	rows="<?php echo $rows ?>" 
	class="<?php echo $this->getCSSClass() ?>" 
><?php echo $this->getPreviousInput() ?></textarea>