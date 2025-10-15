<?php
/* AutoScript-Protection
 * - Codeimage
 *
 * @author: Dominik Paulus, [email]mail@dpaulus.de[/email]
 * @date: 05/25/06
 * @version: 3.4b
 *
 */
 include "../../administrator/components/com_easybook/config.easybook.php";
 // Imagetype: JPEG, PNG, GIF
 $type = $eb_spamfiletyp;
 $codeid = $_GET['CodeID'];
 $codeid = mysql_escape_string($codeid);
 // Imagecolors
 function hex2rgb ( $hex )
{
    $hex = preg_replace("/[^a-fA-F0-9]/", "", $hex);
    $rgb = array();
    if ( strlen ( $hex ) == 3 )
    {
        $rgb[0] = hexdec ( $hex[0] . $hex[0] );
        $rgb[1] = hexdec ( $hex[1] . $hex[1] );
        $rgb[2] = hexdec ( $hex[2] . $hex[2] );
    }
    elseif ( strlen ( $hex ) == 6 )
    {
        $rgb[0] = hexdec ( $hex[0] . $hex[1] );
        $rgb[1] = hexdec ( $hex[2] . $hex[3] );
        $rgb[2] = hexdec ( $hex[4] . $hex[5] );
    }
    else
    {
        return "ERR: Incorrect colorcode, expecting 3 or 6 chars (a-f, A-F, 0-9)";
    }
    return $rgb;
}
$back_rgb = hex2rgb($eb_spambgcolour);
$code_rgb = hex2rgb($eb_spamcodecolour);
$line_rgb = hex2rgb($eb_spamlinecolour);
$border_rgb = hex2rgb($eb_spambordercolour);

 $colors = array(
                // R    G    B   // Only values between 0-255!
                  $back_rgb[0], $back_rgb[1], $back_rgb[2], // Background
                  $code_rgb[0], $code_rgb[1], $code_rgb[2],   // Code
                  $line_rgb[0], $line_rgb[1], $line_rgb[2],   // Vertical Lines
                  $border_rgb[0], $border_rgb[1], $border_rgb[2]    // Border (Last value without ',')
 );
 // Imagesize
 $x = 250; // width
 $y = 70; // height

// ============== Do not change anything below this line! ============
$y2 = $y/2; $x2 = $x/2;

// Codetype
$numeric = true; // true = numeric, false = alphanumeric


mt_srand((double)microtime()*1000000);

// Fontsetup
$fonts = array();
$fd = opendir('./fonts/');
while (false !== ($filename = readdir($fd)))
   $fonts[] = $filename;
rsort($fonts);
if(count($fonts) < 3) die('No fonts found!');

$font = './fonts/'.$fonts[mt_rand(0,(count($fonts)-3))];

// Numerical code
if($numeric)
	$seccode = strval(mt_rand(10000, 99999));
else {
//	$string = "abcdefghijklmnopqrstuvwxyz0123456789";
	$string = "abcdefghjkmnpqrstuvwxyz0123456789"; // better
	$stringlen = strlen($string);
	$seccode = "";
	for($i = 0; $i < 5; $i++)
		$seccode .= $string{mt_rand(0, $stringlen)};
}

$clen = strlen($seccode);

include_once("../../configuration.php");
mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
mysql_select_db($mosConfig_db);

$sqlab = "select * from ".$mosConfig_dbprefix."easybook_code";
$res = mysql_query($sqlab);
while ($code = mysql_fetch_assoc($res))
{
	$age = time() - $code["codedate"];
	if($age > 600)
	{
	$sqlab = "delete from ".$mosConfig_dbprefix."easybook_code where CodeID = '".$code["CodeID"]."'";
	mysql_query($sqlab);
	}
}

$sqlab = "select * from ".$mosConfig_dbprefix."easybook_code where CodeID = '$codeid'";
$res = mysql_query($sqlab);
if (!mysql_num_rows($res))
{
$sqlab = "insert ".$mosConfig_dbprefix."easybook_code (CodeID, CodeMD5, codedate) values ('$codeid', '".md5($seccode)."','".time()."')";
mysql_query($sqlab);
}
else
{
$sqlab = "UPDATE ".$mosConfig_dbprefix."easybook_code SET CodeMD5 = '".md5($seccode)."', codedate = '".time()."' WHERE CodeID = '$codeid'";
mysql_query($sqlab);
}


// create image
$im = ImageCreateTrueColor($x, $y) or die('ImageCreate error!');

