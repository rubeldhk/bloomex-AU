<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: order.order_printdetails.php,v 1.7 2005/05/10 18:45:04 soeren_nb Exp $
* @package mambo-phpShop
* @subpackage HTML
* Contains code from PHPShop(tm):
* 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
*	Community: www.phpshop.org, forums.phpshop.org
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* mambo-phpShop is Free Software.
* mambo-phpShop comes with absolute no warranty.
*
* www.mambo-phpshop.net
*/
mm_showMyFileName( __FILE__ );
require_once(CLASSPATH.'ps_checkout.php');
require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;

$order_id = mosgetparam( $_REQUEST, 'order_id', null);
$dbc = new ps_DB; 
if (!is_numeric($order_id))
    die ('Please provide a valid Order ID!');

$q = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id'";
$db->query($q);

if ($db->next_record()) {
 // Get ship_to information
    $mbbt = new ps_DB;
    $q  = "SELECT * FROM #__{vm}_order_user_info,#__{vm}_state,#__{vm}_country   WHERE #__{vm}_state.state_2_code=#__{vm}_order_user_info.state AND #__{vm}_order_user_info.address_type = 'ST' AND #__{vm}_country.country_3_code=#__{vm}_order_user_info.country AND #__{vm}_order_user_info.order_id='" . $order_id . "' ORDER BY title ASC"; 
    $mbbt->query($q);
    $mbbt->next_record(); 
    $database->setQuery( $q );

//    echo $q;
?>
	<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
	<div align="center">
		
		<img src="<?php echo $mosConfig_live_site; ?>/barcode/html/image.php?code=code128&o=1&dpi=72&t=30&r=1&rot=0&text=<?php echo sprintf("%08d", trim($db->f("order_id")));?>&f1=Arial.ttf&f2=9&a1=&a2=NULL&a3=" />
    </div>
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<?php
	}
?>
   