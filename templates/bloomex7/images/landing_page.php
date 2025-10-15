<?php
// So we share
require_once( 'configuration.php' );

// Lets connect
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($mosConfig_db, $link)) {
    echo 'Could not select database';
    exit;
}

// Grab the request
$the_locale = mysql_real_escape_string($_GET['city']);

// Grab the language
$lang = mysql_real_escape_string($_GET['lang']);

// Build query
$sql = "SELECT * FROM tbl_landing_pages WHERE (url='" . $the_locale . "' OR url='" . str_replace( "/", "", $the_locale) . "') AND lang=1";


$result = mysql_query($sql, $link);

if(mysql_num_rows($result)<1){
	mysql_free_result($result);
	mysql_close($link);
	header('location: https://bloomex.com.au');
	exit();
}

while ($row = mysql_fetch_assoc($result)) {
    // Varibles
	$city = $row['city'];
	$prov = $row['province'];
	$tele = $row['telephone'];
	$activate_loc = (int)$row['enable_location'];
	$location_address = $row['location_address'];
	$location_country = $row['location_country'];
	$location_postcode = $row['location_postcode'];
	$location_telephone = $row['location_telephone'];
	$category_id = $row['category_id'];
}

mysql_free_result($result);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="google-site-verification" content="APtWhYDJvxgO0ehktEjH900A5gW4WqFDVXG8LVE16zA" />
<title><?php echo $city; ?> Florist - <?php echo $city; ?> Flowers | <?php echo $city; ?> Flower Delivery | Send Flowers to <?php echo $city; ?> Australia</title>

<meta name="description" content="50% off <?php echo $city; ?> Flowers from Bloomex Australia - Order Flowers online from your dedicated <?php echo $city; ?> Florist. Send Flowers to <?php echo $city; ?> - Same Day Flower Delivery" />

<meta name="keywords" content="<?php echo $city; ?> florist, <?php echo $city; ?> flowers, <?php echo $city; ?> flower delivery, send flowers to <?php echo $city; ?>, <?php echo $city; ?> flower shop, <?php echo $city; ?> fresh flowers, <?php echo $city; ?> Mothers Day Flowers, <?php echo $city; ?> valentines flowers, <?php echo $city; ?> valentines day flowers, <?php echo $city; ?> sympathy flowers, <?php echo $city; ?> friendship flowers, <?php echo $city; ?> thank you flowers" />

<script type="text/javascript" src="https://bloomex.com.au/modules/luckyphoto/LuckyPhoto.js"></script>
<meta name="Generator" content="Joomla! - Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved." />
<meta name="robots" content="index, follow" />
<base href="https://bloomex.com.au/" />
	<link rel="shortcut icon" href="https://bloomex.com.au/images/bloomex.ico" />

	<link rel="stylesheet" type="text/css" href="https://bloomex.com.au/templates/bloomex7/css/template_css.css" />
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="https://bloomex.com.au/templates/bloomex7/css/ie8.css" />
<![endif]-->
<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="https://bloomex.com.au/templates/bloomex7/css/ie6.css" />
<![endif]-->
<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="https://bloomex.com.au/templates/bloomex7/css/ie8.css" />
<![endif]-->
<script type="text/javascript">
	sImgLoading	= "https://bloomex.com.au/administrator/components/com_virtuemart/html/jquery_ajax.gif";
	sScriptPath	= "https://bloomex.com.au/templates/bloomex7/js/";
</script>
<script type="text/javascript" src="https://bloomex.com.au/templates/bloomex7/js/jquery.js"></script>
<script type="text/javascript" src="https://bloomex.com.au/templates/bloomex7/js/jquery.simplemodal.js"></script>
<script type="text/javascript" src="https://bloomex.com.au/templates/bloomex7/js/func.js"></script>
<link rel="shortcut icon" href="https://bloomex.ca/images/bloomex.ico"/>

<!--================ MOUSEFLOW ========================-->
<script type="text/javascript">document.write(unescape("%3Cscript src='" + (("https:" == document.location.protocol) ? "https" : "http") + "://b.mouseflow.com/projects/c805ef78-25f7-4da2-afd1-25f9c4c0ea7b.js' type='text/javascript'%3E%3C/script%3E"));</script>

