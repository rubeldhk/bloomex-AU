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

$dbq =& $this;

$pageNav =& $dbq->_pageNav;

// Use this to get the results directly
//$results = $dbq->queryGetResults();

// Load the Over Lib library, which DBQ Professional can use
mosCommonHTML::loadOverlib();

// Intialize the display, which will calculate rows and columns -- This is required !
$this->initializeDisplay();
$class = $dbq->getConfigValue('CSS_CLASS');
$CSSRow = " class=\"$class\" onMouseOver = \"this.className='{$class}MouseOver';\" onMouseOut = \"this.className='{$class}MouseOut';\" ";
$CSSHeader = $this->resultsAreRotated() ? " class=\"{$class}VerticalHeader\" ": " class=\"{$class}HorizontalHeader\" ";
$CSSTable = $this->getConfigValue('RESULT_TABLE_SORTABLE') ? 'sortable' : '';
$CSSid = $class.'Table';

// Everyone loves tables
echo '<table class="'.$CSSTable.'" id="'.$CSSid.'"align="center">';

// Determine how we should traverse the result matrix
if ( $this->resultsAreRotated() ) {
	// Print a row header, followed by all values for this field
	while ( $this->nextColumn() ) {
		$this->nextHeader();
		echo "<tr><th $CSSHeader >".$this->header()."</th>";
		while ( $this->nextRow() ) 
			echo "<td $CSSRow >".$this->field()."</td>";
		echo '<tr>';
	}
} else {
	// Print all the headers, then the rows
	echo '<tr>';
	while ( $this->nextHeader() )
		echo "<th $CSSHeader >".$this->header()."</th>";
	echo '</tr>';
	// Print the results
	while ( $this->nextRow() ) {
		echo '<tr>';
		while ( $this->nextColumn() )
			echo "<td $CSSRow >".$this->field()."</td>";
		echo '</tr>';
	}
} 

echo '  </table>'."\n";

?>
