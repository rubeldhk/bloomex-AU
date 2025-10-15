<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on Akobook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

// Spamfix v2.4b3 / 02/28/06

# Don't allow direct linking
  defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

# Variables - Don't change anything here!!!
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
  $easyversion       = "1.1 Stable";

echo "<div id=\"easybook\">\n\n";

$html = "<link href=\"".$mosConfig_live_site."/components/com_easybook/images/datei.css\" rel=\"stylesheet\" type=\"text/css\"/>";

if(method_exists($mainframe, 'addCustomHeadTag')) {
    $mainframe->addCustomHeadTag($html);
} else {
        echo $html;
}


/** eMAIL-Cloaking with eMailIcon
* Added eMailCloaking
* by Christian Muenster
*/
/**
* simple Javascript Cloaking
* email cloacking
* by default replaces an email with a mailto link with email cloacked
*/
function emailCloaking1( $mail, $mailto=1, $pict='', $email=1 ) {
// convert text
$mail = mosHTML::encoding_converter( $mail );
// split email by @ symbol
$mail = explode( '@', $mail );
$mail_parts = explode( '.', $mail[1] );
// random number
$rand = rand( 1, 100000 );

$replacement = "\n<script language='JavaScript' type='text/javascript'> \n";
$replacement .= "<!-- \n";
$replacement .= "var prefix = 'ma' + 'il' + 'to'; \n";
$replacement .= "var path = 'hr' + 'ef' + '='; \n";
$replacement .= "var addy". $rand ." = '". @$mail[0] ."' + '@'; \n";
$replacement .= "addy". $rand ." = addy". $rand ." + '". implode( "' + '.' + '", $mail_parts ) ."'; \n";
$replacement .= "var pict". $rand ." = '" . $pict ."'; \n";
if ( $mailto ) {
// special handling when mail text is different from mail addy
$replacement .= "document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\' >' ); \n";
$replacement .= "document.write( '<img src=\'' + pict" . $rand ." + '\' alt=\''); \n";
$replacement .= "document.write( addy". $rand ." ); \n";
$replacement .= "document.write( '\' title=\''); \n";
$replacement .= "document.write( addy". $rand ." ); \n";
$replacement .= "document.write( '\' hspace=\'3\' height=\'16\' width=\'16\' class=\'png\' border=\'0\' />'); \n";
$replacement .= "document.write( '<\/a>' ); \n";
} else {
$replacement .= "document.write( addy". $rand ." ); \n";
}
$replacement .= "//--> \n";
$replacement .= "</script>";
$replacement .= "<noscript> \n";
$replacement .= _CLOAKING;
$replacement .= "\n</noscript>";

return $replacement;
}

# END EMAIlCloaking