</head>
<body>
<!-- S1 -->
<TABLE id=fullPage cellSpacing="0" cellPadding="0" width="790" align="center"  border=0><TBODY>
  <TR>
    <TD class="borderLeft" width="28" height="6"></TD>
    <TD colSpan=2></TD>
    <TD class="borderRight" width="26"></TD>
    </TR>
    <TR>
     <TD width="28" height="6" class="borderLeft"></TD>

     <TD style="padding-left: 10px; vertical-align: middle; padding-top: 5px;" colspan="2">
		<div class="delivery-intro">&nbsp;&nbsp;&nbsp;&nbsp;
			DELIVERIES OUTSIDE OF AUSTRALIA&nbsp;&nbsp;
			<a href="https://bloomexusa.com"><img src="https://bloomex.com.au/images/flag_usa.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;USA&nbsp;</a>&nbsp;|&nbsp;
			<a href="https://www.serenataflowers.com"><img src="https://bloomex.com.au/images/flag_great_britain.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;UK&nbsp;</a>&nbsp;|&nbsp;
			<a href="https://bloomex.ca"><img src="https://bloomex.com.au/images/flag_canada.png" border="0" height="14" align="absbottom" style="margin-top: 3px;">&nbsp;CAN</a>

		</div><br>
	</TD> 
     <TD width="26" class="borderRight"></TD>
  </TR>
    <TR>
    <TD class="borderLeft" width="28"></TD>
   <TD style="VERTICAL-ALIGN: middle" width="443">Easy ordering for <b><?php echo $city;?> Flowers</b> at:&nbsp;<b><?php echo $tele;?></b></TD>

    <TD align="right">
      <TABLE cellSpacing="0" cellPadding="0" width="224" border="0">
        <TBODY>
        <TR>
         <TD style="VERTICAL-ALIGN: bottom;">&nbsp;
          
          </TD>
          <TD style="VERTICAL-ALIGN: bottom;">
          <A href="https://bloomex.com.au/index.php?page=account.index&option=com_virtuemart&Itemid=1">
           	
               <IMG alt="" src="https://bloomex.com.au/templates/bloomex7/images/myAccount.gif" border="0">

              	
           </A>
          </TD>
          <TD style="VERTICAL-ALIGN: bottom;">
          <A href="https://bloomex.com.au/index.php?page=shop.cart&option=com_virtuemart&Itemid=80">
            	
                <IMG alt="" src="https://bloomex.com.au/templates/bloomex7/images/shoppingCart.gif" border="0">
               	
          </A>
         </TD>
       </TR>
      </TBODY></TABLE>

    </TD>
    <TD class="borderRight" width="26"></TD>
   </TR>
   <TR>
    <TD class="borderLeft" width="28"></TD>
  <?php
		if( $activate_loc ) {
	?>		
	<link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet" />
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	
	<?php
		define("MAPS_HOST", "maps.google.com");
		define("KEY", "AIzaSyDccPQTrPbuXO3kV-s1_ZBJJRBGWPFEweo");
		
		// Initialize delay in geocode speed
		$delay 		= 0;
		$base_url 	= "https://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;
		$address 	=  $location_address . ", " . $location_postcode . ", " . $location_country;
			
		// Iterate through the rows, geocoding each address
		$geocode_pending = true;
		
		//echo $address;
		//echo $request_url 	= $base_url . "&q=" . urlencode($address);
		
		while ($geocode_pending) {
			$request_url 	= $base_url . "&q=" . urlencode($address);
			
			$curl = curl_init($request_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($curl);
			//echo $data."<br/><br/>----------------------<br/><br/>";
	
			$xml 	= simplexml_load_string($data);
			//print_r($xml);
			
			$status = $xml->Response->Status->code;
			if (strcmp($status, "200") == 0) {
				// Successful geocode
				$geocode_pending 	= false;
				$coordinates 			= $xml->Response->Placemark->Point->coordinates;
				$coordinatesSplit 		=explode(",", $coordinates);
				
				
				// Format: Longitude, Latitude, Altitude
				$lat 	= $coordinatesSplit[1];
				$lng = $coordinatesSplit[0];
				
		?>
				<script>
					var Lat		= <?php echo $lat; ?>;
					var Lng		= <?php echo $lng; ?>;
					var Address	= "<?php echo $address; ?>";
				
					// Create an object containing LatLng, population.
					var citymap = {};
						citymap['chicago'] = {
						center: new google.maps.LatLng( Lat, Lng),
						population: 10000 // duong kinh ve hinh tron theo metter
					};
					var cityCircle;
					
					function initialize() {
						var mapOptions = {
							zoom: 10,
							zoomControl: false,
							streetViewControl: false,
							center: new google.maps.LatLng( Lat, Lng),
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						
						var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
						
						for (var city in citymap) {
						// Construct the circle for each value in citymap. We scale population by 20.
							var populationOptions = {
								strokeColor: '#000000',
								strokeOpacity: 0.8,
								strokeWeight: 1,
								fillColor: '#ffff00',
								fillOpacity: 0.20,
								map: map,
								center: citymap[city].center,
								radius: citymap[city].population
							};
							cityCircle = new google.maps.Circle(populationOptions);
							
							 var marker = new google.maps.Marker({
							      	position: new google.maps.LatLng( Lat, Lng),
							      	map: map,
							      	title: Address
							  });
						}
					}
					
					jQuery(document).ready(function() {
					 	initialize();
					});
				</script>
				
		<?php		
			} else if (strcmp($status, "620") == 0) {
				// sent geocodes too fast
				$delay += 100000;
			} else {
				// failure to geocode
				$geocode_pending = false;
				echo "Address " . $address . " failed to geocoded. ";
				echo "Received status " . $status . "
				\n";
			}
			usleep($delay);
		}
	}
	?>
    <TD colSpan=2 width="731" height="193" align="left" valign="top" scope="col" style="background-color:#ffffff;">
 		<div style="z-index:9999;position:absolute;height:192px;display:block;float:left;width:368px;background:url('<?php echo $mosConfig_live_site;?>/templates/bloomex7/images/bloomex-logo.png') top left no-repeat">
			<div style="display:block;float:left;width:100%;">
				<img height="60" src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/australian_flag.jpg" style="float:left;margin:0px 0px 0px 0px;"> 
			</div>
			<div style="display:block;text-align:left;font-size:12px;width:155px;padding:0px 0px 0px 10px;margin:70px 0px 0px 0px;float:left;line-height:150%;">
			        <b><?php echo $city; ?>, <?php echo $prov; ?></b><br>
			        <b><font color="#EE1111">Call Now!</font></b> <span style="white-space:nowrap"><?php echo $tele ?></span>
			</div>
 		</div>
		<div id="map_canvas" style="height:191px;display:block;float:right;width:397px;"></div>
	  </TD>
    <TD class="borderRight" width="26"></TD>

   </TR>
</TBODY></TABLE>
<!-----TOP MENU & SEARCH---->
<TABLE id=fullPage cellSpacing="0" cellPadding="0" width="790" align="center" border="0">
 <TBODY>

  <TR>
    <TD class="borderLeft" width="28" ></TD>
    <TD height="42" width="412"style="BACKGROUND-COLOR: rgb(216,190,232);"  >
       
<script type="text/javascript" src="https://bloomex.com.au/modules/mod_swmenupro/transmenu_Packed.js"></script>

<!--swMenuPro5.6 transmenu by https://www.swmenupro.com-->

<style type='text/css'>
<!--
.transMenu77 {
 position:absolute ; 
 overflow:hidden; 
 left:-1000px; 
 top:-1000px; 
}
.transMenu77 .content {
 position:absolute  ; 
}
.transMenu77 .items {
 border: 0px dashed #FFFFFF ; 
 position:relative ; 
 left:0px; top:0px; 
 z-index:2; 
}
.transMenu77  td
{
 padding: 5px 8px 5px 8px !important;  
 font-size: 12px !important ; 
 font-family: Arial, Helvetica, sans-serif !important ; 
 text-align: left !important ; 
 font-weight: normal !important ; 
 color: #FFFFFF !important ; 
} 
#subwrap77 
{ 
 text-align: left ; 
}
.transMenu77  .item.hover td
{ 
 color: #020028 !important ; 
}
.transMenu77 .item { 
 text-decoration: none ; 
 cursor:pointer; 
}
.transMenu77 .background {
 background-color: #674d7a !important ; 
 position:absolute ; 
 left:0px; top:0px; 
 z-index:1; 
 opacity:0.8; 
 filter:alpha(opacity=80) 
}
.transMenu77 .shadowRight { 
 position:absolute ; 
 z-index:3; 
 top:3px; width:2px; 
 opacity:0.8; 
 filter:alpha(opacity=80)
}
.transMenu77 .shadowBottom { 
 position:absolute ; 
 z-index:1; 
 left:3px; height:2px; 
 opacity:0.8; 
 filter:alpha(opacity=80)
}
.transMenu77 .item.hover {
 background-color: #E8A9FF !important ; 
}
.transMenu77 .item img { 
 margin-left:10px !important ; 
}
table.menu77 {
 top: 0px; 
 left: 3px; 
 position:relative ; 
 margin:0px !important ; 
 border: 0px dashed #FFFFFF ; 
 z-index: 1; 
}
table.menu77 a{
 margin:0px !important ; 
 padding: 10px 8px 2px 8px !important ; 
 display:block !important; 
 position:relative !important ; 
}
div.menu77 a,
div.menu77 a:visited,
div.menu77 a:link {
 height:23px; 
 font-size: 12px !important ; 
 font-family: Arial, Helvetica, sans-serif !important ; 
 text-align: left !important ; 
 font-weight: normal !important ; 
 color: #1B003C !important ; 
 text-decoration: none !important ; 
 margin-bottom:0px !important ; 
 display:block !important; 
 white-space:nowrap ; 
}
div.menu77 td {
 border-bottom: 1px solid #FFFFFF ; 
 border-top: 1px solid #FFFFFF ; 
 border-left: 1px solid #FFFFFF ; 
} 
div.menu77 td.last77 {
 border-right: 1px solid #FFFFFF ; 
} 
#trans-active77 a{
 color: #1B003C !important ; 
 background-color: #E8A9FF !important ; 
} 
#menu77 a.hover   { 
 color: #1B003C !important ; 
 background-color: #E8A9FF !important ; 
 display:block; 
}
#menu77 span {
 display:none; 
}
#menu77 a img.seq1,
.transMenu77 img.seq1,
{
 display:    inline; 
}
#menu77 a.hover img.seq2,
.transMenu77 .item.hover img.seq2 
{
 display:   inline; 
}
#menu77 a.hover img.seq1,
#menu77 a img.seq2,
.transMenu77 img.seq2,
.transMenu77 .item.hover img.seq1
{
 display:   none; 
}
#trans-active77 a img.seq1
{
 display: none;
}
#trans-active77 a img.seq2
{
 display: inline;
}

