<?php

global $dbq, $task;
/*
$class = $dbq->getConfigValue('CSS_CLASS').'ReturnLink';
<div id="<?php echo $class ?>"><a href="#" onclick="javascript: window.history.go(-1); return false;"><?php echo _LANG_TEMPLATE_BACK; ?></a></div>
*/

if ( ! $this->windowIsIndex2()) {
	global $task;

	$url = NULL;
	$previousQuery = mosGetParam($_GET, 'previousQuery');

	// Determine where we link to
	if ( $previousQuery ) {
		// Link back to the previous query
		$urlInfo = array ('task' => 'ExecuteQuery', 'qid' => $previousQuery);
	} elseif ( $dbq->usesInputForms()) {
		// Link back to the form
		$urlInfo = array ('task' => 'PrepareQuery', 'qid' => $dbq->id );
	} else {
		$urlInfo = array ('task' => 'SelectQuery', 'qid' => $dbq->id );
	}
	
	// Print a return url if we have a url
	$url = $dbq->dbq_url($urlInfo);
	if ( $url ) {
?>
	<div class="back_button">
		<a href="<?php echo $url ?>"><?php echo _LANG_TEMPLATE_RETURN ?></a>
	</div>
<?php
	}
}
?>