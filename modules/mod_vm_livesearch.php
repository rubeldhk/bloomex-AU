<?php
/**
* VirtueMart LiveSearch Module
* NOTE: This module require the VirtueMart component
*
* @version $Id: mod_vm_livesearch.php,v 1.0 2006/10/27 20:41:00 Antoine Bernier Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2006 Bernier Antoine
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
* http://forum.joomlafacile.com/showpost.php?p=91768&postcount=1
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

global $VM_LANG, $mm_action_url, $sess;

//grab module parameters:
$limit = $params->get('limit', 10); //par défaut, on affiche 10 résultats
$mini_caract = $params->get('mini_caract', 3);
$fx_duration = $params->get('fx_duration', 400);
$adv_search = $params->get('adv_search', 1);

//the folder containing aditionnal scripts:
$folder="modules/mod_vm_livesearch/";

?>
<!--the form--> 
<form id="livesearch_form" action="<?php $sess->purl( $mm_action_url."index.php?page=shop.browse" ) ?>" method="post">

	<!--we include javascript scripts-->
  <script type="text/javascript" src="<?php echo $folder; ?>prototype.js"></script>
  <script type="text/javascript" src="<?php echo $folder; ?>moo.fx.js"></script>
  <script type="text/javascript" src="<?php echo $folder; ?>livesearch.js"></script>
  
	<label for="keyword"><b><?php echo $VM_LANG->_PHPSHOP_SEARCH_LBL ?></b></label> 
	
	<!--the keyword field-->
	<input name="keyword" type="text" size="18" title="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE ?>" class="inputbox" id="keyword" />
		
	<!--the limit of results-->
	<input name="limit" type="hidden" value="<?php echo $limit; ?>">
	
	<!--the eggtimer-->
	<img src="<?php echo $folder; ?>wait.gif" id="wait" style="display:none" />
	
	<input class="button" type="submit"  name="Search" value="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE ?>" />

</form>
<!--END of form-->

<!--a link to 'advanced search'-->
<?php if($adv_search) { ?>
	<a href="<?php echo $sess->url($mm_action_url."index.php?option=com_virtuemart&page=shop.search") ?>">
		<?php echo $VM_LANG->_PHPSHOP_ADVANCED_SEARCH ?>
	</a>
<?php } ?>

<!--the area where results will be displayed-->
<div id="results_area"></div>


<!--initializing variables for js-->
<script type="text/javascript">
	//form id:
	var form = 'livesearch_form';

	//keyword id:
	var from = 'keyword';
	
	//results area id:
	var to = 'results_area';
	
	//the path to the php file which launch database requests:
	var by = '<?php echo $folder."livesearch.php"; ?>';
	
	//minimum characters(module's parameter):
	var mini = <?php echo $mini_caract; ?>;
	
	//duration of effect(module's parameter):
	var tps = <?php echo $fx_duration; ?>;
</script>
