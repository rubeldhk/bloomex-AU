<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

// Spamfix v2.3b8 / 02/28/06

# Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );
	$gbid = mosGetParam( $_GET, 'gbid');
	$md = mosGetParam( $_GET, 'md');
	if (($is_editor) AND ($gbid)or md5($eb_notify_email) == "$md" AND ($gbid)) {
# Don't allow passed settings
  	if (mosGetParam( $_REQUEST, 'is_editor')) {
    	print "<script>document.location.href='../../index.php'</script>\n";
    	exit();
  	}


  # Javascript for SmilieInsert and Form Check
    echo "<script type=\"text/javascript\">";
    echo "  function x () {";
    echo "    return;";
    echo "  }";
    echo "  function gb_smilie(thesmile) {";
    echo "    document.gbookForm.gbtext.value += \" \"+thesmile+\" \";";
    echo "    document.gbookForm.gbtext.focus();";
    echo "  }";

  echo "function validate(){ ";
  // Prüfe Name
echo "   if(document.gbookForm.gbname.value == '') {";

echo  "   alert(\""._GUESTBOOK_VALIDATE."\");";
echo  "   document.gbookForm.gbname.value();";
echo  "   return false;";

echo "  }";

 if ($eb_showmail){

 // Prüfe E-Mail Adresse

 echo "  if (document.gbookForm.gbmail.value.indexOf) {";
 echo "    var Adresse = document.gbookForm.gbmail.value;";
 echo "    var Ausdruck = /(.{2,})(@)(.+)(\.)(\w){2,4}/i;";
 echo "    var Ergebnis = document.gbookForm.gbmail.value.search(Ausdruck);";
 echo "       if(Ergebnis != 0) {";
 echo "            alert(\""._GUESTBOOK_VALIDATE2."\");";
 echo "          document.gbookForm.gbmail.focus();";
 echo "        return false;";
 echo "       }";
 echo "  }";
    }

  // Prüfe Text

echo "   if(document.gbookForm.gbtext.value == '') {";

echo  "   alert(\""._GUESTBOOK_VALIDATE3."\");";
echo  "   document.gbookForm.gbtext.value();";
	echo  "   return false;";

	echo "  }";
	$ip = getenv ("REMOTE_ADDR");
	echo "      document.gbookForm.action = 'index.php';";
 	echo "      document.gbookForm.submit();";
	echo "}";
	//#ENDE Modifikation
    if ($eb_bbcodesupport) {
      echo "  function DoPrompt(action) {";
      echo "    var revisedMessage;";
      echo "    var currentMessage = document.gbookForm.gbtext.value;";
      echo "    if (action == \"url\") {";
      echo "      var thisURL = prompt(\""._GUESTBOOK_BBCODEURL1."\", \"http://\");";
      echo "      var thisTitle = prompt(\""._GUESTBOOK_BBCODEURL2."\", \""._GUESTBOOK_BBCODEURL3."\");";
      echo "      var urlBBCode = \"[URL=\"+thisURL+\"]\"+thisTitle+\"[/URL]\";";
      echo "      revisedMessage = currentMessage+urlBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"email\") {";
      echo "      var thisEmail = prompt(\""._GUESTBOOK_BBCODEMAIL."\", \"\");";
      echo "      var emailBBCode = \"[EMAIL]\"+thisEmail+\"[/EMAIL]\";";
      echo "      revisedMessage = currentMessage+emailBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"bold\") {";
      echo "      var thisBold = prompt(\""._GUESTBOOK_BBCODEBOLD."\", \"\");";
      echo "      var boldBBCode = \"[B]\"+thisBold+\"[/B]\";";
      echo "      revisedMessage = currentMessage+boldBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"italic\") {";
      echo "      var thisItal = prompt(\""._GUESTBOOK_BBCODEITALIC."\", \"\");";
      echo "      var italBBCode = \"[I]\"+thisItal+\"[/I]\";";
      echo "      revisedMessage = currentMessage+italBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"underline\") {";
      echo "      var thisUndl = prompt(\""._GUESTBOOK_BBCODEUNDERLINE."\", \"\");";
      echo "      var undlBBCode = \"[U]\"+thisUndl+\"[/U]\";";
      echo "      revisedMessage = currentMessage+undlBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"quote\") {";
      echo "      var quoteBBCode = \"[QUOTE]  [/QUOTE]\";";
      echo "      revisedMessage = currentMessage+quoteBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"code\") {";
      echo "      var codeBBCode = \"[CODE]  [/CODE]\";";
      echo "      revisedMessage = currentMessage+codeBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"listopen\") {";
      echo "      var liststartBBCode = \"[LIST]\";";
      echo "      revisedMessage = currentMessage+liststartBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"listclose\") {";
      echo "      var listendBBCode = \"[/LIST]\";";
      echo "      revisedMessage = currentMessage+listendBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"listitem\") {";
      echo "      var thisItem = prompt(\""._GUESTBOOK_BBCODELIST."\", \"\");";
      echo "      var itemBBCode = \"[*]\"+thisItem;";
      echo "      revisedMessage = currentMessage+itemBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "    if (action == \"image\") {";
      echo "      var thisImage = prompt(\""._GUESTBOOK_BBCODEIMAGE."\", \"http://\");";
      echo "      var imageBBCode = \"[IMG]\"+thisImage+\"[/IMG]\";";
      echo "      revisedMessage = currentMessage+imageBBCode;";
      echo "      document.gbookForm.gbtext.value=revisedMessage;";
      echo "      document.gbookForm.gbtext.focus();";
      echo "      return;";
      echo "    }";
      echo "  }";
    }
    echo "</script>";


    echo "<form name='gbookForm' action='index.php' target='_top' method='post'>";
    echo "<input type='hidden' name='gbid' value='$gbid' />";
    $database->setQuery( "SELECT * FROM #__easybook WHERE gbid = $gbid" );
    $row = NULL;
    $database->loadObject( $row );
    echo "<input type='hidden' name='option' value='com_easybook' />";
    echo "<input type='hidden' name='Itemid' value='$Itemid' />";
    echo "<input type='hidden' name='func' value='editsave' />";
	echo "<input type='hidden' name='md' value='$md' />";
    echo "<input type='hidden' name='gbvote' value='$row->gbvote' />";

    echo "<table align='center' width='90%' cellpadding='0' cellspacing='4' border='0'>";
	echo "<tr><td width='130'>"._GUESTBOOK_IPADRESS." <span class='small'>*</span></td><td><input type='text' name='gbiip' style='width:245px;' class='inputbox' value='$row->gbip' disabled='disabled' /></td></tr>";
	echo "<tr><td width='130'>"._GUESTBOOK_ENTERNAME." <span class='small'>*</span></td><td><input type='text' name='gbname' style='width:245px;' class='inputbox' value='$row->gbname' /></td></tr>";

    if ($eb_showmail) echo "<tr><td width='130'>"._GUESTBOOK_ENTERMAIL." <span class='small'>*</span></td><td><input type='text' name='gbmail' style='width:245px;' class='inputbox' value='$row->gbmail' /></td></tr>";
	if ($eb_showhome) echo "<tr><td width='130'>"._GUESTBOOK_ENTERPAGE."</td><td><input type='text' name='gbpage' style='width:245px;' class='inputbox' value='$row->gbpage' /></td></tr>";
    if ($eb_showloca) echo "<tr><td width='130'>"._GUESTBOOK_ENTERLOCA."</td><td><input type='text' name='gbloca' style='width:245px;' class='inputbox' value='$row->gbloca' /></td></tr>";
    if ($eb_showicq)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERICQ."</td><td><input type='text' name='gbicq' style='width:245px;' class='inputbox' value='$row->gbicq' /></td></tr>";
    if ($eb_showaim)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERAIM."</td><td><input type='text' name='gbaim' style='width:245px;' class='inputbox' value='$row->gbaim' /></td></tr>";
    if ($eb_showmsn)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERMSN."</td><td><input type='text' name='gbmsn' style='width:245px;' class='inputbox' value='$row->gbmsn' /></td></tr>";
    if ($eb_showyah)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERYAH."</td><td><input type='text' name='gbyah' style='width:245px;' class='inputbox' value='$row->gbyah' /></td></tr>";
    if ($eb_showskype)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERSKYPE."</td><td><input type='text' name='gbskype' style='width:245px;' class='inputbox' value='$row->gbskype' /></td></tr>";





  # Switch for BB Code support
    if ($eb_bbcodesupport) {
      echo "<tr><td width='130'> </td><td>";
      if ($eb_linksupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"url\");'><img src='components/com_easybook/images/world_link.png' height='16' class='png' width='16' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONURL."' height='16' width='16' /></a>";
      if ($eb_mailsupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"email\");'><img src='components/com_easybook/images/email_link.png' height='16' class='png' width='16' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONMAIL."' height='16' width='16' /></a>";
      if ($eb_picsupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"image\");'><img src='components/com_easybook/images/picture_link.png' height='16' class='png' width='16' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONIMAGE."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"bold\");'><img src='components/com_easybook/images/text_bold.png' height='16' class='png' width='16' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONBOLD."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"italic\");'><img src='components/com_easybook/images/text_italic.png' height='16' class='png' width='16' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONITALIC."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"underline\");'><img src='components/com_easybook/images/text_underline.png' height='16' class='png' width='16' hspace='3' border='0' alt='' Title='"._GUESTBOOK_BBCODEBUTTONUNDERLINE."' height='16' width='16' /></a>";
      echo "</td></tr>";
    }

    echo "<tr><td width='130' valign='top'>"._GUESTBOOK_ENTERTEXT." <span class='small'>*</span><br /><br />";

  # Switch for Smilie Support
    if ($eb_smiliesupport) {
      $count=1;
      foreach ($smiley as $i=>$sm) {
        echo "<a href=\"javascript:gb_smilie('$i')\" title='$i'><img src='components/com_easybook/images/$sm' border='0' alt='$sm' /></a> ";
        if ($count%4==0) echo "<br />";
        $count++;
      }
    }

    echo "</td><td valign='top'><textarea style='width:245px;' rows='8' cols='50' name='gbtext' class='inputbox'>$row->gbtext</textarea></td></tr>";
    echo "<tr><td width='130'><input type='button' name='send' value='"._GUESTBOOK_SENDFORM."' class='button' onclick='validate()' /></td>";
    echo "<td align='right'><input type='reset' value='"._GUESTBOOK_CLEARFORM."' name='reset' class='button' /></td></tr></table></form>";
    echo "<center><span class='small'>* "._GUESTBOOK_REQUIREDFIELD."</span></center>";


  }

?>
