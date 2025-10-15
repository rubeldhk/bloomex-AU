<?php
/**
* VirtueMart LuckyZoom Module
* NOTE: THIS MODULE REQUIRES THE PHPSHOP COMPONENT FOR MOS!
*
* @version $Id
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2007 LuckyTeam
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
*/

global $_MOS_OPTION, $mainframe;

$pages = $params->get('pages', "both");
$page=(isset($_REQUEST["page"]))?$_REQUEST["page"]:'';
if($pages != "both"){
  if($_REQUEST["page"] != $pages){
	return;
  }
}

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

?>

<?php

if (!defined( '_JOS_LUCKYPHOTO_MODULE' )) {

  $GLOBALS["lzp_msg"] = $params->get('msg', "");

  if($GLOBALS["lzp_msg"] != ""){
	$GLOBALS["lzp_msg"] = "<br/>".$GLOBALS["lzp_msg"];
  }

  define( '_JOS_LUCKYPHOTO_MODULE', 1 );

  $pattern = '/<\s*(title)\s*([^>]*)(?:>(.*?)<\/\1>|\/>)/s';

  $buf = ob_get_contents(); ob_clean();
  
  $buf = preg_replace_callback($pattern, "callback_LuckyPhoto_head", $buf);
  
  $pattern = '/<\s*(script)\s*([^>]*)(?:>(.*?)<\/\1>|\/>)/s';
	
  if($params->get('mode', "mode1") == "mode2"){
	$newbuf = $buf;
  } else {
	$newbuf = $_MOS_OPTION['buffer'];
  }
  if($page!='shop.product_details'){
  $newbuf = preg_replace_callback($pattern, "callback_LuckyPhoto", $newbuf);
  }
  else{
     $newbuf = preg_replace_callback($pattern, "callback_LuckyPhoto2", $newbuf);   
  }
   if(isset($GLOBALS["luckyPhoto_photos"])){
  if(count($GLOBALS["luckyPhoto_photos"])){

  $newbuf .= '<script type="text/javascript">'."\n".
    'var photos = {';

  foreach($GLOBALS["luckyPhoto_photos"] as $ph){
        $ii[] = $ph["id"]." : "."'".$ph["big"]."'";
  }

  $newbuf .= join(", \n", $ii);

  $newbuf .= "\n".
  '};'."\n".
  'var options = {'."\n".
        'speed : '.$params->get('speed', "70").','."\n".
        'background_color : "'.$params->get('bgcolor', "#000").'",'."\n".
        'background_opacity : '.$params->get('bgopacity', "100").', // 0-100'."\n".
	'preload_image : "'.$mosConfig_live_site.$params->get('progress', "/modules/luckyphoto/progr02.gif").'", // progress bar image'."\n".
        'image_border : "'.$params->get('border', "3px solid #fff").'" //css style rule for a border'."\n".
    '};'."\n".
    'zoom = new luckyPhoto(photos, options);'."\n".
    'zoom.init();'."\n".
  '</script>'."\n";

  }
   }
  if($params->get('mode', "mode1") == "mode2"){
	$buf = $newbuf;
  } else {
	$_MOS_OPTION['buffer'] = $newbuf;
  }
				
  echo $buf;
  
}

function callback_LuckyPhoto_head($matches)
{
  global $mosConfig_live_site, $mainframe;

  return $matches[0]."\n".
	'<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/luckyphoto/LuckyPhoto.js"></script>';
}

