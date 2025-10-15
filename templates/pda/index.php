<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $cur_template,$mosConfig_absolute_path,$mosConfig_sitename,$_VERSION;
global $pdabotparams,$pdabotversion,$pdahome;

if( !isset($pdabotversion) || $pdabotversion!=212 )
	die('Inconsistency between versions of pda-mambot and pda-template.');


if(!isset($pdabotparams))
{
 	$pdabotparams = new mosParameters( '' );
	$pdabotparams->def( 'useragent', 1 );
	$pdabotparams->def( 'subdomain', 1 );
	$pdabotparams->def( 'subdomainname', 'pda' );
	$pdabotparams->def( 'header1', 'header' );
	$pdabotparams->def( 'header2', '' );
	$pdabotparams->def( 'pathway', 1 );
	$pdabotparams->def( 'middle1', '' );
	$pdabotparams->def( 'middle2', '' );
	$pdabotparams->def( 'footer1', 'footer' );
	$pdabotparams->def( 'footer2', '' );
	$pdabotparams->def( 'jfooter', 1 );
	$pdabotparams->def( 'homepage', '' );
	$pdabotparams->def( 'pathwayhome', 1 );
	$pdabotparams->def( 'componentonhome', 1 );
	$pdabotparams->def( 'head', 0 );
	$pdabotparams->def( 'allowextedit', 0 );
	$pdabotparams->def( 'removeimg', 0 );
	$pdabotparams->def( 'removeiframe', 0 );
	$pdabotparams->def( 'removeobject', 0 );
	$pdabotparams->def( 'removeapplet', 0 );
	$pdabotparams->def( 'removeembed', 0 );
	$pdabotparams->def( 'removescript', 0 );
	$pdabotparams->def( 'utf', 0 );
	$pdabotparams->def( 'pdatemplate', 'pda' );
	$pdabotparams->def( 'embedcss', 0 );
	$pdabotparams->def( 'content', 0 );
	$pdabotparams->def( 'xmlhead', 1 );
	$pdabotparams->def( 'xmlhtml', 1 );
	$pdabotparams->def( 'doctype', 1 );
	$pdabotparams->def( 'gzip', 0 );
}

$iso=split('=',_ISO);
$charset_source=$iso[1];
$charset=$pdabotparams->get( 'utf' )?'utf-8':$charset_source;

$contenttype='text/html';
switch( $pdabotparams->get( 'content' ) )
{
case 1:
	$contenttype='application/vnd.wap.xhtml+xml';
	break;
case 2:
	$contenttype='application/xhtml+xml';
	break;
case 3:
	$contenttype='text/xhtml';
	break;
}

header('Content-type: '.$contenttype.'; charset='.$charset);

if( $pdabotparams->get( 'xmlhead' ) )
        echo '<?xml version="1.0" encoding="', $charset, '" ?>',"\n";

switch( $pdabotparams->get( 'doctype' ) )
{
case 1:
	echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">',"\n";
	break;
case 2:
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',"\n";
	break;
case 3:
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',"\n";
	break;
}

if( $pdabotparams->get( 'xmlhead' ) )
	echo '<html xmlns="http://www.w3.org/1999/xhtml">',"\n";
else
	echo '<html>',"\n";
?>
<head>
<meta http-equiv="Content-Type" content="<?php echo $contenttype; ?>; charset=<?php echo $charset; ?>" />
<?php
if( $pdabotparams->get( 'head' ) )
{
	global $mainframe;
	echo '<title>', $mainframe->_head['title'], '</title>',"\n";
}
else
	mosShowHead();

if( $pdabotparams->get( 'embedcss' ) )
{
	echo '<style>',"\n";
	@readfile($mosConfig_absolute_path.'/templates/'.$cur_template.'/css/template_css.css');
	echo '</style>',"\n";
}
else
	echo '<link href="', $mosConfig_live_site, '/templates/', $cur_template, '/css/template_css.css" rel="stylesheet" type="text/css" />',"\n";

if( $pdabotparams->get( 'allowextedit') && $my->id)
	initEditor();
?>
</head>
<body>
<?php

$modulepos=$pdabotparams->get( 'header1' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

$modulepos=$pdabotparams->get( 'header2' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

if( $pdabotparams->get( 'pathway' ) )
	if( !isset($pdahome) || $pdabotparams->get( 'pathwayhome' ) )
		mosPathway();

$modulepos=$pdabotparams->get( 'middle1' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

$modulepos=$pdabotparams->get( 'middle2' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

if( !isset($pdahome) || $pdabotparams->get( 'componentonhome' ) )
	mosMainBody();

$modulepos=$pdabotparams->get( 'footer1' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

$modulepos=$pdabotparams->get( 'footer2' );
if( $modulepos && mosCountModules($modulepos)>0) {
	?><div id="<?php echo $modulepos; ?>"><?php mosLoadModules($modulepos,-2); ?></div><?php
}

if( $pdabotparams->get( 'jfooter' ) ) { ?>
<div class="small" align="center">
&copy; <?php echo mosCurrentDate('%Y'),' ',$mosConfig_sitename; ?><br />
<?php echo $_VERSION->URL; ?><br />
<a href="http://physicist.phpnet.us/">PDA Template</a>
</div>
<?php } ?>
</body></html><?php
if( $pdabotparams->get( 'removeimg' ) ||
	$pdabotparams->get( 'removeiframe' ) ||
	$pdabotparams->get( 'removeobject' ) ||
	$pdabotparams->get( 'removeembed' ) ||
	$pdabotparams->get( 'removeapplet' ) ||
	$pdabotparams->get( 'removescript' ) ||
	$pdabotparams->get( 'utf' ) )
{
	$text=ob_get_contents();
	ob_clean();
	if( $pdabotparams->get( 'removeimg' ) )
		$text = preg_replace( '|<img\s[^>]+>|is',       '', $text );
	if( $pdabotparams->get( 'removeiframe' ) ) {
		$text = preg_replace( '|<iframe\s[^>]+ />|is',  '', $text );
		$text = preg_replace( '|<iframe.+</iframe>|is', '', $text );
	}
	if( $pdabotparams->get( 'removeobject' ) ) {
		$text = preg_replace( '|<object\s[^>]+ />|is',  '', $text );
		$text = preg_replace( '|<object\s.+</object>|is','',$text );
	}
	if( $pdabotparams->get( 'removeembed' ) ) {
		$text = preg_replace( '|<embed\s[^>]+ />|is',   '', $text );
		$text = preg_replace( '|<embed.+</embed>|is',   '', $text );
	}
	if( $pdabotparams->get( 'removeapplet' ) ) {
		$text = preg_replace( '|<applet\s[^>]+ />|is',  '', $text );
		$text = preg_replace( '|<applet\s.+</applet>|is','',$text );
	}
	if( $pdabotparams->get( 'removescript' ) ) {
		$text = preg_replace( '|<script\s[^>]+ />|is',  '', $text );
		$text = preg_replace( '|<script\s.+</script>|is','',$text );
	}
	if( $pdabotparams->get( 'utf' ) && strcasecmp($charset_source,'utf-8') )
		$text = iconv( $charset_source, 'UTF-8', $text );
	echo $text;
}
?>