-->
</style>
<div id="wrap77" class="menu77" align="left">
<table cellspacing="0" cellpadding="0" id="menu77" class="menu77" > 
<tr> 
<td> 
<a id="menu77303" >Specials</a>
</td> 
<td> 
<a id="menu77246" href="/index.php?option=com_virtuemart&amp;page=shop.browse&amp;category_id=7&amp;Itemid=246" >Occasions</a>
</td> 
<td> 

<a id="menu77262" href="https://bloomex.com.au/index.php/Flowers/View-all-products.html" >Flowers</a>
</td> 
<td> 
<a id="menu77322" >Price</a>
</td> 
<td> 
<a id="menu77305" >My Account</a>
</td> 
<td class="last77"> 
<a id="menu77327" >Support</a>
</td> 
</tr> 
</table></div> 

<div id="subwrap77"> 
<script type="text/javascript">
<!--
if (TransMenu.isSupported()) {
var ms = new TransMenuSet(TransMenu.direction.down, 0,0, TransMenu.reference.bottomLeft);
var menu77303 = ms.addMenu(document.getElementById("menu77303"));
 menu77303.addItem("Half Price Roses", "https://bloomex.com.au/index.php/Half-Price-Roses/View-all-products.html", "0");
menu77303.addItem("Designer&#039;s Collection", "https://bloomex.com.au/index.php?page=shop.browse&category_id=145&option=com_virtuemart&Itemid=255", "0");
menu77303.addItem("Combo Savings", "https://bloomex.com.au/index.php/Combo-Savings/View-all-products.html", "0");
var menu77246 = ms.addMenu(document.getElementById("menu77246"));
menu77246.addItem("Anniversary", "https://bloomex.com.au/index.php/Anniversary/View-all-products.html", "0");
menu77246.addItem("Birthday", "https://bloomex.com.au/index.php/Birthday/View-all-products.html", "0");
var menu7732 = menu77246.addMenu(menu77246.items[1],0,0);
menu7732.addItem("Moms Birthday", "https://bloomex.com.au/index.php/Mom-s-Birthday/View-all-products.html", "0");
menu7732.addItem("Wife&#039;s Birthday", "https://bloomex.com.au/index.php/Wife-s-Birthday/View-all-products.html", "0");
menu7732.addItem("Friend&#039;s Birthday", "https://bloomex.com.au/index.php/Friend-s-Birthday/View-all-products.html", "0");
menu77246.addItem("Congratulations", "https://bloomex.com.au/index.php/Congratulations/View-all-products.html", "0");
menu77246.addItem("Get Well", "https://bloomex.com.au/index.php/Get-Well/View-all-products.html", "0");
menu77246.addItem("Housewarming", "https://bloomex.com.au/index.php/Housewarming/View-all-products.html", "0");
menu77246.addItem("Just Because", "https://bloomex.com.au/index.php/Just-Because/View-all-products.html", "0");
menu77246.addItem("Love and Romance", "https://bloomex.com.au/index.php/Love-Romance/View-all-products.html", "0");
menu77246.addItem("New Baby", "https://bloomex.com.au/index.php/New-Baby/View-all-products.html", "0");
menu77246.addItem("Sympathy and Funeral", "https://bloomex.com.au/index.php/Sympathy-Funeral/View-all-products.html", "0");
menu77246.addItem("Thank You", "https://bloomex.com.au/index.php/Thank-You/View-all-products.html", "0");
menu77246.addItem("Wedding Flowers", "https://bloomex.com.au/index.php/Wedding-Flowers/View-all-products.html", "0");
var menu77262 = ms.addMenu(document.getElementById("menu77262"));
 menu77262.addItem("Best Selling", "https://bloomex.com.au/index.php/Best-Sellers/View-all-products.html", "0");
menu77262.addItem("Mixed Bouquets", "https://bloomex.com.au/index.php/All-Mixed-Bouquets/View-all-products.html", "0");
var menu77379 = menu77262.addMenu(menu77262.items[1],0,0);
menu77379.addItem("Lilies &amp; Iris Collection", "/index.php/Lilies-and-Iris/View-all-products.html", "0");
menu77379.addItem("Roses &amp; Lilies Collection", "/index.php/Roses-and-Lilies/View-all-products.html", "0");
menu77379.addItem("Daisies &amp; Roses Collection", "/index.php/Daisies-and-Roses/View-all-products.html", "0");
menu77262.addItem("Roses", "https://bloomex.com.au/index.php/Roses/View-all-products.html", "0");
var menu77371 = menu77262.addMenu(menu77262.items[2],0,0);
menu77371.addItem("Half Dozen Roses", "/index.php/Half-Dozen-Roses/View-all-products.html", "0");
menu77371.addItem("Dozen Roses", "/index.php/Dozen-Roses/View-all-products.html", "0");
menu77371.addItem("24 Roses", "/index.php/24-Roses-and-more/View-all-products.html", "0");
menu77371.addItem("36 Roses", "/index.php/36-Roses/View-all-products.html", "0");
menu77262.addItem("Lilies", "https://bloomex.com.au/index.php/Lillies/View-all-products.html", "0");
var menu77385 = menu77262.addMenu(menu77262.items[3],0,0);
menu77385.addItem("Alstromeria (Peruvian Lilies)", "/index.php/Alstromeria-Peruvian-Lilies/View-all-products.html", "0");
menu77385.addItem("Stargazer Lilies", "/index.php/Stargazer-Lilies/View-all-products.html", "0");
menu77262.addItem("Daisies", "https://bloomex.com.au/index.php/Daisies/View-all-products.html", "0");
menu77262.addItem("Iris", "https://bloomex.com.au/index.php/Iris/View-all-products.html", "0");
menu77262.addItem("Orchids &amp; Exotic Flowers", "/index.php?page=shop.browse&category_id=24&option=com_virtuemart&Itemid=275", "0");
menu77262.addItem("Flower Guide", "https://bloomex.com.au/", "0");
var menu77322 = ms.addMenu(document.getElementById("menu77322"));
 menu77322.addItem("Under $50", "https://bloomex.com.au/index.php/Under-$50/View-all-products.html", "0");
menu77322.addItem("$50 to $60", "https://bloomex.com.au/index.php/$50-to-$60/View-all-products.html", "0");
menu77322.addItem("$60 - $70", "https://bloomex.com.au/index.php/$60-to-$70/View-all-products.html", "0");
menu77322.addItem("$70 - $80", "https://bloomex.com.au/index.php/$70-to-$80/View-all-products.html", "0");
menu77322.addItem("$80 - $90", "https://bloomex.com.au/index.php/$80-to-$90/View-all-products.html", "0");
menu77322.addItem("$90 - $100", "https://bloomex.com.au/index.php/$90-to-$100/View-all-products.html", "0");
menu77322.addItem("Over $100", "https://bloomex.com.au/index.php/Over-$100/View-all-products.html", "0");
menu77322.addItem("Best Sellers", "https://bloomex.com.au/index.php/Best-Sellers/View-all-products.html", "0");
var menu77305 = ms.addMenu(document.getElementById("menu77305"));
 menu77305.addItem("Shopping Cart", "https://bloomex.com.au/index.php/View-your-cart-content.html", "0");
menu77305.addItem("Account Details", "https://bloomex.com.au/index.php/View-your-account-details.html", "0");
menu77305.addItem("My Orders", "https://bloomex.com.au/index.php/View-your-account-details.html", "0");
menu77305.addItem("Account Login/ Logout", "https://bloomex.com.au/index.php/Log-in.html", "0");
var menu77327 = ms.addMenu(document.getElementById("menu77327"));
 menu77327.addItem("Live Chat", "https://bloomex.ca/liveperson/livehelp.php?page=user_qa.php&department=1&tab=1", "2");
menu77327.addItem("How to Order", "https://bloomex.com.au/index.php/About-Us/Easy-Ordering-Video-Tutorial.html", "0");
function init77() {
if (TransMenu.isSupported()) {
TransMenu.initialize();
menu77303.onactivate = function() {document.getElementById("menu77303").className = "hover"; };
 menu77303.ondeactivate = function() {document.getElementById("menu77303").className = ""; };
 menu77246.onactivate = function() {document.getElementById("menu77246").className = "hover"; };
 menu77246.ondeactivate = function() {document.getElementById("menu77246").className = ""; };
 menu77262.onactivate = function() {document.getElementById("menu77262").className = "hover"; };
 menu77262.ondeactivate = function() {document.getElementById("menu77262").className = ""; };
 menu77322.onactivate = function() {document.getElementById("menu77322").className = "hover"; };
 menu77322.ondeactivate = function() {document.getElementById("menu77322").className = ""; };
 menu77305.onactivate = function() {document.getElementById("menu77305").className = "hover"; };
 menu77305.ondeactivate = function() {document.getElementById("menu77305").className = ""; };
 menu77327.onactivate = function() {document.getElementById("menu77327").className = "hover"; };
 menu77327.ondeactivate = function() {document.getElementById("menu77327").className = ""; };
 }}
TransMenu.spacerGif = "https://bloomex.com.au/modules/mod_swmenupro/images/transmenu/x.gif";
TransMenu.dingbatOn = "https://bloomex.com.au/modules/mod_swmenupro/images/transmenu/submenu-on.gif";
TransMenu.dingbatOff = "https://bloomex.com.au/modules/mod_swmenupro/images/transmenu/submenu-off.gif"; 
TransMenu.sub_indicator = true; 
TransMenu.menuPadding = 0;
TransMenu.itemPadding = 0;
TransMenu.shadowSize = 2;
TransMenu.shadowOffset = 3;
TransMenu.shadowColor = "#888";
TransMenu.shadowPng = "https://bloomex.com.au/modules/mod_swmenupro/images/transmenu/grey-40.png";
TransMenu.backgroundColor = "#674d7a";
TransMenu.backgroundPng = "https://bloomex.com.au/modules/mod_swmenupro/images/transmenu/white-90.png";
TransMenu.hideDelay = 800;
TransMenu.slideTime = 400;
TransMenu.modid = 77;
TransMenu.selecthack = 1;
TransMenu.renderAll();
if ( typeof window.addEventListener != "undefined" )
window.addEventListener( "load", init77, false );
else if ( typeof window.attachEvent != "undefined" ) {
window.attachEvent( "onload", init77 );
}else{
if ( window.onload != null ) {
var oldOnload = window.onload;
window.onload = function ( e ) {
oldOnload( e );
init77();
}
}else
window.onload = init77();
}
}
-->
</script>
</div>

