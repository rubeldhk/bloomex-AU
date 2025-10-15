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
global $database;


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
?>
<br/>
<style type="text/css" media="print">
@media print{
	@page {
		size: portrait;
		margin: 0.5in;
	}
}

body{
	
}

div.page {	
	height:420px;
	font-size:22px;	
	text-align:center;
	display:block;
	width:60%;
}

.page-size {
	-webkit-transform: rotate(90deg); 
	-moz-transform:rotate(90deg);
	filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=1);
	page-break-before:avoid;
	margin:0px 0px 200px 0px;
	width:500px;
	
} 
</style>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" valign="top" class="page-size">    
	<tr>
    	<td valign="top" align="center">    		
			<div class="page">
				<b>
					<?php
						if( $db->f("customer_note") ) {
							echo str_replace("\\", "",nl2br( htmlspecialchars_decode($db->f("customer_note"))));
						}else{
							echo " ";
						}
					?>
				</b>
			</div>
		</td>
	</tr>
</table>
<?php
	}
?>
   