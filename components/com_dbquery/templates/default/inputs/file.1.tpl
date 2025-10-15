<?php
//$maxsize = $this->getConfigValue('MAXIMUM_FILE_SIZE');
//if ( ! $maxsize ) $maxsize = 1000000;

$accept = $this->getConfigValue('FILE_ACCEPT');
if ($accept) $accept = "accept=\"$accept\"";
?>
<input 	class="<?php echo $this->getCSSClass() ?>"
		id="<?php echo $this->name ?>"
		name="<?php echo $this->getName() ?>" 
		size="<?php echo $this->getSize() ?>"
		value="<?php echo $this->getPreviousInput() ?>"
		type="file" <?php echo $accept ?> />