<!--End SWmenuPro menu module-->
    </TD>
    <TD  height="42" width="321" style="vertical-align:top;background:#D8BEE8 url('<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/for_deliver.png') top right no-repeat;">
  		<a href="https://bloomex.com.au" style="float:right;margin:0px 0px 0px 0px;width:280px;height:42px;" >
  			<div style="color:#ffffff; float:right;margin:4px 0px 0px 0px;width:85px;text-transform:uppercase;"><?php echo $city; ?></div>
		</a>
            </TD>
    <TD class="borderRight2" width="26" height="42"></TD>
    </TR>
</TBODY></TABLE>

<!-----END TOP MENU & SEARCH---->

<!-----BODY ---->
<TABLE cellSpacing="0" cellPadding="0" width="790" align="center" border="0" style="BACKGROUND-COLOR: rgb(239,229,246);">
  <TBODY>
  <TR>
    <TD class="borderLeft" width="28"></TD>
    <TD style="BACKGROUND-IMAGE: url(https://bloomex.com.au/templates/bloomex7/images/leftBg.gif); BACKGROUND-POSITION: 100% 50%; 	background-repeat: no-repeat; BACKGROUND-COLOR: rgb(216,190,232);" vAlign=top width="164">
        <!----  LEGT      ---->
    <TABLE cellSpacing="0" cellPadding="0" width="100%" border=0 >
        <TBODY>
         <TR>
          <TD style="text-align:center;padding:10px;">
	 	<form name="instantSearchForm" method="post" action="https://bloomex.com.au/">
		<div class="search">
		<input type="text"  value="search..." size="15" name="searchword" class="inputbox" alt="search"><input type="submit" class="button" value="ok"><div style="display: none;" class="hiddenDiv" id="instant_search_form"></div>
		</div>
		<input type="hidden" value="search" name="option">
		</form>
          </TD>
         </TR>
         <TR>
         <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>

			<td>
				<?php
					if($activate_loc == 1) {
						 //Location Based SEO
						 
						echo '<div id="addvloc" style="color:#000000;">';
						echo '<b>'.$city.' Flower Delivery</b>';
						echo '<br />';
						if( !empty($tele) ) {
							echo $tele;
						}else {
							echo $location_telephone;	
						}					 
						 echo '</div>';
					 }
				 ?><br/>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><span class="mainlevel" >- - - - - - - - - - - - - - - - - -</span></td></tr>