function callback_LuckyPhoto2($matches)
{
  global $mosConfig_live_site;

	if(preg_match("/shop_image\/product/m", $matches[3])){
	  if(preg_match_all("/(http|https):\/\/(.*?)\.(jpg|png|gif)(.*?)[\\\\\"\']/i", $matches[3], $images)){
		$img_big_src = substr($images[0][0], 0, strlen($images[0][0])-1);
		$img_small_src = substr($images[0][1], 0, strlen($images[0][1])-1);

		preg_match("/alt=\"(.*?)\"/", $matches[3], $alt);
		$alt = $alt[1];
		
		$img_small_height = "";
		if(preg_match("/height=\"(.*?)\"/", $matches[3], $hh)){
		    $img_small_height = $hh[1];
		}

		$img_small_path = $img_small_src;

		preg_match("/^".preg_quote($mosConfig_live_site, "/")."(.*?)product\/(.*?)$/", $img_big_src, $image_paths);
		$img_big_path = IMAGEPATH."product/".$image_paths[2];
		$dim = @getimagesize($img_big_path);
		if(!$dim) return $matches[0];
		$img_big_width = $dim[0];
		$img_big_height = $dim[1];

		if(preg_match("/^".preg_quote($mosConfig_live_site, "/")."(.*?)product\/(.*?)$/", $img_small_src, $image_paths)){
		  $img_small_path = IMAGEPATH."product/".$image_paths[2];
		}
		$dim = @getimagesize($img_small_path);
		$img_small_dim = '';
		if($dim){
		  $img_small_dim = $dim[3];
		  $img_small_width = $dim[0];
		  if($img_small_height == ""){
		    $img_small_height = $dim[1];
		  }
		} else {
		  preg_match("/newxsize=(\d+)/", $matches[3], $img_small_width);
		  $img_small_width = $img_small_width[1];
		  if($img_small_height == ""){
		    preg_match("/newysize=(\d+)/", $matches[3], $img_small_height);
		    $img_small_height = $img_small_height[1];
		  }
		}

		$id = md5($img_small_path);

		$ph["id"] = "sc".$id;
		$ph["src"] = $img_small_src;
		$ph["big"] = $img_big_src;
		$GLOBALS["luckyPhoto_photos"][] = $ph;
               
		return '<img id="sc'.$id.'" src="'.$img_small_src.'" width="100%" alt="'.$alt.'"/>'.$GLOBALS["lzp_msg"];
                

	  }
	}
	
	return $matches[0];

}
function callback_LuckyPhoto($matches)
{
  global $mosConfig_live_site;

	if(preg_match("/shop_image\/product/m", $matches[3])){
	  if(preg_match_all("/(http|https):\/\/(.*?)\.(jpg|png|gif)(.*?)[\\\\\"\']/i", $matches[3], $images)){
		$img_big_src = substr($images[0][0], 0, strlen($images[0][0])-1);
		$img_small_src = substr($images[0][1], 0, strlen($images[0][1])-1);

		preg_match("/alt=\"(.*?)\"/", $matches[3], $alt);
		$alt = $alt[1];
		
		$img_small_height = "";
		if(preg_match("/height=\"(.*?)\"/", $matches[3], $hh)){
		    $img_small_height = $hh[1];
		}

		$img_small_path = $img_small_src;

		preg_match("/^".preg_quote($mosConfig_live_site, "/")."(.*?)product\/(.*?)$/", $img_big_src, $image_paths);
		$img_big_path = IMAGEPATH."product/".$image_paths[2];
		$dim = @getimagesize($img_big_path);
		if(!$dim) return $matches[0];
		$img_big_width = $dim[0];
		$img_big_height = $dim[1];

		if(preg_match("/^".preg_quote($mosConfig_live_site, "/")."(.*?)product\/(.*?)$/", $img_small_src, $image_paths)){
		  $img_small_path = IMAGEPATH."product/".$image_paths[2];
		}
		$dim = @getimagesize($img_small_path);
		$img_small_dim = '';
		if($dim){
		  $img_small_dim = $dim[3];
		  $img_small_width = $dim[0];
		  if($img_small_height == ""){
		    $img_small_height = $dim[1];
		  }
		} else {
		  preg_match("/newxsize=(\d+)/", $matches[3], $img_small_width);
		  $img_small_width = $img_small_width[1];
		  if($img_small_height == ""){
		    preg_match("/newysize=(\d+)/", $matches[3], $img_small_height);
		    $img_small_height = $img_small_height[1];
		  }
		}

		$id = md5($img_small_path);

		$ph["id"] = "sc".$id;
		$ph["src"] = $img_small_src;
		$ph["big"] = $img_big_src;
		$GLOBALS["luckyPhoto_photos"][] = $ph;
               
		return '<img id="sc'.$id.'" src="'.$img_small_src.'" height="'.$img_small_height.'" alt="'.$alt.'"/>'.$GLOBALS["lzp_msg"];
                

	  }
	}
	
	return $matches[0];

}

?>