<?php
/***************************************
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 **/

defined('_VALID_MOS') or die(_LANG_TEMPLATE_NO_ACCESS);

$this->initializeDisplay();

// Clear the current output
@ob_end_clean();
header('Content-Type: text/xml');

// Determine the encoding
$encoding = '';
if ( preg_match('/=(.*)$/', _ISO, $tmp) ) 
	$encoding = 'encoding="'.$tmp[1].'"';

echo '<?xml version="1.0" '.$encoding." ?>\n";
echo '<query>';


// Generate the header information
echo "<fields>\n";
while ( $this->nextHeader() ) {
	$field = $this->getHeaderName();
	echo '<field name="'.$field.'">'.htmlspecialchars($this->header())."</field>\n";
 }
echo '</fields>';

// Generate the data
echo "<results>\n";
while ( $this->nextRow() ) {
	echo "<row>\n";
	while ( $this->nextColumn() ) {
		$field = $this->getFieldName();
		echo "<$field>".htmlspecialchars($this->field())."</$field>\n";
	}
	echo "</row>";
}
echo "</results>\n";

echo "</query>\n";

// No more processing is required
exit();

?>