<tr align="left"><td><span class="mainlevel" >  SHOP BY OCCASION:</span></td></tr>
<!--<tr align="left"><td><a class="mainlevel" href="/index.php/Valentine-s-Day/View-all-products.html">Valentines Day Flowers</a></td></tr>-->
<tr align="left"><td><a href="/index.php?page=shop.browse&amp;category_id=9&amp;option=com_virtuemart&amp;Itemid=49" class="mainlevel" >Birthday Flowers</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Sympathy-Funeral/View-all-products.html" class="mainlevel" >Sympathy Flowers</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Get-Well/View-all-products.html" class="mainlevel" >Get Well Flowers</a></td></tr>
<tr align="left"><td><a href="/index.php/Love-Romance/View-all-products.html" class="mainlevel" >Love and Romance</a></td></tr>
<tr align="left"><td><a href="/index.php/Wedding-Flowers/View-all-products.html" class="mainlevel" >Wedding Flowers</a></td></tr>
<tr align="left"><td><span class="mainlevel" >- - - - - - - - - - - - - - - - - -</span></td></tr>
<tr align="left"><td><span class="mainlevel" >SHOP BY FLOWER:</span></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Roses/View-all-products.html" class="mainlevel" >Roses</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Lillies/View-all-products.html" class="mainlevel" >Lilies</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/All-Mixed-Bouquets/View-all-products.html" class="mainlevel" >Mixed</a></td></tr>
<tr align="left"><td><a href="/index.php/Iris/View-all-products.html" class="mainlevel" >Iris</a></td></tr>
<tr align="left"><td><a href="/index.php/Daisies/View-all-products.html" class="mainlevel" >Daisies</a></td></tr>
<tr align="left"><td><a class="mainlevel" href="https://bloomex.com.au/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0">Gift Baskets - NEW!</a></td></tr>
<tr align="left"><td><a class="mainlevel" href="https://bloomex.com.au/index.php/Fruit-Baskets/View-all-products.html?category_parent_id=183">Fruit Baskets - NEW!</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Extra-Touches/View-all-products.html" class="mainlevel" >Extra Touches</a></td></tr>
<tr align="left"><td><span class="mainlevel" >- - - - - - - - - - - - - - - - - -</span></td></tr>
<tr align="left"><td><span class="mainlevel" >SHOP BY PRICE:</span></td></tr>
</table>			</td>
		</tr>
		</table>
		</TD>
         </TR>
         <TD style="padding-bottom:7px;">		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Under-$50/View-all-products.html" class="mainlevel" >Under $50</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/$50-to-$60/View-all-products.html" class="mainlevel" >$50 to $60</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/$60-to-$70/View-all-products.html" class="mainlevel" >$60 to $70</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/$70-to-$80/View-all-products.html" class="mainlevel" >$70 to $80</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/$80-to-$90/View-all-products.html" class="mainlevel" >$80 to $90</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/$90-to-$100/View-all-products.html" class="mainlevel" >$90 to $100</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Over-$100/View-all-products.html" class="mainlevel" >Over $100</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Best-Sellers/View-all-products.html" class="mainlevel" >Best Sellers</a></td></tr>
<tr align="left"><td><span class="mainlevel" >- - - - - - - - - - - - - - - - - -</span></td></tr>
<tr align="left"><td><span class="mainlevel" >TOP DELIVERY REGIONS:</span></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/sydney-florist/sydney-flowers/index.html" class="mainlevel" >Sydney, NSW</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/melbourne-florist/melbourne-flowers/index.html" class="mainlevel" >Melbourne, VIC</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/brisbane-florist/brisbane-flowers/index.html" class="mainlevel" >Brisbane, QLD</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/canberra-florist/canberra-flowers/index.html" class="mainlevel" >Canberra, ACT</a></td></tr>
</table>			</td>
		</tr>
		</table>
		</TD>
         </TR>
         <TR>
         <TD><IMG height="70" alt="" src="https://bloomex.com.au/templates/bloomex7/images/geotrust_logo.jpg" width="164" border=0>
         </TD>
         </TR>
         <TR>
         <TD style="padding-top:6px;">		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="https://bloomex.com.au/index.php/About-Us/delivery-Policy.html" class="mainlevel" >Delivery Policy</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/About-Us/Next-Day-Delivery-Guaranteed.html" class="mainlevel" >Next Day Delivery</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/About-Us/Return-Policy.html" class="mainlevel" >Return Policy</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/View-your-account-details.html" class="mainlevel" >Track Orders</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/component/option,com_contxtd/Itemid,3/task,blog/" class="mainlevel" >Contact Us</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/About-Us/Customer-Comments.html" class="mainlevel" >About US</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/About-Us/Flower-Care.html" class="mainlevel" >Flower Care</a></td></tr>
</table>			</td>
		</tr>
		</table>
				<table cellpadding="0" cellspacing="0" class="moduletable-nletter">
					<tr>

				<th valign="top">
					E-Mail Exclusives				</th>
			</tr>
					<tr>
			<td>
				<div class="caption">
	Sign up to receive special offers and promotions from Bloomex:
</div>
<div class="nletter">

	<form name="Fnletter" method="POST" action="">
		<input name="email_address" type="text" size="20" value="Enter Email" class="tbox" />
		<input  type="button" id="btn-nletter" name="btn-nletter" class="btn-nletter" value="&nbsp;" />
	</form>
	<span class="loading" id="msg-nletter">&nbsp;</span>	
</div>

