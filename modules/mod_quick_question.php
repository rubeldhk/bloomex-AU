<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_live_site;

$pretext = $params->get( 'pretext' );
$posttext = $params->get( 'posttext' );
$cols = $params->get( 'cols' );
$rows = $params->get( 'rows' );
$thanks = $params->get( 'thanks' );
$buttontext = $params->get( 'buttontext' );
$sendto = $params->get( 'sendto' );
$cssclass = $params->get( 'cssclass' );


echo "
<form action=\"". $mosConfig_live_site ."/modules/mod_quick_question_sendform.php\" method=\"post\" >
	<input type=hidden name=thanks value=\"". $thanks ."\">
	<input type=hidden name=sendto value=\"". $sendto ."\">
	<input type=hidden name=myself value=". $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'] .">".$pretext."
	<textarea class=\"". $cssclass ."\" cols=\"".$cols."\" rows=\"".$rows."\" name=\"input\" value=\"\"></textarea>
	".$posttext."
	<div style=\"text-align:right;\"><input style=\"font-size:9px\" type=submit value=\"". $buttontext ."\"></div>
</form>
";

?>