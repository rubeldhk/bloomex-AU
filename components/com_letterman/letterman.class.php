<?php
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @author soeren
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
*  Table Class
*
* Provides access to the mos_letterman table
*/
class mosLetterman extends mosDBTable {
	/** @var int Unique id*/
	var $id=null;
	/** @var string */
	var $subject=null;
	/** @var string */
	var $headers=null;
	/** @var string */
	var $message=null;
	/** @var string */
	var $html_message=null;
	/** @var int */
	var $published=null;
	/** @var int */
	var $checked_out=null;
	/** @var datetime */
	var $checked_out_time=null;
	/** @var datetime */
	var $publish_up=null;
	/** @var datetime */
	var $publish_down=null;
	/** @var int */
	var $created=null;
    /** @var datetime */
	var $send=null;
    /** @var datetime */
	var $hits=null;
	/** @var int */
	var $access=null;
	/**
	* @param database A database connector object
	*/
	function __construct( &$database ) {
		global $mosConfig_absolute_path, $lm_params;
        parent::__construct( '#__letterman', 'id', $database );
		
		if( !isset($GLOBALS['lm_params']) || !empty($_REQUEST['lm_params'])) {
			// pull id of letterman component
			$query = "SELECT a.id FROM `#__components` AS a"
			. "\n WHERE `a`.`option` = 'com_letterman' AND `parent` = 0";
			$database->setQuery( $query );
			$letterman_id = $database->loadResult();
			
			// load Letterman parameters
			$component = new mosComponent( $database );
			$component->load( $letterman_id );
			$GLOBALS['lm_params'] = $lm_params = new mosParameters( $component->params );
			
			if( empty($lm_params->_params->newsletter_css)) {
				$database->setQuery('SELECT template FROM `#__templates_menu` WHERE client_id=0 ORDER BY menuid ASC LIMIT 0, 1');
				$cur_template = $database->loadResult();
				$template_css_file = $mosConfig_absolute_path."/templates/$cur_template/css/template_css.css";
				if( file_exists($template_css_file)) {
					
					$template_css = str_replace( "\r\n", "\n", file_get_contents( $template_css_file ));
					
					$txt = array();
					foreach ($lm_params->_params as $k=>$v) {
						$txt[] = "$k=$v";
					}
					$txt[] = "newsletter_css=".$template_css;
						
					$total = count( $txt );
					for( $i=0; $i < $total; $i++ ) {
						if ( strstr( $txt[$i], "\n" ) ) {
							$txt[$i] = str_replace( "\n", '<br />', $txt[$i] );
						}
					}
					
					$params = implode( "\n", $txt );
					
					$component->params = $params;
					$component->store();
				}
	
			}
		}
		
	}
	function check() {
		
		if( empty( $this->created ) ) {
			$this->created = date('Y-m-d H:i:s');
		}
		
		return true;
	}
}
/**
*  Table Class
*
* Provides access to the mos_letterman_subscribers table
*/
class mosLettermanSubscribers extends mosDBTable {
	/** @var int Unique id*/
	var $subscriber_id=null;
	/** @var string */
	var $subscriber_name=null;
	/** @var string */
	var $user_id=null;
	/** @var string */
	var $subscriber_email=null;
	/** @var int */
	var $confirmed=null;
	/** @var int */
	var $subscribe_date=null;
	/**
	* @param database A database connector object
	*/
	function __construct( &$database ) {
        parent::__construct( '#__letterman_subscribers', 'subscriber_id', $database );
	}
}
/**
 * Replaces all src attributes with a full URL to the live site
 * OR (when images are to be embedded) embeds the images into
 * the mail and changes the src attributes to a content id (=cid)
 * refrencing the encoded image in the mail body
 *
 * @param string $html_message
 * @param mosPHPMailer $mymail
 */