<script type="text/javascript">
$j(document).ready(function(){
	$j("#btn-nletter").click(function () {
		$j("#msg-nletter").attr("style", "display:none"); 
		var email_address	= $j("input[name='email_address']").val();
		
		if( !jQuery.trim(email_address) )  {
			alert("Please enter your email address!");	
			return;
		}		
		
		if( !(/^\w+([\.-]*\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email_address)) ) {
			alert("Your email address is incorrect! Eg: example@email.com");	
			return;
		}	
				
		$j.post( "index.php",
			{ 	option: 		"com_nletter", 
				task:		"send",
				email:		email_address,
			  	ajaxSend: function(){
				 	$j("#msg-nletter").html("Sending..."); 
				 	$j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 
		   	  	}
			},			
			function(data){
				if( data == "exist" ) {	
					$j("#msg-nletter").html("Sorry, your email already exists in our mailing list"); 
					$j("#msg-nletter").attr("style", "display:block; color:#ff0000"); 						
				}else {
					$j("#msg-nletter").html("Your email was successfully added to our mailing list"); 
				 	$j("#msg-nletter").attr("style", "display:block; color:#0000ff"); 				
					$j("input[name='email_address']").val("Enter Email");
				}
			}
		);
	});	
	
	
	$j("input[name='email_address']").click(function () {
		if( $j("input[name='email_address']").val() == "Enter Email" ) 	$j("input[name='email_address']").val("");
	});	
});
</script>	
				</td>
		</tr>

		</table>
		<br/></TD>
         </TR>
         <TR>
         <TD>
<hr>
</TD>
         </TR>
         <TR>
         <TD>		<table cellpadding="0" cellspacing="0" class="moduletable">

					<tr>
				<th valign="top">
					Live Support via Skype				</th>
			</tr>
					<tr>
			<td>
				
<br />
<a href="skype:Bloomex1?call" onclick="return skypeCheck();">
<img border="0" src="https://bloomex.ca/modules/jomskype/call_blue.png" /></a><br />

<a href="skype:Bloomex1?chat" onclick="return skypeCheck();">
<img border="0" src="https://bloomex.ca/modules/jomskype/chat_blue.png" /></a>			</td>
		</tr>
		</table>
		<br/></TD>
         </TR>
     </TBODY></TABLE>
<p>  
 	
              <center><a href="mailto:wecare@bloomex.com.au">wecare@bloomex.com.au</a></center>
                      <!----  END LEGT      ---->

    </TD>
    <TD vAlign=top width="568" align="left">
        <!---- BODY ---->
        <!--<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td>&nbsp;
					<a href="/index.php/Valentine-s-Day/View-all-products.html">
						<div style="text-align: center">
							<img border="0" alt=" " src="/templates/bloomex7/images/VD-Main-Banner.png">
						</div>
					</a> 
				</td>
			</tr>
		</tbody>
	</table>-->
	
	<!--FLOWER and GIFT BASKET BANNER-->
	   <table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tbody>
				<tr>
					<td>&nbsp;
						<a href="/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0">
							<img src="https://stage1.bloomex.com.au/templates/bloomex7/images/GB_banner-sm.gif" border="0" alt=" " />
						</a> 
				</td>
			<td>&nbsp;
						<a href="/index.php/Flowers/View-all-products.html">
							<img src="https://stage1.bloomex.com.au/templates/bloomex7/images/flowers_banner.png" border="0" alt=" " />
						</a> 
					</td>
				</tr>
			</tbody>
	  </table>

       <TABLE cellSpacing="0" cellPadding="0" border=0 align="center">
        <TBODY>
        <!--<tr>
        	<td>        		
			<img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/starting-at-29.jpg" style="margin:13px 0px 0px 14px;" />
		</td>
        </tr>-->

        <TR>
         <TD align="center">
         <!-- BEGIN HelpOnClick CODE -->
			<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR><TD height=57 width=155>
						<a href='javascript:void(0)' onclick='window.open("https://app.helponclick.com/help?lang=en&a=4d9f177b48504f4bb25f0580b86aab83","hoc_chat_login","width=720,height=550,scrollbars=no,status=0,toolbar=no,location=no,resizable=no")'>
				<img style="float:left;" border="0" alt="Click for Live Chat" src="https://bloomex.com.au/templates/bloomex7/images/livechat.gif"/>
			</a>
			</TD></TR></TABLE>

			<!-- END HelpOnClick CODE --> 	
		</TD>
	</TR>
<TR>
<TD >



       		       <br>
         		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
			
			
  <table border="0" cellpadding="3" cellspacing="5" width="100%">
<?php
	$nLimit	= 9;
	if ( $category_id ) {
		$q  = "SELECT DISTINCT P.product_sku,  P.product_name, P.product_id, P.product_thumb_image, VMP.product_price,  VMP.saving_price 
			    FROM jos_vm_product AS P 
			    INNER JOIN jos_vm_product_category_xref AS PCX ON P.product_id = PCX.product_id 
			    INNER JOIN jos_vm_category AS C ON C.category_id = PCX.category_id 
			    INNER JOIN jos_vm_product_price AS VMP ON P.product_id = VMP.product_id 
			    WHERE C.category_id='$category_id' AND P.product_publish='Y' 
			    ORDER BY  (VMP.product_price - VMP.saving_price) ASC LIMIT $nLimit";
		$result = mysql_query($q, $link);
		
		$i = 1;
		while ($row = mysql_fetch_assoc($result)) {			
			$sTitlte		= $row['product_name'];
			$sSKU		= $row['product_sku'];
			
			if( !empty($row["saving_price"]) && $row["saving_price"] > 0 && $row["product_price"] >= 0 ) {
				$product_price	= $row["product_price"] - $row["saving_price"];
			}else{
				$product_price	= $row["product_price"];
			}
			
			$nPrice		= LangNumberFormat::number_format($product_price, 2, '.', '');;
			$nProductID	= $row['product_id'];
			$sImage		= $row['product_thumb_image'];
			$sLink		= "$mosConfig_live_site/index.php?option=com_virtuemart&Itemid=257&category_id=$category_id&flypage=shop.flypage&lang=en&manufacturer_id=0&page=shop.product_details&product_id=$nProductID";
			
			if( $i == 1 ) {
				 echo "<tr>";
			}
			
?>			<td align="center">
				<span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $sTitlte; ?></span><br>
				<br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sSKU; ?></span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span style="font-weight:bold">
				<?php echo $nPrice; ?></span>
				
				 </span>
				<a title="<?php echo $sTitlte; ?>" href="<?php echo $sLink; ?>"><img  class="product-random" height="200" width="170" border="0" alt="<?php echo $sTitlte; ?>" src="<?php echo $mosConfig_live_site; ?>/components/com_virtuemart/shop_image/product/<?php echo $sImage; ?>" /></a><br />
				<div class='form-add-cart'>
					<a href="https://bloomex.com.au/index.php?page=shop.cart&func=cartAdd&product_id=<?php echo $nProductID; ?>&option=com_virtuemart&Itemid=1&link=main" class='mod-add-to-cart'  title="Add to Cart: <?php echo $sTitlte; ?>" href="#"><IMG height=17 src="https://bloomex.com.au/components/com_virtuemart/shop_image/ps_image/button.png" width=80 border=0></a>
				</div>
			</td>
<?php			
			
			if( $i % 3 == 0 && $i > 1 && $i < $nLimit ) {
				 echo "</tr><tr>";
			}
			
			
			if( $i == $nLimit ) {
				 echo "</tr>";
			}
			
			$i++;
		}
	}
