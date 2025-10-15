
<br/>
Debugging Information<br/>
Request Data<br/>
<div style="overflow: auto; text-align: left; height: 200px; width: 80%;">
<?php
foreach ($_REQUEST as $k => $v) {
	echo "<div>The value of '$k' is '$v'</div>";
}
?>
	</pre>
</div>

<br/>

<?php if ( isset($obj) && is_object($obj) ) { ?>
Current Object<br/>
<div style="overflow: auto; text-align: left; height: 400px; width: 80%;">
	<pre>
<?php 

// Don't print the parent object
if (isset($obj->_parent) ) {
	$obj->_parent = ( isset($obj->_parent->name ) ) ? $obj->_parent->name : 'Unnamed Parent Object';
}

// Dump the contents of the object
ob_start();
print_r($obj);
$result = ob_get_contents();
ob_end_clean();
$captured = explode("\n", $result);

// Format the dump with colors to improve readability
foreach ($captured as $line) {
	$line = htmlspecialchars($line, ENT_QUOTES);
	$line = str_replace('[', '[<font color="red">', $line);
	$line = str_replace(']', '</font>]', $line);
	$line = str_replace("\r", '', $line);
	$line = str_replace('Array', '<font color="blue">Array</font>', $line);
	$line = str_replace('=>', '<font color="#556F55">=></font>', $line);
	echo $line.'<br/>';
}

?>
	</pre>
</div>

<?php } // end if statement ?>