function lm_replaceImagesInMailBody( $html_message, &$mymail ) {
	global $lm_params, $mosConfig_absolute_path, $mosConfig_live_site;
	
	$embed_images = $lm_params->get('embed_images', 1);
	
	// Handle <img />Images and embed ALL images
	$images = array();
	if (preg_match_all("/<img[^>]*>/", $html_message, $images) > 0) {
		$i = 0;
		foreach ($images as $image) {
			if ( is_array( $image ) ) {
				foreach( $image as $src) {
					preg_match("'src=\"[^\"]*\"'si", $src, $matches);
					$source = str_replace ("src=\"", "", $matches[0]);

					$source = str_replace ("\"", "", $source);
					
					if( $embed_images ) {
						$filename = basename( $source );
						// must be a remote Image or somethin with ../../../image.gif then
						if (!stristr($source, $mosConfig_live_site)) {
	
							// must be a local image.
							// Attention! Now we guess it's located somewhere in the folder /images/ !!!
							if (!stristr($source, "http")) {
								// convert "media/mypicture.gif" to "/home/user/public_html/media/mypicture.gif"
								$tmp_source = "$mosConfig_absolute_path/$source";
								if( !file_exists($tmp_source)) {
									// IN /ADMINISTRATOR/ then!
									// convert "images/mypicture.gif" to "/home/user/public_html/administrator/images/mypicture.gif"					
									$tmp_source = "$mosConfig_absolute_path/administrator/$source";
									if( !file_exists($tmp_source)) {
										// leave the URL unchanged (we don't know where to find the image here!)
										continue;
									}
								}
								$source = $tmp_source;
							}
							else {
								// remote pictures are left unchanged
								continue;
							}
						}
						else {
							$source = str_replace( $mosConfig_live_site, $mosConfig_absolute_path, $source );
						}
						$pathinfo  = pathinfo( $filename );
						$cid = basename( $filename, ".".$pathinfo['extension'] );
						$size = @getimagesize( $source );
	
						switch($pathinfo['extension']) {
							case "jpg":
							case "jpeg":
							$mimetype = "image/jpeg"; break;
							case "png":
							$mimetype = "image/png"; break;
							case "gif":
							$mimetype = "image/gif"; break;
							case "swf":
							$mimetype = "image/swf"; break;
						}
						$mymail->AddEmbeddedImage( $source, $cid, $filename, "base64", $mimetype );
						$newtag = $size[3] ." src=\"cid:$cid\"";
						$html_message = str_replace( $matches[0], $newtag, $html_message );
					}
					else {
						if (!stristr($source, $mosConfig_live_site)) {
							if( substr($source, 0, 3) == '../') {
								$source = str_replace('../', '', $source);
							}
							// must be a remote Image or somethin with ../images/stories/image.jpg then
							$source = "$mosConfig_live_site/$source";
							$html_message = str_replace( $matches[0], "src=\"$source\"", $html_message );
						}
					}
				}
			}
		}
	}
	return $html_message;
}


/**
 * Sends out the mailing using mosPHPMailer
 *
 */