# Functions of easybook
  function GuestbookHeader($viewlink) {
    global $Itemid, $database, $eb_allowentry, $mosConfig_lang, $mosConfig_mbf_content;
    $mname = new mosMenu( $database );
    $mname->load($Itemid);
    # Check for Joom!Fish and use translated Menuname instead
    $menuname = $mname->name;
    echo "<h2 class=\"componentheading\">$menuname</h2>\n\n";
	echo "<div style='padding-top: 10px;'>";
    if ($viewlink) {
      echo"<a class=\"view\" href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid")."'><b>"._GUESTBOOK_VIEW." <img src='components/com_easybook/images/book.png' height='16' width='16' class='png' style='vertical-align: middle;' border='0' alt='' title='' /></b></a>";
    } elseif ($eb_allowentry) {
      echo"<a class=\"sign\" href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=sign")."'><b>"._GUESTBOOK_SIGN." <img src='components/com_easybook/images/new.png' height='16' width='16' class='png' style='vertical-align: middle;' border='0' alt='' title='' /></b></a>";
    }
    echo "<br /><br />";
    return;
  }

  function easyparse($message, $eb_maxlength) {
    global $smiley, $eb_wordwrap , $eb_bbcodesupport, $eb_picsupport, $eb_smiliesupport, $eb_linksupport, $eb_mailsupport, $mosConfig_live_site;
    # Convert BB Code to HTML commands
    if ($eb_bbcodesupport) {
      $matchCount = preg_match_all("#\[code\](.*?)\[/code\]#si", $message, $matches);
      for ($i = 0; $i < $matchCount; $i++) {
        $currMatchTextBefore = preg_quote($matches[1][$i]);
        $currMatchTextAfter = htmlspecialchars($matches[1][$i]);
        $message = preg_replace("#\[code\]$currMatchTextBefore\[/code\]#si", "<b>Code:</b><hr />$currMatchTextAfter<hr />", $message);
      }
      $message = preg_replace("#\[quote\](.*?)\[/quote]#si", "<strong>Quote:</strong><hr /><blockquote>\\1</blockquote><hr />", $message);
      $message = preg_replace("#\[b\](.*?)\[/b\]#si", "<strong>\\1</strong>", $message);
      $message = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>", $message);
      $message = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $message);
      if ($eb_linksupport) $message = preg_replace("#\[url\](http://)?(.*?)\[/url\]#si", "<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $message);
      if ($eb_linksupport) $message = preg_replace("#\[url=(http://)?(.*?)\](.*?)\[/url\]#si", "<a href=\"http://\\2\" target=\"_blank\">\\3</a>", $message);
      if ($eb_mailsupport) $message = preg_replace("#\[email\](.*?)\[/email\]#si", "<a href=\"mailto:\\1\">\\1</a>", $message);
      if ($eb_picsupport) $message = preg_replace("#\[img\](.*?)\[/img\]#si", "<img src=\"\\1\" />", $message);
      $matchCount = preg_match_all("#\[list\](.*?)\[/list\]#si", $message, $matches);
      for ($i = 0; $i < $matchCount; $i++) {
        $currMatchTextBefore = preg_quote($matches[1][$i]);
        $currMatchTextAfter = preg_replace("#\[\*\]#si", "<LI>", $matches[1][$i]);
        $message = preg_replace("#\[list\]$currMatchTextBefore\[/list\]#si", "<UL>$currMatchTextAfter</UL>", $message);
      }
      $matchCount = preg_match_all("#\[list=([a1])\](.*?)\[/list\]#si", $message, $matches);
      for ($i = 0; $i < $matchCount; $i++) {
        $currMatchTextBefore = preg_quote($matches[2][$i]);
        $currMatchTextAfter = preg_replace("#\[\*\]#si", "<LI>", $matches[2][$i]);
        $message = preg_replace("#\[list=([a1])\]$currMatchTextBefore\[/list\]#si", "<OL TYPE=\\1>$currMatchTextAfter</OL>", $message);
      }
    }
    # Convert CR and LF to HTML BR command
    $message = preg_replace("/(\015\012)|(\015)|(\012)/","<br>", $message);

	# Einfügen des automatischen Zeilenumbruchs
	if($eb_wordwrap == 1)
	{
	$message = GuestbookMaxLength($message, $eb_maxlength);
	}
    # Convert smilies to images
    if ($eb_smiliesupport) {
      foreach ($smiley as $i=>$sm) {
        $message = str_replace ("$i", "<img src='$mosConfig_live_site/components/com_easybook/images/$sm' border='0' alt='$i' title='$i' />", $message);
      }
    }
    return $message;

  }

function GuestbookMaxLength($text, $size) {
	$words = explode(" ", $text);
	$anzahl = count($words);
	$neuertext = NULL;
	for ($i=0; $i<$anzahl; $i++)
	{
	 if (strlen($words[$i]) > $size)
	 {
	 $words[$i] = wordwrap($words[$i], $size, "\n",1);
	 }
	$neuertext = $neuertext . " " . $words[$i];
	}
	return $neuertext;
}

  function GuestbookFooter() {
    global $easyversion, $mosConfig_live_site;
    echo "<p id=\"easyfooter\" align=\"center\"><a href='http://www.easy-joomla.org/' target='_blank'><img src='".$mosConfig_live_site."/components/com_easybook/images/logo_sm.png' class='png' alt='EasyBook' border='0' width='138' height='26' /></a></p>";
    return;
  }

  function is_email($email){
    $rBool=false;
    if(preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email)) {
      $rBool=true;
    }
    return $rBool;
  }

  function textwrap($text, $width=75) {
   if ($text) return preg_replace("/([^\n\r ?&\.\/<>\"\\-]{".$width."})/i"," \\1\n",$text);
  }
