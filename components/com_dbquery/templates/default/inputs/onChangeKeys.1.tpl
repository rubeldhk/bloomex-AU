<?php

$functionName = 'setOptionsFor'.ucwords($this->name);

?>
<script language="javascript" type="text/javascript">

	function <?php echo $functionName ?> (field) {
		var options = new Array();
				

<?php 

// Create a non-option option
if ( ! $this->isRequired() ) {
?>
	options[''] = new Option(<?php echo _LANG_TEMPLATE_ENTER_OPTION ?>, '');
<?php
}

foreach ( $this->getList() as $item) { 

?>
	
		option = new Option('<?php echo $item->label ?>','<?php echo $item->id ?>');

		var key = '<?php echo $item->key ?>';
	
		if (options[key] == null) {
			options[key] = new Array();
		}

		options[key]['<?php echo $item->id ?>'] = option;

<?php } ?>

		
	
		input = document.prepareQuery.<?php echo $this->name?>;
		
		if ( (field != '') && (options[field]) ) {
			input.options.length = 0;

			for ( var i in options[field]) {
				input[input.options.length] = options[field][i];
			} 
		} else {
			for ( var x in options) {
				for ( var y in options[x]) {
					input[input.options.length] = option = options[x][y];
				}
			}
		}
	}
</script>
