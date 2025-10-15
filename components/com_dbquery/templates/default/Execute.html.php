<?php


/***************************************
 * $Id: ExecuteQuery.Results.html.php,v 1.8.2.2 2005/08/09 00:54:39 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.8.2.2 $
 **/

defined('_VALID_MOS') or die(_LANG_TEMPLATE_NO_ACCESS);

global $dbq, $subtask;


// Look at and perform the requested subtask
switch ($subtask) {
 case 'results':
   showResults();
   break;
 case 'noresults':
   showNoResults();
   break;
 case 'noresultsexpected':
   showNoResultsExpected();
   break;
}

if ( $dbq->supportsXHTMLOutput() )
	$dbq->showPopUpFeatures();

// Show results that were found in the database
function showResults() {

	global $dbq;

	if ( $dbq->supportsXHTMLOutput() ) {
		echo '<div align="center">'.$dbq->getDescriptionResultsAbove().'</div>';
		// Load the javascript file that will be used with this template
		$dbq->loadTemplateJavaScript();

		// Display page navigation, if used by the query
		$dbq->showPageNavigation();
		// Include the template for the apropriate interface -- probably the file DisplayResults.SQL.html.php
		$dbq->showResults();
  
		// Display page navigation, if used by the query
		$dbq->showPageNavigation();
		echo '<div align="center">'.$dbq->getDescriptionResultsBelow().'</div>';
		$dbq->showReturnLink();
	} else {
		$dbq->showResults();
	}
} // end showResults()

function showNoResults() {
  global $dbq;
  echo '<div align="center">'.$dbq->getDescriptionNoResults().'</div>';
} // end showNoResults()

function showNoResultsExpected() {
  global $dbq;
  echo '<div align="center">'.$dbq->getDescriptionWithoutResults().'</div>';
} // end showNoResultsExpected()

?>