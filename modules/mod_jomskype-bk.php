<?php
/**
http://www.opensource.org/licenses/mit-license.php
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


if (!defined( '_jomskype' )) {
	/** ensure that functions are declared only once */
	define( '_jomskype', 1 );
	}

class	phpjomskype
	{
	var $skypeid;
	
	var $statusuri = "http://mystatus.skype.com/%s.xml";
	var $statusimguri = "http://mystatus.skype.com/%s/%s";
	
	var $str_status_xml = '';
	
	function phpjomskype($id = ""){
		if ($id != "") {
			$this->setSkypeID($id);
		}
	}
	
	/**
	 * set the skypeid for a user to check
	 */
	function setSkypeID($id){
		$this->skypeid = $id;
	}
	
	/**
	 * get status from skype mystatus server
	 */
	function _retrieveStatus(){
		$this->str_status_xml =  @file_get_contents(sprintf($this->statusuri,$this->skypeid));
	if (!$this->str_status_xml)
		{
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, sprintf($this->statusuri,$this->skypeid));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		$this->str_status_xml = $file_contents;
		}
	}
	
	/**
	 * returns the unprocessed xml/rdf data
	 */
	function getXML(){
		$this->_retrieveStatus();
		return $this->str_status_xml;
	}
	
	/**
	 * returns status text in specified language. defaults to english
	 * 
	 * English	en
	 * Deutsch	de
	 * Francais	fr
	 * italian	it
	 * polish	pl
	 * Japanese	ja
	 * Spanish	es
	 * Pt		pt
	 * Pt/br	pt-br
	 * Swedish	se
	 * zh		zh-cn
	 * Cn		zh-cn
	 * Zh/cn	zh-cn
	 * hk		zh-tw
	 * tw		zh-tw
	 * Zh/tw	zh-tw
	 */
	function getText($lang = "en"){
		$match = array();
		$this->_retrieveStatus();
		$pattern = "~xml:lang=\"".strtolower($lang)."\">(.*)</~";
		if ($this->str_status_xml)
			{
			preg_match($pattern,$this->str_status_xml, $match);
			return $match[1];
			}
		else
			{
			Echo "Unable to retrieve status of Skype user. Maybe URL file-access is disabled in the server configuration";
			return false;
			}
	}
	
	/**
	 * get the status number
	 * 
	 * 0	UNKNOWN		Not opted in or no data available.
	 * 1	OFFLINE			The user is Offline
	 * 2	ONLINE			The user is Online
	 * 3	AWAY			The user is Away
	 * 4	NOT AVAILABLE	The user is Not Available
	 * 5	DO NOT DISTURB	The user is Do Not Disturb (DND)
	 * 6	INVISIBLE		The user is Invisible or appears Offline
	 * 7	SKYPE ME		The user is in Skype Me mode
	 */
	function getNum(){
		$match = array();
		$this->_retrieveStatus();
		$pattern = "~xml:lang=\"NUM\">(\d)</~";
		
		preg_match($pattern,$this->str_status_xml, $match);
		return $match[1];
		}
	}

global $mosConfig_live_site;
	
$params->def( 'moduleclass_sfx', '' );
$params->def( 'skypeid', '' );
$params->def( 'showtext', 0 );
$params->def( 'lang', 0 );
$params->def( 'useskypesimages', 0 );
$params->def( 'type', 0 );
$params->def( 'showcallalways', 0 );
$params->def( 'showcall', 0 );
$params->def( 'showsendfile', 0 );
$params->def( 'showchat', 	0 );
$params->def( 'showadd', 0 );
$params->def( 'showprofile', 0 );
$params->def( 'showvoicemail', 0 );
$params->def( 'linktext_call', "Call" );
$params->def( 'linktext_chat', "Send a textmessage" );
$params->def( 'linktext_sendfile', 	"Send a file" );
$params->def( 'linktext_add', "Add to contact list" );
$params->def( 'linktext_profile', "Show Skype profile" );
$params->def( 'linktext_voicemail', "Leave a voicemail message" );
$params->def( 'useimages', 1 );
$params->def( 'colourscheme', 1 );
$params->def( 'usemyskypeimages', 0 );
$params->def( 'mystatusis', '' );
$params->def( 'myskypeimagesize', '96' );
$params->def( 'customimage', 'clock.jpg' );

