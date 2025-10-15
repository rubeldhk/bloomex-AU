<?php

/***************************************
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 **/

defined( '_VALID_MOS' ) or die( _LANG_TEMPLATE_NO_ACCESS );

global $dbq;

$queries =& $this->queries;

/*
// Uncomment if you are having problems listing a list of queries
echo 'showing the query array now<br/><pre>';
print_r(array_keys($queries));
echo '</pre>';
*/

// Show the print page if enabled in the DBQ settings

if ( ! count($queries ) ) {
		echo _LANG_TEMPLATE_NO_QUERIES_AVAILABLE;
} else {
?>

<table align="center">
  <tr>
    <th style="width: ;"><?php echo _LANG_TEMPLATE_NAME ?></th>
    <th><?php echo _LANG_TEMPLATE_DESCRIPTION ?></th>
  </tr>
<?php 
  $i = 1;
  $categoryID = 0;
  $showCategoryDescriptions = $dbq->getConfigValue('SHOW_CATEGORY_DESCRIPTIONS');
  foreach($queries as $query) { 
  	
  	// Optionally show category descriptions
	if ( $showCategoryDescriptions && ($categoryID != $query->catid) ) {
		$categoryDescription = $query->category_description;
		echo '<tr><td colspan="2">'.$categoryDescription.'</td></tr>';
		$categoryID = $query->catid;
	}

	// Show normal query information
    $class = 'sectiontableentry'.$i++%2 +1;
    $link = $dbq->makeLinkToNextTask($query);
    $description = $query->getDescriptionSelectQuery();
    $displayName = $query->getDisplayName();
?>
  <tr class="<?php echo $class ?>">
    <td><a href="<?php echo $link ?>"><?php echo $displayName ?></a></td>
    <td><?php echo $description ?></td>
  </tr>
<?php  }  ?>
</table>
<?php } ?>