#########################################################################################
# Modified by Thomas Mader for "Frontend Publish" - Beginn
function publish ( $gbid ) {
	global $database;
	$gbid = mysql_escape_string($gbid);
	$query = "SELECT * FROM #__easybook WHERE gbid ='$gbid';";
	$database->setQuery($query);
	$database->loadObject($gb);
	if ($gb->published == 0) {
		$publish = 1;
	} else {
		$publish = 0;
	}
	#Update
	$query = "UPDATE #__easybook SET published='$publish' WHERE gbid='$gbid';";
	$gbid = mysql_escape_string($gbid);
	$database->setQuery($query);
	$database->query();
}
# Modified by Thomas Mader for "Frontend Publish" - End
#########################################################################################
# Check if EasyBook is offline
  if ($eb_offline == 1) {
    require($mosConfig_absolute_path."/components/com_easybook/offline.php");
  } else {

    # Needed Variables - Don't Change!
    $smiley[':zzz']   = "sm_sleep.gif";    $smiley[':upset'] = "sm_upset.gif";
    $smiley[';)']     = "sm_wink.gif";     $smiley['8)']     = "sm_cool.gif";
    $smiley[':p']     = "sm_razz.gif";     $smiley[':roll']  = "sm_rolleyes.gif";
    $smiley[':eek']   = "sm_bigeek.gif";   $smiley[':grin']  = "sm_biggrin.gif";
    $smiley[':)']     = "sm_smile.gif";    $smiley[':sigh']  = "sm_sigh.gif";
    $smiley[':?']     = "sm_confused.gif"; $smiley[':cry']   = "sm_cry.gif";
    $smiley[':(']     = "sm_mad.gif";      $smiley[':x']     = "sm_dead.gif";

    # Get the right language if it exists
    if (file_exists($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php')) {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php');
    } else {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/english.php');
    }

    # Check for Editor rights
    $is_editor = (strtolower($my->usertype) == 'editor' || strtolower($my->usertype) == 'publisher' || strtolower($my->usertype) == 'manager' || strtolower($my->usertype) == 'administrator' || strtolower($my->usertype) == 'super administrator' );
    $is_user   = (strtolower($my->usertype) <> '');

	#Getting some Variables
	$func = mosGetParam( $_REQUEST, 'func', '');
	$gbid = mosGetParam( $_REQUEST, 'gbid');
	$md = mosGetParam( $_REQUEST, 'md');
    $gbtext	= mosGetParam( $_REQUEST, 'gbtext');
    $gbcomment = mosGetParam( $_REQUEST, 'gbcomment');
    $gbip = mosGetParam( $_REQUEST, 'gbip');
    $gbname = mosGetParam( $_REQUEST, 'gbname');
    $gbmail = mosGetParam( $_REQUEST, 'gbmail');
    $gbmailshow = mosGetParam( $_REQUEST, 'gbmailshow');
    $gbloca = mosGetParam( $_REQUEST, 'gbloca');
    $gbpage = mosGetParam( $_REQUEST, 'gbpage');
    $gbvote = mosGetParam( $_REQUEST, 'gbvote');
    $gbcode = mosGetParam( $_REQUEST, 'gbcode');
    $gbicq  = mosGetParam( $_REQUEST, 'gbicq');
    $gbaim  = mosGetParam( $_REQUEST, 'gbaim');
    $gbmsn  = mosGetParam( $_REQUEST, 'gbmsn');
    $gbskype  = mosGetParam( $_REQUEST, 'gbskype');
    $gbyah  = mosGetParam( $_REQUEST, 'gbyah');
    $published  = mosGetParam( $_REQUEST, 'published');
    $submit  = mosGetParam( $_REQUEST, 'submit');
    $startpage  = mosGetParam( $_REQUEST, 'startpage');
	$codeid = mosGetParam( $_REQUEST, 'CodeID', '');

    switch ($func) {
 #########################################################################################
      # Modified by Thomas Mader for "Frontend Publish" - Beginn
      case 'publish':
		if (($is_editor) AND ($gbid)or md5($eb_notify_email) == "$md" AND ($gbid)) {
		GuestbookHeader(true);
		$gbid = $database->getEscaped( $gbid );
      	publish( $gbid );
        echo "<script> document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
		}
		else
		{echo _GUESTBOOK_ACCESSDENIED;}
		break;
      # Modified by Thomas Mader for "Frontend Publish" - End
      #########################################################################################
      case 'deleteentry':
        GuestbookHeader(true);
        include('components/com_easybook/sub_deleteentry.php');
        break;
      #########################################################################################
      case 'comment':
        GuestbookHeader(true);
        include('components/com_easybook/sub_commententry.php');
        break;
      #########################################################################################
      case 'edit':
        GuestbookHeader(true);
	      echo "<br />";
		  echo "<br />";
        include('components/com_easybook/sub_editentry.php');
        break;
      #########################################################################################
      case 'editsave':
		if (($is_editor) AND ($gbid)or md5($eb_notify_email) == "$md" AND ($gbid)) {
            $query1 = "UPDATE #__easybook SET gbname='$gbname', gbmail='$gbmail', gbloca='$gbloca', gbpage='$gbpage', gbvote='$gbvote', gbtext='$gbtext', gbicq='$gbicq', gbaim='$gbaim', gbskype='$gbskype' , gbmsn='$gbmsn', gbyah='$gbyah' WHERE gbid=$gbid";
            $database->setQuery( $query1 );
            $database->query();
            echo "<script> alert('"._GUESTBOOK_SAVED."'); document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
          }
      #########################################################################################
      case 'entry':

        # Clear any HTML
        $gbtext = strip_tags($gbtext);
        $gbname = strip_tags($gbname);
        $gbmail = strip_tags($gbmail);
        $gbloca = strip_tags($gbloca);
        $gbpage = strip_tags($gbpage);
        $gbvote = strip_tags($gbvote);
        $gbicq  = strip_tags($gbicq);
        $gbaim  = strip_tags($gbaim);
        $gbmsn  = strip_tags($gbmsn);
        $gbskype  = strip_tags($gbskype);
        $gbyah  = strip_tags($gbyah);

        # Clear dangerous sql injections
        $gbid = mysql_escape_string($gbid);
        $gbname = mysql_escape_string($gbname);
        $gbmail = mysql_escape_string($gbmail);
        $gbloca = mysql_escape_string($gbloca);
        $gbpage = mysql_escape_string($gbpage);
        $gbvote = mysql_escape_string($gbvote);
        $gbtext = mysql_escape_string($gbtext);
        $gbicq  = mysql_escape_string($gbicq);
        $gbaim  = mysql_escape_string($gbaim);
        $gbmsn  = mysql_escape_string($gbmsn);
        $gbskype  = mysql_escape_string($gbskype);
        $gbyah  = mysql_escape_string($gbyah);
		$md5 = mysql_escape_string($md5);

        # Spamfix
        $query = "SELECT * FROM #__easybook_code WHERE CodeID='$codeid'";
	    $database->setQuery( $query );
    	$row = NULL;
    	$database->loadObject( $row );
        if((isset($row->CodeMD5) and ($row->CodeMD5 != "") and ($row->CodeMD5 == md5($gbcode))) || $eb_spamfix == "0") {

		#start badwords filter
            if($eb_wordfilter == "1")
		    {
		        require($mosConfig_absolute_path."/components/com_easybook/languages/wordfilter.php");
						$counter = 0;
						foreach ($words as $my_words)
						{
							$pattern ="/([a-zA-Z0-9])+".$my_words."([a-zA-Z0-9_-])+/";

							if(preg_match("/\b".$my_words."\b/i", $gbtext) === 1)
							{
								echo "<script> alert('". _GUESTBOOK_BADWORDFOUND . "$my_words');</script>";
								$badword = true;
							}

						}

						if ($badword == true)
						{
							echo "<script> document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
							break;
				    }
				}


	    	#end badwords filter

		# Email-Response Check

		if($eb_mailcheck == 1 AND $eb_showmail)
		{
		$maildomain = strstr($gbmail, "@");
		$maildomain = substr($maildomain,1);
		$host=$maildomain;
        $port="80";
        $numpings="1";
        $starttime=microtime();
        $socket=@fsockopen($host,$port);
        $endtime=microtime();
        if ($socket!=false)
            {
            fclose($socket);
               list($msec,$sec)=explode(" ",$starttime);
               $starttime=(float)$msec+(float)$sec;
               list($msec,$sec)=explode(" ",$endtime);
               $endtime=(float)$msec+(float)$sec;
               $pingtime=($endtime-$starttime)*1000;
                    }
                else
                    {
                        $pingtime=-1;
                    }

                if ($pingtime!=-1)
                    {
                    }
                else
                    {
						echo "<script> alert('" . _GUESTBOOK_BADMAILSERVER . "');</script>";
                        	 if($eb_allowentry) {
				  		     GuestbookHeader(true);
				 			 include('components/com_easybook/sub_writeentry.php');
				 			 break;
							}
                    }
           }

# Check if entry was edited by editor
        if (($is_editor) AND ($gbid)or md5($eb_notify_email) == "$md" AND ($gbid)) {
            $query1 = "UPDATE #__easybook SET gbname='$gbname', gbmail='$gbmail', gbloca='$gbloca', gbpage='$gbpage', gbvote='$gbvote', gbtext='$gbtext', gbicq='$gbicq', gbaim='$gbaim', gbskype='$gbskype' , gbmsn='$gbmsn', gbyah='$gbyah' WHERE gbid=$gbid";
            $database->setQuery( $query1 );
            $database->query();
            echo "<script> alert('"._GUESTBOOK_SAVED."'); document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
          } else {
            $gbdate = time();
            $gbip   = getenv('REMOTE_ADDR');
            $query2 = "INSERT INTO #__easybook SET gbname='$gbname',gbip='$gbip', gbdate='$gbdate', gbmail='$gbmail', gbmailshow='$gbmailshow', gbloca='$gbloca', gbpage='$gbpage', gbvote='$gbvote', gbtext='$gbtext', gbicq='$gbicq', gbaim='$gbaim', gbskype='$gbskype', gbmsn='$gbmsn', gbyah='$gbyah'";
            if ($eb_autopublish) {
              $query2 .= ",published='1'";
            }
            $database->setQuery( $query2 );
            $database->query();
            if ($eb_notify AND is_email($eb_notify_email) ) {
          $database->setQuery( "SELECT gbid FROM #__easybook WHERE gbdate=$gbdate" );
        $rowids = $database->loadObjectList();
        foreach ( $rowids as $rowid) {
            $gbmailtext = _GUESTBOOK_ADMINMAIL."\r\n\r\n"._GUESTBOOK_ADMIN_NAME.": ".$gbname."\r\n"._GUESTBOOK_ADMIN_MESSAGE.": ".$gbtext."\r\n\r\n"._GUESTBOOK_ADELETE.":\n".$mosConfig_live_site."/index.php?option=com_easybook&Itemid=".$Itemid."&func=deleteentry&gbid=".$rowid->gbid."&md=".md5($eb_notify_email)."\n"._GUESTBOOK_ACOMMENT.":\n".$mosConfig_live_site."/index.php?option=com_easybook&Itemid=".$Itemid."&func=comment&gbid=".$rowid->gbid."&md=".md5($eb_notify_email)."\n "._GUESTBOOK_AEDIT.":\n".$mosConfig_live_site."/index.php?option=com_easybook&Itemid=".$Itemid."&func=edit&gbid=".$rowid->gbid."&md=".md5($eb_notify_email)."\n "._GUESTBOOK_MAILNOTIFICATIONPUBLISH.":\n".$mosConfig_live_site."/index.php?option=com_easybook&Itemid=".$Itemid."&func=publish&gbid=".$rowid->gbid."&md=".md5($eb_notify_email)."\n\n\n "._GUESTBOOK_MAILFOOTER;
            mail($eb_notify_email,_GUESTBOOK_ADMINMAILHEADER,$gbmailtext,"From: ".$eb_notify_email);
        }
		  }
            if ($eb_thankuser AND is_email($gbmail) ) {
              $gbmailtext = _GUESTBOOK_USERMAIL."\r\n\r\n"._GUESTBOOK_ADMIN_NAME.": ".$gbname."\r\n"._GUESTBOOK_ADMIN_MESSAGE.": ".$gbtext."\r\n\r\n"._GUESTBOOK_MAILFOOTER;
              mail($gbmail,_GUESTBOOK_USERMAILHEADER,$gbmailtext,"From: ".$eb_notify_email);
            }
            echo "<script> alert('"._GUESTBOOK_SAVED."'); document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
          }
        # Spamfix
        }else {
          if(isset($gbcode) ) {
        	  echo "<SCRIPT> alert('"._GUESTBOOK_CODEWRONG."'); document.location.href='index.php?".SID."&option=com_easybook&func=sign&Itemid=$Itemid'</SCRIPT>";
        	}
        }
        break;
      #########################################################################################
      case 'sign':
        if ($eb_allowentry) {
          GuestbookHeader(true);
	      echo "<br />";
		  echo "<br />";
          include($mosConfig_absolute_path.'/components/com_easybook/sub_writeentry.php');
          break;
        }
      #########################################################################################
      default:
        GuestbookHeader(false);
        # Feststellen der Anzahl der verfügbaren Datensätze
        # Modified by Thomas Mader - Beginn
        if ($is_editor) {
        	$database->setQuery( "SELECT COUNT(gbid) AS amount FROM #__easybook" );
        } else {
        $database->setQuery( "SELECT COUNT(gbid) AS amount FROM #__easybook WHERE published='1'" );
        }
        # Modified by Thomas Mader - End
        $row = NULL;
        $database->loadObject( $row );
        $count = $row->amount;

        # Berechnen der Gesamtseiten
        $gesamtseiten = floor($count / $eb_perpage);
        $seitenrest   = $count % $eb_perpage;
        if ($seitenrest>0) {
         $gesamtseiten++;
        }
        # Feststellen der aktuellen Seite
        if (isset($startpage)) {
         if ($startpage>$gesamtseiten) {
           $startpage = $gesamtseiten;
         } else if ($startpage<1) {
           $startpage = 1;
         }
        } else {
         $startpage = 1;
        }

        $seiterueck = $startpage - 1;
        $seitevor = $startpage + 1;
        $start = ( $startpage - 1 ) * $eb_perpage;
        // Database Query
        $line=1;

        # Modified by Thomas Mader - Beginn
		if ($is_editor) {
			$database->setQuery( "SELECT * FROM #__easybook"
        	. "\nORDER BY gbdate $eb_sorting"
        	. "\nLIMIT $start,$eb_perpage"
        	);
		} else {
        $database->setQuery( "SELECT * FROM #__easybook"
        . "\nWHERE published = 1"
        . "\nORDER BY gbdate $eb_sorting"
        . "\nLIMIT $start,$eb_perpage"
        );
		}
		echo "<br />";
		# Modified by Thomas Mader - Beginn
        $rows = $database->loadObjectList();
        foreach ( $rows AS $row1) {
          $linecolor = ($line % 2) + 1;
          $row1->gbtext = stripslashes($row1->gbtext);
          $row1->gbname = stripslashes($row1->gbname);
          $row1->gbloca = stripslashes($row1->gbloca);
          $row1->gbname = textwrap($row1->gbname,20);
          $row1->gbloca = textwrap($row1->gbloca,30);
#Badwordfilter beim Abrufen der Einträge
          if($eb_wordfilterfront == "1")
		    {
		        require($mosConfig_absolute_path."/components/com_easybook/languages/wordfilter.php");
						$counter = 0;
						foreach ($words as $my_words)
						{
							$pattern ="/([a-zA-Z0-9])+".$my_words."([a-zA-Z0-9_-])+/";

							if(preg_match("/\b".$my_words."\b/i", $row1->gbtext) === 1)
							{
							    $query1 = "DELETE FROM #__easybook WHERE gbid = $row1->gbid";
     							$database->setQuery( $query1 );
      							$database->query();
								$bad_words .= $my_words;
								$bad_words .= ", ";
								$badword = true;
								$counter++;
							}

						}


				}

$signtime = strftime("%d %B %Y %H:%M",$row1->gbdate + ($mosConfig_offset*60*60));
$origtext = easyparse($row1->gbtext, $eb_maxlength);
$origtext = textwrap($origtext,80);

// Rahmen
echo "<div class='easy_frame' style='";if ($is_editor) {if ($row1->published == 0) {echo "background-color: #fffefd; border: #ffb39b solid 1px;";} }echo "'>
		<div class='easy_top' style='";if ($is_editor) {if ($row1->published == 0) {echo "background-color: #FFE7D7;";} }echo "'>
			<div class='easy_top_left'>
				<b class='easy_big'>$row1->gbname</b>
				<b class='easy_small'>&nbsp;&nbsp;&nbsp;";


				if ($row1->published == 1) echo "$signtime";
				if ($row1->published == 1 && $eb_showloca == 1){
				if ($row1->gbloca == "") {
					echo " | "._GUESTBOOK_NOLOCATION; }
				else {
					echo " | $row1->gbloca";
				}}

				if ($is_editor) {
					if ($row1->published == 0) {
						echo " | </b><b class='easy_small_red'>"._GUESTBOOK_ENTRYOFFLINE;
					}
				}
			echo "</b>
		  </div>
		  <div class='easy_top_right'>";

			//Voting
			if ($eb_showrating AND $row1->gbvote !== "0") {
				for($start=1;$start<=$eb_maxvoting;$start++) {
					$ratimg = $row1->gbvote>=$start ? 'sun.png' : 'clouds.png';
					echo("<img src='$mosConfig_live_site/components/com_easybook/images/$ratimg' class='easy_align_middle' height='16' width='16' alt='"._GUESTBOOK_ADMIN_RATE."' />");
					}
				if ($is_editor) echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
			}


			// Adminfunktionen
			if ($is_editor) {

				echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=edit&amp;gbid=$row1->gbid")."'><img border='0' src='$mosConfig_live_site/components/com_easybook/images/edit.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_AEDIT."' title='"._GUESTBOOK_AEDIT."' /></a>&nbsp;&nbsp;";

				echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=deleteentry&amp;gbid=$row1->gbid")."'><img border='0' src='$mosConfig_live_site/components/com_easybook/images/delete.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_ADELETE."' title='"._GUESTBOOK_ADELETE."' /></a>&nbsp;&nbsp;";

				if ($row1->gbcomment<>"") {
					echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=comment&amp;gbid=$row1->gbid")."'><img border='0' src='$mosConfig_live_site/components/com_easybook/images/comment_edit.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_ACOMMENT."' title='"._GUESTBOOK_ACOMMENT."' /></a>&nbsp;&nbsp;";
				}
				else {
					echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=comment&amp;gbid=$row1->gbid")."'><img border='0' src='$mosConfig_live_site/components/com_easybook/images/comment.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_ACOMMENT."' title='"._GUESTBOOK_ACOMMENT."' /></a>&nbsp;&nbsp;";
				}

				if ($row1->published == 0) {
					echo "<a href=\"".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=publish&amp;gbid=$row1->gbid")."\"><img border='0' src='$mosConfig_live_site/components/com_easybook/images/offline.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_PUBLISH."' title='"._GUESTBOOK_PUBLISH."' /></a>";
				}
				else {
					echo "<a href=\"".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;func=publish&amp;gbid=$row1->gbid")."\"><img border='0' src='$mosConfig_live_site/components/com_easybook/images/online.png' height='16' width='16' class='easy_align_middle' alt='"._GUESTBOOK_UNPUBLISH."' title='"._GUESTBOOK_UNPUBLISH."' /></a>";
				}

			}

			echo "</div><div style='clear: both;'></div></div>";

if($row1->gbpage && $eb_showhome || $row1->gbmailshow && $eb_showmail || $row1->gbicq && $eb_showicq || $row1->gbaim && $eb_showaim || $row1->gbmsn && $eb_showmsn || $row1->gbyah && $eb_showyah || $row1->gbskype && $eb_showskype) {
			echo "<div class='easy_contact'>";

if ($row1->gbmail<>"" AND $eb_showmail AND $row1->gbmailshow)
{
		  $pic= "$mosConfig_live_site\/components\/com_easybook\/images\/email.png";
		  echo emailCloaking1( $row1->gbmail,1,$pic,0);
}
if ($row1->gbpage<>"" AND $eb_showhome) {
if (substr($row1->gbpage,0,7)!="http://") $row1->gbpage="http://$row1->gbpage";
echo "<a href=\"$row1->gbpage\" target=\"_blank\"><img src='$mosConfig_live_site/components/com_easybook/images/world.png' height='16' width='16' alt='$row1->gbpage' class='png' title='$row1->gbpage' hspace='3' border='0' /></a>";}
if ($row1->gbicq<>"" AND $eb_showicq) echo "<a href=\"mailto:$row1->gbicq@pager.icq.com\"><img src='$mosConfig_live_site/components/com_easybook/images/im-icq.png' height='16' width='16' alt='$row1->gbicq'  class='png' title='$row1->gbicq' hspace='3' border='0' /></a>";
if ($row1->gbaim<>"" AND $eb_showaim) echo "<a href=\"aim:goim?screenname=$row1->gbaim\"><img src='$mosConfig_live_site/components/com_easybook/images/im-aim.png' alt='$row1->gbaim' class='png' height='16' width='16' title='$row1->gbaim' hspace='3' border='0' /></a>";
if ($row1->gbmsn<>"" AND $eb_showmsn) echo "<img src='$mosConfig_live_site/components/com_easybook/images/im-msn.png' alt='$row1->gbmsn' title='$row1->gbmsn'  hspace='3' class='png' height='16' width='16' border='0' />";
if ($row1->gbyah<>"" AND $eb_showyah) echo "<a href='ymsgr:sendIM?$row1->gbyah'><img src='$mosConfig_live_site/components/com_easybook/images/im-yahoo.png' height='16' width='16' alt='$row1->gbyah' title='$row1->gbyah' hspace='3' border='0' class='png' /></a>";
if ($row1->gbskype<>"" AND $eb_showskype) echo "<a href='skype:" . $row1->gbskype . "?call'><img src='$mosConfig_live_site/components/com_easybook/images/im-skype.png' height='16' width='16' alt='$row1->gbskype' class='png' title='$row1->gbskype' hspace='3' border='0' /></a>";


			echo "</div>";

}


			echo "<div class='easy_content'>$origtext</div>";


			if ($row1->gbcomment<>"") {
			$origcomment = easyparse($row1->gbcomment, $eb_maxlength);
				echo "<div class='easy_admincomment'><img border='0' src='$mosConfig_live_site/components/com_easybook/images/admin.png' height='16' width='16' class='easy_align_middle' style='padding-bottom: 2px;' alt='"._GUESTBOOK_ADMINSCOMMENT."' title='"._GUESTBOOK_ADMINSCOMMENT."' /> <b>"._GUESTBOOK_ADMINSCOMMENT.":</b><br />$origcomment</div>";
			}

			echo "</div><p class=\"clr\"></p>";



















/* Kontaktmöglichkeiten

*/






















 $line++;
if (isset($badword) && $badword == true)
{
echo "<script> document.location.href='index.php?option=com_easybook&amp;Itemid=$Itemid';</script>";
if($eb_wordfiltermail == 1)
{
$gbmailtext = _GUESTBOOK_BADWORDFRONTENDFOUND."\r\n\r\n"._GUESTBOOK_ADMIN_NAME.": ".$row1->gbname."\r\n"._GUESTBOOK_ADMIN_MESSAGE.": ".$row1->gbtext."\r\n"._GUESTBOOK_ADMIN_IP.": ".$row1->gbip."\r\n"._GUESTBOOK_BADWORDFRONTENDFOUNDWORD.": ".$bad_words."\r\n"._GUESTBOOK_MAILFOOTER;
mail($eb_notify_email,_GUESTBOOK_BADWORDFRONTENDFOUNDSUBJECT,$gbmailtext,"From: ".$eb_notify_email);
}
break;
}
}



# Berechnen der Gesamtseiten
        $gesamtseiten = floor($count / $eb_perpage);
        $seitenrest   = $count % $eb_perpage;
        if ($seitenrest>0) {
         $gesamtseiten++;
        }
        # Feststellen der aktuellen Seite
        if (isset($startpage)) {
         if ($startpage>$gesamtseiten) {
           $startpage = $gesamtseiten;
         } else if ($startpage<1) {
           $startpage = 1;
         }
        } else {
         $startpage = 1;
        }
        echo "<div align='center'><br /><b style='font-size: 18px;'>$count</b><br/>";
		if ($count == 1) {echo _GUESTBOOK_AFTERENTRIE;} else {
		echo _GUESTBOOK_AFTERENTRIES;}
		echo "</div>";

		if ( $gesamtseiten == 1) {} else {
		echo "<div align='center'>";
        # Ausgeben der Seite zurueck Funktion
        $seiterueck = $startpage - 1;
        if ($seiterueck>0) {
          echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;startpage=$seiterueck")."'><strong>&laquo;</strong></a> ";
          }
        #Ausgeben der einzelnen Seiten
         for ($i=1; $i <= $gesamtseiten; $i++) {
           if ($i==$startpage) {
             echo "$i ";
           } else {
             echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;startpage=$i")."'>$i</a> ";
           }
         }
        # Ausgeben der Seite vorwärts Funktion
        $seitevor = $startpage + 1;
        if ($seitevor<=$gesamtseiten) {
          echo "<a href='".sefRelToAbs("index.php?option=com_easybook&amp;Itemid=$Itemid&amp;startpage=$seitevor")."'><strong>&raquo;</strong></a> ";
          }
        # Limit und Seite Vor- & Rueckfunktionen
        $start = ( $startpage - 1 ) * $eb_perpage;
        echo "</div>";
        break;
		}
    }
	if($eb_footer == 1)
	{
    GuestbookFooter();
	}
# Close Offline Tag
  }

echo "</div></div>";
?>
