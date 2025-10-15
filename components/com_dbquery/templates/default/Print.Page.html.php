<?php

defined( '_VALID_MOS' ) or die( _LANG_TEMPLATE_NO_ACCESS );

global $dbq, $task, $qid;

$url_info = array ('task' => $task, 'qid' => $qid);
$link = $dbq->dbq_url2($url_info);
$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
$image = mosAdminMenus :: ImageCheck('printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT);
?>

<div class="contentheading">
	<div style="float:left;" id="DBQPrintPageLeft"><?php echo $dbq->getDisplayName(); ?></div>
	<div style="float: right;" id="DBQPrintPageRight">

<?php

	$onmouse = 'onmouseover="window.status=\''._CMN_PRINT.'\'; return true;" onmouseout="window.status=\'\';return true;" title="'._CMN_PRINT.'"';
if ( $dbq->windowIsIndex2() ) {
	// Print the screen
?>
      <a href="#" onclick="javascript:window.print(); return false" <?php echo $onmouse; ?>><?php echo $image; ?></a>

<?php


} else {
	// Show the screen as normal

?>
			<a href="javascript:void window.open('<?php echo $link; ?>', 'win2', '<?php echo $status; ?>');" <?php echo $onmouse; ?> > <?php echo $image;?> </a>
			
<?php

}
?>
	</div>
	<div style="clear: both;">&nbsp;</div>
</div>


<?php
global $mainframe;
if ($dbq->windowIsIndex2() && ! $mainframe->isAdmin() ) { 
?>
<div style="text-align: center;" id="DBQPrintPageCloseWindow"><br/><a href="javascript:window.close();"><?php echo _LANG_TEMPLATE_CLOSE_WINDOW; ?></a></div>
<?php } ?>