// Image colors
$bgcolor     = ImageColorAllocate($im, $colors[0], $colors[1], $colors[2]);
$fontcolor   = ImageColorAllocate($im, $colors[3], $colors[4], $colors[5]);
$linecolor   = ImageColorAllocate($im, $colors[6], $colors[7], $colors[8]);
$bordercolor = ImageColorAllocate($im, $colors[9], $colors[10], $colors[11]);
$alphacolor  = ImageColorAllocate($im, 0, 255, 0);
ImageFill($im, 0, 0, $bgcolor);
//ImageColorTransparent($im, $bgcolor);
/**/
// Code
$xspace =70;
$yspace = 60;
$size = 25;
$angle = 20;
$ttfborders = array();
for($i = 0; $i < $clen; $i++) {
	$tmp = ImageCreateTrueColor($xspace,$yspace);
	ImageFill($tmp, 0, 0, $bgcolor);
	$ttfborders[] = ImageTTFText($tmp, $size+mt_rand(0, 8), mt_rand(-$angle, $angle), 20, $yspace-10, $fontcolor, $font, $seccode{$i});
	morph($tmp, 0, 0, 50, 50);
	ImageColorTransparent($tmp, $bgcolor);
	ImageCopyMerge($im, $tmp, ($i)*50, 2, 0, 0, $xspace, $yspace, 100);
	ImageDestroy($tmp);
}

// Morph
function morph($im, $sx, $sy, $w, $h) {
	$morphx = $h;
	$morphy = mt_rand(3.5,5.2);
	$mx = $sx;
	$my = $sy;
	$mvalues = array();
	for($i = 0; $i < $morphx/2; $i++) {
		$mvalues[] = $mx-(log($i+1)*$morphy);
		//                        dx           dy     sx   sy         w   h
		ImageCopyMerge($im, $im, $mvalues[$i], $my+$i, $mx, $my+$i, $w+20, 1, 0);
	}
	$mvalues = array_reverse($mvalues);
	$mvcount = count($mvalues);
	for($i = 0; $i < $mvcount; $i++) {
		//                        dx           dy     sx   sy                         w   h
		ImageCopyMerge($im, $im, $mvalues[$i], $my+$i+$mvcount, $mx, $my+$i+$mvcount, $w+20, 1, 0);
	}
}

// Wave
ImageSetThickness($im, 3);
$ux = $uy = 0;
$vx = 0; //mt_rand(10,15);
$vy = mt_rand($y2-3, $y2+3);

for($i = 0; $i < 10; $i++) {
	$ux = $vx + mt_rand(20,30);
	$uy = mt_rand($y2-8,$y2+8);
	ImageSetThickness($im, mt_rand(1,2));
	ImageLine($im, $vx, $vy, $ux, $uy, $linecolor);
	$vx = $ux;
	$vy = $uy;
}
ImageLine($im, $vx, $vy, $x, $y2, $linecolor);

// Triangle
ImageSetThickness($im, 3);
$ux = mt_rand($x2-10, $x2+10);
$uy = mt_rand($y2-10, $y2-30);
ImageLine($im, mt_rand(10,$x2-20), $y, $ux, $uy, $linecolor);
ImageSetThickness($im, 1);
ImageLine($im,  mt_rand($x2+20,$x-10), $y, $ux, $uy, $linecolor);
ImageSetThickness($im, 1);

// Border
ImageSetThickness($im, 1);
ImageLine($im, 0, 0, 0, $y, $bordercolor); // left
ImageLine($im, 0, 0, $x, 0, $bordercolor); // top
ImageLine($im, 0, $y-1, $x, $y-1, $bordercolor); // bottom
ImageLine($im, $x-1, 0, $x-1, $y-1, $bordercolor); // right

for($i = $x/$clen; $i < $x; $i+=$x/$clen)
	ImageLine($im, $i, 0, $i, $y, $bordercolor);

// Debug
//ImageString($im, 8, 0, $y-20, $font, $fontcolor);

// Create Image
switch($type) {
  ///////////////////////////////////////////
  case 'JPEG':
    // JPEG output
    Header("Content-Type: image/jpeg");
   ImageJPEG($im,"",75);
  break;
  ///////////////////////////////////////////
  case 'PNG':
    // PNG output
    Header("Content-Type: image/png");
   ImagePNG($im);
  break;
  ///////////////////////////////////////////
  case 'GIF':
    // GIF output
    Header("Content-Type: image/gif");
    ImageGIF($im);
  break;
  ///////////////////////////////////////////
  default:
    die("Wrong \$type in img.php (should be JPEG, PNG or GIF)\n");
  ///////////////////////////////////////////
}

ImageDestroy($im);
?>