?>
  </table>
    			</td>

		</tr>
		</table>
		   <br>
         <div align="center"></div>
                  <TABLE style="MARGIN-LEFT: 0px" cellSpacing=0 cellPadding=0 width="100%" border=0>
         <TBODY>
         <TR>
          <TD align=middle><IMG height=23 alt="" src="https://bloomex.com.au/templates/bloomex7/images/ocassion.gif" width=186 border=0></TD>
           	
              <TD align=middle><IMG height=23 alt="" src="https://bloomex.com.au/templates/bloomex7/images/flowergift.gif" width=186 border=0></TD>

              	
           	
             <TD align=middle width=186><IMG height=23 alt="" src="https://bloomex.com.au/templates/bloomex7/images/featured.gif" width=186 border=0></TD>
             	
        </TR>
        <TR valign="top">
          <TD style="background: Rgb(239,237,237);">
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Anniversary/View-all-products.html" class="mainlevel" >Anniversary</a></td></tr>

<tr align="left"><td><a href="https://bloomex.com.au/index.php/Birthday/View-all-products.html" class="mainlevel" >Birthday</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Congratulations/View-all-products.html" class="mainlevel" >Congratulations</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Thank-You/View-all-products.html" class="mainlevel" >Thank You</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Get-Well/View-all-products.html" class="mainlevel" >Get Well</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Housewarming/View-all-products.html" class="mainlevel" >Housewarming</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Just-Because/View-all-products.html" class="mainlevel" >Just Because</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Love-Romance/View-all-products.html" class="mainlevel" >Love and Romance</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/New-Baby/View-all-products.html" class="mainlevel" >New Baby</a></td></tr>
<tr align="left"><td><a href="https://bloomex.com.au/index.php/Sympathy-Funeral/View-all-products.html" class="mainlevel" >Sympathy and Funeral</a></td></tr>

</table>			</td>
		</tr>
		</table>
		          </TD>
          <TD style="background: Rgb(239,237,237);">
          		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="left"><td><a href="/index.php/Combo-Savings/View-all-products.html?category_parent_id=145" class="mainlevel" >Online specials</a></td></tr>
<tr align="left"><td><a href="/index.php/Gift-Baskets/View-all-products.html?category_parent_id=0" class="mainlevel" >Gift Baskets</a></td></tr>
<tr align="left"><td><a href="/index.php/Fruit-Baskets/View-all-products.html?category_parent_id=183" class="mainlevel" >Fruit Baskets</a></td></tr>
<tr align="left"><td><a href="/index.php/All-Mixed-Bouquets/View-all-products.html" class="mainlevel" >Mixed Bouquets</a></td></tr>
<tr align="left"><td><a href="/index.php/All-Arrangements/View-all-products.html" class="mainlevel" >Arrangements</a></td></tr>
<tr align="left"><td><a href="/index.php/Roses/View-all-products.html" class="mainlevel" >Roses</a></td></tr>
<tr align="left"><td><a href="/index.php/Lillies/View-all-products.html" class="mainlevel" >Lilies</a></td></tr>
<tr align="left"><td><a href="/index.php/Daisies/View-all-products.html" class="mainlevel" >Daisies</a></td></tr>

<tr align="left"><td><a href="/index.php/Lillies/View-all-products.html" class="mainlevel" >Iris</a></td></tr>
<tr align="left"><td><a href="/index.php/Orchids/View-all-products.html" class="mainlevel" >Orchids</a></td></tr>
<!--<tr align="left"><td><a href="https://bloomex.com.au/index.php/Sunflowers/View-all-products.html" class="mainlevel" >Sunflowers</a></td></tr>-->
</table>			</td>
		</tr>
		</table>
		          </TD>
          <TD style="vertical-align:top;background-color:white;font-family:Verdana, arial, sans serif;font-size: 0.75em;color: rgb(81,81,81);width: 186px;padding: 0px;padding-top: 10px;background-image: url(templates/bloomex7/images/featBottom.gif);background-repeat: no-repeat;background-position: bottom left;" width=186>
          		<table cellpadding="0" cellspacing="0" class="moduletable">

				<tr>
			<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center" >
				<td width="100%">
					<?php 
						//FEATURE
						$sTitlte		= "Designer's Collection I";
						$sSKU		= "DC01";
						$nPrice		= "$40.00";
						$nProductID	= "821";
						$sImage		= "33e6dcef83b25773a97d016f155e65e9.jpg";
						$sLink		= "/index.php/Designers-Collection/Designer-s-Collection-I/Detailed-product-flyer.html";
					?>	
					<span style="font-weight:bold;font-size: 11px; color:#666666;"><?php echo $sTitlte; ?></span><br>
<br /><span style="font-weight:normal;font-size: 11px; color:#666666;VERTICAL-ALIGN: justify;"><?php echo $sSKU; ?></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;font-size: 11px; color:#CC0000;"><span style="font-weight:bold"><?php echo $nPrice; ?></span>

 </span>
<a title="<?php echo $sTitlte; ?>" href="<?php echo $sLink; ?>"><img  class="product-random" height="200" width="170" border="0" alt="<?php echo $sTitlte; ?>" src="https://bloomex.com.au/components/com_virtuemart/shop_image/product/<?php echo $sImage; ?>" /></a><br />
<br />
				</td>
			</tr>
		</table>
			</td>
		</tr>
		</table>
		          </TD>

        </TR></TBODY>
       </TABLE>
              </TD></TR></TBODY></TABLE>
        <!----END BODY---->
    </TD>
    <TD class="borderRight2" width=26></TD>
    </TR>
    <TR>
    <TD class="borderLeft"></TD>

    <TD colspan=2 style="BACKGROUND-COLOR: rgb(216,190,232)" align=right>
     <TABLE cellSpacing=0 cellPadding=0 border=0 width=568>
        <TR>
          <TD style="PADDING-LEFT: 16px" height=24> 
                    		<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<ul id="mainlevel-nav"><li><a href="https://bloomex.com.au/" class="mainlevel-nav" id="active_menu-nav">Home</a></li><li><a href="https://bloomex.com.au/index.php/component/option,com_contxtd/Itemid,3/task,blog/" class="mainlevel-nav" >Contact US</a></li><li><a href="https://bloomex.com.au/index.php/Search.html" class="mainlevel-nav" >Search</a></li><li><a href="https://bloomex.com.au/index.php/Log-in.html" class="mainlevel-nav" >LOGIN</a></li></ul>			</td>

		</tr>
		</table>
		          </TD>
        </TR>
      </TABLE>
    </TD>
    <TD class="borderRight2" width=26></TD>
    </TR>