function lm_sendMail() {

	global $database, $my, $mosConfig_sitename, $mosConfig_live_site, $mosConfig_lang,
	$mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_fromname, $lm_params;
	if( !function_exists( "sefRelToAbs" )) {
		include_once( $mosConfig_absolute_path."/administrator/components/com_letterman/includes/sef.php" );
	}
	/*
	* because sending mail may take a long time, we want to disable timeout
	* unfortunately when you are running php in safe mode you cannot use set_time_limit(0)
	* that's why the disable-timout is optional.
	*/
	
	$mails_per_pageload  = intval( mosGetParam( $_POST, "mails_per_pageload", 100 ));
	$startfrom  = intval( mosGetParam( $_POST, "startfrom", 0 ));
	$option  = mosGetParam( $_POST, "option", 'com_letterman' );
	$disable_timeout  = mosGetParam( $_POST, "disable_timeout", '' );
	$id  = mosGetParam( $_POST, "id", '' );
	$sendto = mosGetParam( $_POST, "sendto", null );
	$mailfrom = mosGetParam( $_POST, "mailfrom", $mosConfig_mailfrom );
	$confirmed_accounts = mosGetParam( $_POST, "confirmed_accounts", "0" );
	$replyto = mosGetParam( $_POST, "replyto" );
	
	if ( $disable_timeout ) {
		@set_time_limit(0);
	}
	// Get default emailaddress
	$database->setQuery( "SELECT `email` FROM `#__users` WHERE usertype='superadministrator' LIMIT 0,1");
	$admin_email = $database->loadResult();
	echo $database->getErrorMsg();
	
	$mailfrom = $mailfrom ? $mailfrom : $admin_email;
	$replyto = $replyto ? $replyto : $admin_email;

	if ( $sendto===null) {
		mosRedirect( $_SERVER['PHP_SELF'].'?option=com_letterman&mosmsg='.LM_ERROR_NEWSLETTER_COULDNTBESENT );
	}
	// Get Itemid for Letterman
	$database->setQuery ( "SELECT `id` FROM `#__menu` WHERE link LIKE '%com_letterman%'" );
	$database->loadObject($myid);
	if( !empty($myid->id)) {
		$Itemid = $myid->id;
	}
	else {
		$Itemid = 1;
	}

	// Get newsletter
	$database->setQuery( "SELECT subject, message, html_message FROM `#__letterman` WHERE id='$id'");
	$database->loadObject( $newsletter );

	// Build e-mail message format
	$subject = $newsletter->subject;
	$message = $newsletter->message;
	$unsub_link = sefRelToAbs("index.php?option=$option&task=unsubscribe&Itemid=$Itemid");
	if( substr( $unsub_link, 0, 4 ) != "http" ) {
		$unsub_link = $mosConfig_live_site ."/". $unsub_link;
	}
	$unsub_link_html = "<a href=\"$unsub_link\">$unsub_link</a>";

	$footer_html = str_replace( "[UNLINK]", $unsub_link_html, LM_NEWSLETTER_FOOTER );
	$footer_html = str_replace( "[mosConfig_live_site]", "<a href=\"$mosConfig_live_site\">$mosConfig_live_site</a>", $footer_html );

	$footer_text = str_replace( "[UNLINK]", $unsub_link, LM_NEWSLETTER_FOOTER );
	$footer_text = str_replace( "[mosConfig_live_site]", $mosConfig_live_site, $footer_text );
	$footer_text = str_replace( "<br/>", "", $footer_text );
	$footer_text = str_replace( "<br />", "", $footer_text );
	
	// Prevent HTML tags in the text message
	$footer_text = strip_tags( $footer_text );

	$html_message = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
					  <html>
						  <head>
							  <title>$mosConfig_sitename :: $subject</title>
							  <base href=\"$mosConfig_live_site/\" />
							  <style type=\"text/css\">
							  ".strip_tags( $lm_params->get('newsletter_css'))."
							  </style>
						  </head>
						  <body>"
							. $newsletter->html_message
							. "<p>$footer_html</p>" 
							. '<p style="font-size:8px;color:grey;">'._CMN_EMAIL.': [EMAIL]</p>'
							. "</body>
					  </html>";
	
	$html_message = str_replace( "../../..", $mosConfig_live_site, $html_message );
	
	// Create the PHPMailer Object ( we need that NOW! )
	/** @var mosPHPMailer $mymail */
	$mymail = mosCreateMail( $mailfrom, $mosConfig_fromname, $subject, $html_message);
	
	// Patch to get correct Line Endings
	switch( substr( strtoupper( PHP_OS ), 0, 3 ) ) {
		case "WIN":
		$mail->LE = "\r\n";
		break;
		case "MAC": // fallthrough
		case "DAR": // Does PHP_OS return 'Macintosh' or 'Darwin' ?
		$mail->LE = "\r";
		default: // change nothing
		break;
	}
	
	$mymail->AddReplyTo( $replyto, $mosConfig_fromname );
	
	$html_message = lm_replaceImagesInMailBody( $html_message, $mymail );
	
	// Handle Attachments
	$regex = '#\[ATTACHMENT filename="(.*?)"\]#si';
	$attachments = array();
	if (preg_match_all($regex, $message, $attachments) > 0) {
		
		foreach ($attachments[1] as $idx => $attachment ) {
			$mymail->AddAttachment( $mosConfig_absolute_path.$attachment, basename($mosConfig_absolute_path.$attachment));
		}
	}
	$message = preg_replace( $regex, '', $message );
	
	// Get all users email and group
	if( $sendto == "subscribers" ) {
		$q = "SELECT subscriber_name AS name, subscriber_email AS email FROM #__letterman_subscribers";
		if( $confirmed_accounts == "1" ) {
			$q .= " WHERE confirmed='1'";
		}
	}
	// Currenly supported: VirtueMart and mambo-phpShop customers
	elseif( $sendto == 'customers') {
		if( file_exists($mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php')) {
			$shop = "virtuemart";
			// the configuration file for the Shop
			require_once( $mosConfig_absolute_path. "/administrator/components/com_$shop/$shop.cfg.php" );
			$q = 'SELECT user_email as email, CONCAT( first_name, \' \', last_name) as name FROM `#__{vm}_user_info` WHERE address_type=\'BT\'';
			
			// This is just needed for VirtueMart's table prefix vm
			require_once( CLASSPATH. 'ps_database.php' );
			$db = new ps_DB();
			$db->setQuery( $q );
			$q = $db->_sql;
		}
		elseif( file_exists($mosConfig_absolute_path.'/components/com_phpshop/phpshop_parser.php')) {
			$shop = 'phpshop';
			// the configuration file for the Shop
			require_once( $mosConfig_absolute_path. "/administrator/components/com_$shop/$shop.cfg.php" );
			$q = 'SELECT email, CONCAT( first_name, \' \',  last_name) as name FROM `#__users` WHERE address_type=\'BT\'';			
		}
	}
	//Marlar's CB integration
    elseif( substr($sendto,0,3) == 'cb:' ) {
        $sendto=substr($sendto, 3);
        $database->setQuery("SHOW COLUMNS FROM `#__comprofiler` LIKE '$sendto'");
        if(is_null($database->loadRow())){
              echo "<div style='font-size:15px; width:700px;border: 2px solid black; padding: 30px 10px; margin: 50px;'>I can't find the specified field '$sendto' in Community Builder to control email sending!<br><br>";
              echo "Please go to Community Builder -&gt; Field Management and create a Check Box (single) named <b>$sendto</b></div>";
              die;
        }
		$q = "SELECT CONCAT_WS(' ', firstname, middlename, lastname) AS name, email FROM `#__comprofiler` t1 INNER JOIN `#__users` t2 ON t1.user_id=t2.id WHERE $sendto=1";
		//Marlar's CB hack end
	}
	else {
		$query_appendix = ", #__core_acl_aro, #__core_acl_groups_aro_map WHERE #__core_acl_aro.value=#__users.id AND #__core_acl_groups_aro_map.aro_id = #__core_acl_aro.aro_id AND #__core_acl_groups_aro_map.group_id='$sendto'";
		$q = "SELECT #__users.name, email FROM #__users ";
		$q .= ($sendto !== '0') ? $query_appendix : "";
		
	}
	$database->setQuery( $q );
	$database->query();
	$all_rows = $database->getNumRows();
	
	echo $database->getErrorMsg();

	$q .= " LIMIT $startfrom, $mails_per_pageload";
	$database->setQuery( $q );
		
	// Now process all Recipients
	$i = 0;
	$errors = 0;
	
	// Send individual newsletters for each recipient
	$rows = $database->loadObjectList();
	foreach ($rows as $row) {
		if( empty($row->name) ) {
			$name = LM_SUBSCRIBER;
		}
		else {
			$name = $row->name;
		}
		// Now let's update the HTML Mail Body
		// embed the subscriber / user name
		$mymail->Body = str_replace( "[NAME]", $name, $html_message);
		
		// embed the email address this newsletter is sent to
		$mymail->Body = str_replace( "[EMAIL]", $row->email, $mymail->Body);
		
		// Set alternative Body with Text Message
		$mymail->AltBody = str_replace( "[NAME]", $name, $message . $footer_text );

		$mymail->ClearAddresses();
		$mymail->AddAddress( $row->email, $row->name );

		//Send email
		if( $mymail->Send()) {
			$i++;
		}
		else {
			$i++; // Added to prevent double mailings when errors occur
			$errors++;
		}
	}
	
	$msg = '';
	if( $errors > 0) {
		$msg = $mymail->ErrorInfo." =&gt; $errors Errors
";
	}
	$database->setQuery( "UPDATE `#__letterman` SET send=NOW() WHERE id=$id" );
	$database->query();
	$msg .= str_replace( "{X}", $i-$errors, LM_NEWSLETTER_SENDTO_X_USERS);
	if( $startfrom+$i >= $all_rows  ) {
		mosRedirect( $_SERVER['PHP_SELF']."?option=$option", $msg );
	}
	else {
		HTML_letterman::sendMailInfo( $all_rows, $startfrom+$mails_per_pageload, $msg );
	}

}

/**
 * Prints out a form to prepare the sending of a newsletter
 *
 * @param int $uid The Newsletter ID
 * @param string $option
 */
function lm_sendNewsletter ( $uid, $option ) {
	global $database, $my, $mosConfig_absolute_path, $mosConfig_lang;
	
	// Get default emailaddress
	$database->setQuery( "SELECT `email` FROM `#__users` WHERE usertype='superadministrator' OR gid=25 LIMIT 0,1");
	$row = $database->loadObjectList();
	$admin_email = $row[0]->email;

	$row = new mosLetterman( $database );
	// load the row from the db table
	$row->load( $uid );

	// get list of groups
	$groups = array( mosHTML::makeOption( "subscribers", '- All Subscribers -' ) );
	
	if( file_exists($mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php')) {
		$shop = "virtuemart";
	}
	elseif( file_exists($mosConfig_absolute_path.'/components/com_phpshop/phpshop_parser.php')) {
		$shop = 'phpshop';
	}
	else {
		$shop = '';
	}
	if( $shop != '') {
		// the configuration file for the Shop
		require_once( $mosConfig_absolute_path. "/administrator/components/com_$shop/$shop.cfg.php" );
		if( $shop == 'virtuemart') {
			// The abstract language class
			require_once( CLASSPATH."language.class.php" );
		}
		else {
			// load mosAbstractLanguage
    		require_once($mosConfig_absolute_path. '/administrator/components/com_phpshop/mos_4.6_code.php');
		}
		// load the Language File
		if (file_exists( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' )) {
			require_once( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' );
		}
		else {
			require_once( ADMINPATH. 'languages/english.php' );
		}
		$shop_lang = new phpShopLanguage();
		$groups[] = mosHTML::makeOption( '- - - - - - - -', '- - - - - - - -' );
		$groups[] = mosHTML::makeOption( "customers", '- '.$shop_lang->_PHPSHOP_STATISTIC_CUSTOMERS.' ('.$shop.') -' );
	}
	// CB integration
	if( file_exists($mosConfig_absolute_path.'/components/com_comprofiler/comprofiler.php')) {
	    $database->setQuery("SELECT name FROM `#__comprofiler_fields` where type = 'checkbox' and published=1");
	    $result = $database->loadResultArray();
	    if(is_array($result)) {
	    	$groups[] = mosHTML::makeOption( '- - - - - - - -', '- - - - - - - -' );
		    foreach($result as $r) {
		      	$groups = array_merge($groups, array(mosHTML::makeOption( "cb:$r", "- Community Builder, field $r -" )));
			}
	    }
	}
    
	$groups[] = mosHTML::makeOption( '- - - - - - - -', '- - - - - - - -' );
	$groups[] = mosHTML::makeOption( 0, '- All User Groups -' );
	$groups[] = mosHTML::makeOption( '- - - - - - - -', '- - - - - - - -' );
	$database->setQuery( "SELECT group_id AS value, name AS text FROM #__core_acl_aro_groups WHERE group_id<>17 AND group_id NOT in(28,29,30) ORDER BY group_id" );
	$groups = array_merge( $groups, $database->loadObjectList() );
	
	
	// build the html select list
	$grouplist = mosHTML::selectList( $groups, 'sendto', 'class="inputbox" size="1"',
	'value', 'text', '-1' );
	// manually disable options. This is MISSING IN mosHTML::selectList (core team, read this!!)
	$grouplist = str_replace( '"- - - - - - - -"', '"- - - - - - - -" disabled="disabled"', $grouplist);
	
	HTML_Letterman::sendNewsletter( $row, $option , $grouplist, $admin_email );
}

?>