$moduleclass_sfx 	= $params->get( 'moduleclass_sfx', '' );
$skypeid 			= $params->get( 'skypeid', '' );
$showtext			= $params->get( 'showtext', 1 );
$lang 				= $params->get( 'lang', 'en' );
$useskypesimages 	= $params->get( 'useskypesimages', '0' );
$type 				= $params->get( 'type', 'smallicon' );
$showcallalways		= $params->get( 'showcallalways', 1 );
$showcall			= $params->get( 'showcall', 1 );
$showsendfile 		= $params->get( 'showsendfile', 0 );
$showchat 			= $params->get( 'showchat', 0 );
$showadd 			= $params->get( 'showadd', 0 );
$showprofile 		= $params->get( 'showprofile', 0 );
$showvoicemail 		= $params->get( 'showvoicemail', 0 );

$linktext_call		= $params->get( 'linktext_call', 0);
$linktext_chat 		= $params->get( 'linktext_chat', 0 );
$linktext_sendfile 	= $params->get( 'linktext_sendfile', 0 );
$linktext_add 		= $params->get( 'linktext_add', 0 );
$linktext_profile 	= $params->get( 'linktext_profile', 0 );
$linktext_voicemail = $params->get( 'linktext_voicemail', 0 );
$useimages 			= $params->get( 'useimages', 0 );
$colourscheme		= $params->get( 'colourscheme', 0 );
$usemyskypeimages	= $params->get( 'usemyskypeimages', 0 );
$myskypeimage		= $params->get( 'myskypeimage', 0 );
$mystatusis			= $params->get( 'mystatusis', '' );
$myskypeimagesize	= $params->get( 'myskypeimagesize', '96' );
$customimage		= $params->get( 'customimage', 'clock.jpg' );

if ($colourscheme==1)
	$imagesArray[]=array("call"=>"call_green.png","chat"=>"chat_green.png","sendfile"=>"sendfile_green.png","add"=>"add_green.png","userinfo"=>"userinfo_green.png","voicemail"=>"voicemail_green.png");
else
	$imagesArray[]=array("call"=>"call_blue.png","chat"=>"chat_blue.png","sendfile"=>"sendfile_blue.png","add"=>"add_blue.png","userinfo"=>"userinfo_blue.png","voicemail"=>"voicemail_blue.png");

$oktocontinue=true;
if (empty($skypeid) )
	{
	$oktocontinue=false;
	echo "Error, Skype ID is empty. Have you entered it in your module configuration?";
	}