</TABLE>

<!----END BODY ---->
<TABLE cellSpacing=0 cellPadding=0 width=790 align=center border=0>
  <TBODY>
    <TR>
    <TD class="borderLeftBottom" width=29 height=11></TD>
    <TD class="borderBottom" width=734></TD>
    <TD class="borderRightBottom" width=27 height=11></TD>
    </TR>
    <TR>
    <TD colspan=3 align="center">

    			<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table border="0" cellspacing="5" cellpadding="5" class="htmtableborders" align="center" style="width: 50%"><tbody><tr><td><p align="center"><a href="index.php?option=com_content&amp;task=view&amp;id=17&amp;Itemid=47">&nbsp;Term of Use</a></p></td><td><p align="center"><a href="index.php?option=com_content&amp;task=view&amp;id=18&amp;Itemid=48">Privacy Policy</a></p></td><td><p align="center"><a href="index.php?option=com_content&amp;task=view&amp;id=84&amp;Itemid=48">Site Map</a></p></td></tr></tbody></table>			</td>
		</tr>
		</table>
				<table cellspacing="0" cellpadding="0" border="0" align="center" style="width: 90%">

			<tbody>
				<tr>
					<td style="text-align:left;">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) {return;}
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
						
						<div class="fb-like"  data-send="false" data-width="450" data-show-faces="false"></div>
					</td>
				</tr>

			</tbody>
		</table>
			<table cellpadding="0" cellspacing="0" class="moduletable">
				<tr>
			<td>
				<table border="0" cellspacing="5" cellpadding="5" width="775" height="406" class="htmtableborders" align="center"><tbody><tr><td style="color: #49536e"><div align="justify">   </div><p class="MsoNormal" align="justify"><span style="color: #595959">&copy; 2011&nbsp;Bloomex Inc - At Bloomex we are devoted to making it simple for you to send flowers online for any occasion. We provide a wide range of flower bouquets and arrangement that you can explore right here on our website. You can find flowers for <a href="/index.php/Birthday/View-all-products.html">Birthdays</a> , <a href="/index.php/Sympathy-Funeral/View-all-products.html">Sympathy</a> , <a href="/index.php/Love-Romance/View-all-products.html">Love &amp; Romance</a> , <a href="/index.php/Thank-You/View-all-products.html">Thank You</a> , <a href="/index.php/New-Baby/View-all-products.html">New Baby</a> , and more.</span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">We are dedicated to delivering fresh flowers with the highest quality. In order to deliver this promise to our customers, we have established a strong bargaining power with the best flower growers in the world. This has enabled us to purchase and deliver quality flowers at a price that is 35 &ndash; 40% cheaper than other online and traditional florists, and we pass our savings on to our customers. We also have established relationships with local florists across Australia to make sure that we deliver the freshest flowers possible and delivered on time. Bloomex deploys the smartest technology to make flower delivery is efficient and fast. </span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">Our customer service team is ready to provide outstanding service via phone (TOLL FREE 1-800-905-147) or live chat. You can place your flower order online. We will help you find and deliver the perfect flower <a href="/index.php/All-Arrangements/View-all-products.html">arrangements</a> , <a href="/index.php/All-Mixed-Bouquets/View-all-products.html">bouquets</a> , <a href="/index.php/Roses/View-all-products.html">roses</a> , <a href="/index.php/Lillies/View-all-products.html">lilies</a> , <a href="/index.php/Daisies/View-all-products.html">daisies </a> for as <a href="/index.php/Root-category-65/Category-69/View-all-products.html">low as $30</a>  or <a href="/index.php/Root-category-65/Category-70/View-all-products.html">higher than $50</a> .</span></p><div align="justify">  </div><p class="MsoNormal" align="justify"><span style="color: #595959">In order to make sure that the flowers are delivered on time we have a Next Day Delivery policy. All you have to do is order by 2:30pm of the receiver&rsquo;s time. We send flowers to almost any location in New South Wales, Australia Capital Territory, Queensland and Victoria. Major delivery areas include: <a href="/index.php/florist/sydney/">Sydney Florist</a> , <a href="/index.php/florist/melbourne_florist/">Melbourne Florist</a>  , <a href="/index.php/florist/brisbane/">Brisbane Florist</a> , <a href="/index.php/florist/canberra/">Canberra Florist</a> , </span></p><div align="justify">  </div><div align="center"><span style="color: #595959"><a href="https://www.bloomex.ca">Bloomex.ca</a>  &bull; <a href="https://www.bloomexusa.com">Bloomexusa.com</a>  &bull; <a href="https://www.bloomex.com.au">Bloomex.com.au</a></span></div><p style="text-align: center" class="MsoNormal" align="center"><span style="color: #595959"> </span></p>  </td></tr><tr><td><table border="0" style="width: 100%"><tbody><tr><td><p style="margin: 0cm 0cm 0pt; text-align: center" class="MsoNormal" align="center"><strong><span style="font-size: 7.5pt; color: #cc0000; font-family: Verdana">Only one promotional offer per order. Promotional offers can not be combined.</span></strong><span style="font-size: 7.5pt; color: #cc0000; font-family: Verdana">&nbsp;</span></p>  </td></tr></tbody></table></td></tr><tr><td><div align="center"><strong>Major Delivery Areas:</strong><br />  <a href="https://bloomex.com.au/index.php/florist/sydney/">Sydney Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/newcastle/">Newcastle Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/melbourne_florist/">Melbourne Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/geelong/">Geelong Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/brisbane/">Brisbane Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/gold_coast/">Gold Coast Flowers</a>&nbsp;  <a href="https://bloomex.com.au/index.php/florist/toowoomba/">Toowoomba Flowers</a> |  <a href="https://bloomex.com.au/index.php/florist/canberra/">Canberra Flowers</a> </div></td></tr></tbody></table>			</td>

		</tr>
		</table>
		

    <!--FOOTER!-->
    

<div align="center">
	&copy; 2012 bloomex.com.au</div>

<div align="center">

</div>
    <div align="center"></div>

    </TD></TR>
  </TBODY></TABLE>
 


<!-- #1206 (29/06/2011)-->
<script type="text/javascript">

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-24439979-1']);
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>


<a href="/index.php/Roses/View-all-products.html">
	<img border="0" src="templates/bloomex7/images/Roses.png" style="bottom: 0pt;position: fixed;left: 0pt;z-index:10">
</a>
<a href="https://bloomex.com.au/index.php/Designer-Collections/View-all-products.html">
	<img border="0" src="templates/bloomex7/images/Designers-Collection_NEW.png" style="bottom: 0pt;position: fixed;right: 0pt;z-index:10">
</a>

</BODY>
</HTML>
<?php
mysql_close($link);
?>
<!-- 1328728722 -->
