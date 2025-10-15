<?php
/**
* YOOaccordion Joomla! Module
*
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

if(function_exists('botMosLoadPosition'))	{
 	$plgParams = new mosParameters('');	
	botMosLoadPosition(true, $item, $plgParams);
}
?>
<div class="article">
	<?php echo $item->text; ?>
</div>