if ($oktocontinue)
	{
	?>
	<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js">
	</script>
	<?php
	// new status
	$status = new phpjomskype($skypeid);
	$statusText = $status->getText($lang);
	$statusNum =  $status->getNum();
	if ($showtext==1)
		echo $mystatusis.' '.$statusText."&nbsp;";
	if ($useskypesimages=="0")
		{
		switch ($statusNum) 
			{
			case 2 :
				// ONLINE
				$statusImage="status_online.png";
				break;
			case 7 :
				// SKYPE ME
				$statusImage="status_skypeme.png";
				break;
			case 3 :
				// AWAY
				$statusImage="status_away.png";
				break;
			case 4 :
				// NOT AVAILABLE
				$statusImage="status_notavailable.png";
				break;
			case 5 :
				// DO NOT DISTURB
				$statusImage="status_dnd.png";
				break;
			case 1 :
				// OFFLINE
				$statusImage="status_unknown.png";
				break;
			case 6 :
				// INVISIBLE
				$statusImage="status_unknown.png";
				break;
			default :
				$statusImage="status_unknown.png";
				break;
			}
		if ($showcallalways==1)
			echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="'.$mosConfig_live_site.'/modules/jomskype/'.$statusImage.'" border=0></a>';
		else 
			{
			if ($showcallalways!=2)
				{
				if ($status->getNum()==2 || $status->getNum()==7)
					echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="'.$mosConfig_live_site.'/modules/jomskype/'.$statusImage.'" border=0></a>';
				else
					echo '<img src="'.$mosConfig_live_site.'/modules/jomskype/'.$statusImage.'" border=0>';
				}
			}
		}
	else
		{
		if ($showcallalways==1)
			echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="http://mystatus.skype.com/'.$type.'/'.$skypeid.'" border=0></a>';
		else 
			{
			if ($showcallalways!=2)
				{
				if ($status->getNum()==2 || $status->getNum()==7)
					echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="http://mystatus.skype.com/'.$type.'/'.$skypeid.'" border=0></a>';
				else
					echo '<img src="http://mystatus.skype.com/'.$type.'/'.$skypeid.'" border=0>';
				}
			}
		}
	echo "<br/>";
	if ($usemyskypeimages!="0")
		{
		if ($myskypeimage =="custom") 
			echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="'.$mosConfig_live_site.'/images/stories/'.$customimage.'"  width="'.$myskypeimagesize.'" height="'.$myskypeimagesize.'"  border=0></a>';
		else
			echo '<a href="skype:'.$skypeid.'?call" onclick="return skypeCheck();"><img src="'.$mosConfig_live_site.'/modules/jomskype/myskype/'.$myskypeimage.'"  width="'.$myskypeimagesize.'" height="'.$myskypeimagesize.'"  border=0></a>';
		}
		
	$fields = array ();
	switch ($statusNum) 
		{
		case 0 :
			echo "Not detected. See <a href=\"http://www.skype.com/share/buttons/status.html\" target=\"_blank\">Info about SkypeWebStatus</a>";
			break;
		case 2 :
			// ONLINE
			$fields[] = array ("call", $linktext_call);
			$fields[] = array ("chat", $linktext_chat);
			$fields[] = array ("sendfile", $linktext_sendfile);	
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 7 :
			// SKYPE ME
			$fields[] = array ("call", $linktext_call);
			$fields[] = array ("chat", $linktext_chat);
			$fields[] = array ("sendfile", $linktext_sendfile);
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 3 :
			// AWAY
			$fields[] = array ("chat", $linktext_chat);
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 4 :
			// NOT AVAILABLE
			$fields[] = array ("chat", $linktext_chat);
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 5 :
			// DO NOT DISTURB
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 1 :
			// OFFLINE
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		case 6 :
			// INVISIBLE
			$fields[] = array ("add", $linktext_add);
			$fields[] = array ("userinfo", $linktext_profile);
			$fields[] = array ("voicemail", $linktext_voicemail);
			break;
		default :
			$fields = array ();
			break;
		}
	drawSelectBox($fields,$showcall,$showsendfile,$showchat,$showadd,$showprofile,$skypeid,$showvoicemail,$imagesArray,$useimages,$statusImage);
	}
// End module
	
	
/*
 * function to draw with special fields
 */
function drawSelectBox($fields = array (),$showcall,$showsendfile,$showchat,$showadd,$showprofile,$skypeid,$showvoicemail,$imagesArray,$useimages,$statusImage) {
	global $mosConfig_live_site;
	if (empty ($fields))
		return;
	foreach ($fields as $field) 
		{
		$show=false;
		if ($showcall==1 && $field[0]=="call")
			$show=true;
		if ($showchat==1 && $field[0]=="chat")
			$show=true;
		if ($showsendfile==1 && $field[0]=="sendfile")
			$show=true;
		if ($showadd==1 && $field[0]=="add")
			$show=true;
		if ($showprofile==1 && $field[0]=="userinfo")
			$show=true;
		if ($showvoicemail==1 && $field[0]=="voicemail")
			$show=true;
		if ($show==true)
			{
			$theField=$field[0];
			if ($useimages==1)
				echo '<a href="skype:'.$skypeid.'?'.$field[0].'" onclick="return skypeCheck();"><img src="'.$mosConfig_live_site.'/modules/jomskype/'.$imagesArray[0][$theField].'" border=0></a><br/>';
			else
				echo "<a href=\"skype:{$skypeid}?{$field[0]}\" onclick=\"return skypeCheck();\">{$field[1]}</a><br/>\n";
			}
		}
	return;
	}
?>