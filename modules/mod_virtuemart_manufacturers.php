<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* Manufacturer Module
*
* NOTE: THIS MODULE REQUIRES THE VIRTUEMART COMPONENT!
/*
* @version $Id: mod_virtuemart_manufacturers.php,v 1.2.2.1 2006/03/14 18:42:25 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004-2005 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

global $mosConfig_absolute_path, $sess;

$text_before = $params->get( 'text_before', '');
$show_dropdown = $params->get( 'show_dropdown', 1);
$show_linklist = $params->get( 'show_linklist', 1);
$auto = $params->get( 'auto', 0);

$category_id = mosGetParam( $_REQUEST, 'category_id', '' );

// the configuration file for PHPShop
require_once( $mosConfig_absolute_path."/components/com_virtuemart/virtuemart_parser.php");
$sess = new ps_session;

$query  = "SELECT distinct a.manufacturer_id,a.mf_name FROM #__{vm}_manufacturer AS a ";
if ($auto == 1 && !empty( $category_id ) ) {
    $query .= ", #__{vm}_product_category_xref AS d, "
    . " #__{vm}_product AS b, "
    . " #__{vm}_product_mf_xref AS c "
    . " WHERE d.category_id='$category_id'"
    . " AND d.product_id = b.product_id "
    . " AND b.product_id = c.product_id AND c.manufacturer_id = a.manufacturer_id ";
}
$query .= "ORDER BY mf_name ASC";
$db = new ps_DB;
$db->query( $query );

$res = $db->record;
    
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

<table cellpadding="1" cellspacing="1" border="0" width="80%">

<?php if( $show_linklist == 1 ) { ?>
  <!--BEGIN manufacturer DropDown List --> 
  <tr> 
    <td colspan="2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td><?php echo $text_before ?></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <?php foreach( $res as $manufacturer) { ?>
            <tr>
                <td><a href="<?php echo $sess->url( URL."index.php?option=com_virtuemart&page=shop.browse&manufacturer_id=". $manufacturer->manufacturer_id ) ?>">
                    <?php echo $manufacturer->mf_name; ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
      </table>
    </td>
  </tr>
<?php 
}
if( $show_dropdown == 1 ) { ?>
  <tr> 
    <td colspan="2"> 
        <input type="hidden" name="option" value="com_virtuemart" />
        <input type="hidden" name="page" value="shop.browse" />
        <br/>
        <select class="inputbox" name="manufacturer_id">
            <option value=""><?php echo _CMN_SELECT ?></option>
        <?php  
                        foreach ($res as $manufacturer) { 
                                $selected = '';
                                if( @$_REQUEST['manufacturer_id'] == $manufacturer->manufacturer_id ) {
                                        $selected = 'selected="selected"';      
                                }
                                echo "<option value=\"".$manufacturer->manufacturer_id ."\" $selected>". $manufacturer->mf_name ."</option>\n";
          
                        } 
        ?>
        </select>
    </td>
  </tr>
  <tr>
      <td>
          <input class="button" type="submit" name="manufacturerSearch" value="<?php echo _SEARCH_TITLE ?>" />
      </td>
  </tr>
<?php 
} 
?>
<!-- End Manufacturer Module --> 
</table>
</form>