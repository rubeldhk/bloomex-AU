<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onStart', 'botPDA' );

function CheckAgent()
{
	$pdas = array(
	'acer',			'alcatel',		'audiovox',		'avantgo',		'blackberry',
	'blazer',		'cdm',			'digital paths','elaine',		'epoc',
	'ericsson',		'handspring',	'iemobile',		'kyocera',		'lg',
	'midp',			'mmp',			'mobile',		'motorola',		'nec',
	'nokia',		'o2',			'openwave',		'opera mini',	'operamini',
	'opwv',			'palm',			'panasonic',	'pda',			'phone',
	'playstation portable','pocket','psp',			'qci',			'sagem',
	'sanyo',		'samsung',		'sec',			'sendo',		'sharp',
	'smartphone',	'sonyericsson',	'symbian',		'telit',		'tsm',
	'up-browser',	'up.browser',	'up.link',		'vodafone',		'wap',
 	'windows ce',	'xiino'
	);

	$accept = isset($_SERVER['HTTP_ACCEPT'])?strtolower($_SERVER['HTTP_ACCEPT']):'';
	if(((strpos($accept,'text/vnd.wap.wml')>0)||
		(strpos($accept,'application/vnd.wap.xhtml+xml')>0))||
		isset($_SERVER['HTTP_X_WAP_PROFILE'])||
		isset($_SERVER['HTTP_PROFILE'])||
		isset($_SERVER['X-OperaMini-Features'])||
		isset($_SERVER['UA-pixels']) )
	return true;

	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($pdas as $browser)
			if(false!==strpos($agent,$browser))
				return true;
	}
	return false;
}

function botPDA()
{
	global $database,$mosConfig_live_site;

	$query = "SELECT params FROM #__mambots WHERE element = 'pdabot' AND folder = 'system'";
	$database->setQuery( $query );
	$database->loadObject( $mambot );
 	$botParams = new mosParameters( $mambot->params );

	$botParams->def( 'useragent', 1 );
	$botParams->def( 'subdomain', 1 );
	$botParams->def( 'subdomainname', 'pda' );
	$botParams->def( 'header1', 'header' );
	$botParams->def( 'header2', '' );
	$botParams->def( 'pathway', 1 );
	$botParams->def( 'middle1', '' );
	$botParams->def( 'middle2', '' );
	$botParams->def( 'footer1', 'footer' );
	$botParams->def( 'footer2', '' );
	$botParams->def( 'jfooter', 1 );
	$botParams->def( 'homepage', '' );
	$botParams->def( 'pathwayhome', 1 );
	$botParams->def( 'componentonhome', 1 );
	$botParams->def( 'head', 0 );
	$botParams->def( 'allowextedit', 0 );
	$botParams->def( 'removeimg', 0 );
	$botParams->def( 'removeiframe', 0 );
	$botParams->def( 'removeobject', 0 );
	$botParams->def( 'removeapplet', 0 );
	$botParams->def( 'removeembed', 0 );
	$botParams->def( 'removescript', 0 );
	$botParams->def( 'utf', 0 );
	if(!function_exists('iconv'))
		$botParams->set( 'utf', 0 );
	$botParams->def( 'pdatemplate', 'pda' );
	$botParams->def( 'embedcss', 0 );
	$botParams->def( 'content', 0 );
	$botParams->def( 'xmlhead', 1 );
	$botParams->def( 'xmlhtml', 1 );
	$botParams->def( 'doctype', 1 );
	$botParams->def( 'gzip', 0 );

	$GLOBALS['pdabotparams']=$botParams;
	$GLOBALS['pdabotversion']=212;
	$GLOBALS['ispda']=false;

	$parsed=parse_url($mosConfig_live_site);
	$path=isset($parsed['path'])?$parsed['path']:'';

	$subdomain=$botParams->get('subdomainname').'.';
	if( $botParams->get( 'subdomain' ) &&
		substr($_SERVER['HTTP_HOST'],0,strlen($subdomain))==$subdomain )
	{
		$mosConfig_live_site='http://'.$_SERVER['HTTP_HOST'].$path;
		$GLOBALS['ispda']=true;
	}

	if( $botParams->get( 'useragent' ) && CheckAgent() )
	{	
		$GLOBALS['ispda']=true;
	}
	
	if( $GLOBALS['ispda'] )
	{
		$_COOKIE['jos_user_template']=$botParams->get( 'pdatemplate' );
		$GLOBALS['mosConfig_gzip']=$botParams->get( 'gzip' );
		$homepage=$botParams->get( 'homepage' );
		if($homepage && ($_SERVER['REQUEST_URI']==$path.'/' || $_SERVER['REQUEST_URI']==$path.'/index.php') )
		{
			$GLOBALS['pdahome']=1;
			$_SERVER['REQUEST_URI']=$path.'/'.$homepage;
			if(substr($homepage,0,10)=='index.php?')
			{
				$_SERVER['QUERY_STRING']=substr($homepage,10);
				parse_str($_SERVER['QUERY_STRING'],$_REQUEST);
			}
		}
	}
}
?>