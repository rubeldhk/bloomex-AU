<?php
define('_VALID_MOS', 1);
require( '../../../globals.php' );
require_once( '../../../configuration.php' );
require_once( '../../../includes/joomla.php' );

		switch ($task) {		
			
			default:
				getTableEmail();
				break;
		}

function getTableEmail(){
    var_dump($_FILES['filename']);
    if (isset($_FILES['filename'])) {
	
	require_once "simplexlsx.class.php";
	
	$xlsx = new SimpleXLSX( $_FILES['filename']['tmp_name'] );
	
	echo '<h1>Parsing Result</h1>';
	echo '<table border="1" cellpadding="3" style="border-collapse: collapse">';
	
	list($cols,) = $xlsx->dimension();
	
	foreach( $xlsx->rows() as $k => $r) {
//		if ($k == 0) continue; // skip first row
		echo '<tr>';
		for( $i = 0; $i < $cols; $i++)
			echo '<td>'.( (isset($r[$i])) ? $r[$i] : '&nbsp;' ).'</td>';
		echo '</tr>';
	}
	echo '</table>';
}
    
    
}



?>
