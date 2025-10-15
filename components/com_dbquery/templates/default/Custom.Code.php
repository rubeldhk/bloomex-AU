<?php


defined( '_VALID_MOS' ) or die( _LANG_TEMPLATE_NO_ACCESS );

global $dbq, $category, $task, $qid, $qname;
//echo $dbq->getUserConfig('TEST');

//echo "Fields are category=$category, task=$task, qid=$qid, qname=$qname<br/>";


switch ($task) {
	default :
	case 'SelectQuery' :
		break;
	case 'PrepareQuery' :
		break;
	case 'ExecuteQuery' :
		break;
}
?>
