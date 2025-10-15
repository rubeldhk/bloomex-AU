<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory) (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

// Spamfix v2.3b8 / 02/28/06

# Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );

# Don't allow passed settings
  if (mosGetParam( $_REQUEST, 'is_editor')) {
    print "<script>document.location.href='../../index.php'</script>\n";
    exit();
  }

# Check if Registered Users only
  if (!$eb_anonentry AND !$is_user) {
    echo _GUESTBOOK_ONLYREGISTERED;
  } else {

  # Add custom Pathway
  $mainframe->appendPathWay(_GUESTBOOK_SIGN);


	mt_srand((double)microtime()*1000000);
	$codeid = mt_rand(100000,999999);


  # Javascript for SmilieInsert and Form Check
    echo "<script type=\"text/javascript\">";
    echo "  function x () {";
    echo "    return;";
    echo "  }";
    echo "  function gb_smilie(thesmile) {";
    echo "    document.gbookForm.gbtext.value += \" \"+thesmile+\" \";";
    echo "    document.gbookForm.gbtext.focus();";
    echo "  }";
   // Start Modifikation
echo "function validate(){ ";
  // Prüfe Name
echo "   if(document.gbookForm.gbname.value == '') {";

echo  "   alert(\""._GUESTBOOK_VALIDATE."\");";
echo  "   document.gbookForm.gbname.value();";
echo  "   return false;";

echo "  }";

 if ($eb_mailmandatory AND $eb_showmail) {

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


// Spamfix Reloadbutton

	if ($eb_spamfix == "1")
	{
	echo "function spamfixreload (id) {";
	echo "var a= Math.floor(Math.random()*1000);";
 	echo "var neuesbild = document.getElementById(id);";
 	echo "neuesbild.src = \"".$mosConfig_live_site."/components/com_easybook/img.php?CodeID=".$codeid."&reload=\"+a;}";
	}


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
	global $my;
	$gbname = $my->name;
    $gbmail = $my->email;
    echo "<input type='hidden' name='option' value='com_easybook' />";
    echo "<input type='hidden' name='Itemid' value='$Itemid' />";
    echo "<input type='hidden' name='func' value='entry' />";

    echo "<table align='center' width='90%' cellpadding='0' cellspacing='4' border='0' >";
	echo "<tr><td width='130'>"._GUESTBOOK_IPADRESS." <span class='small'>*</span></td><td><input type='text' name='gbiip' style='width:245px;' class='inputbox' value='$ip' disabled='disabled' /></td></tr>";
	echo "<tr><td width='130'>"._GUESTBOOK_ENTERNAME." <span class='small'>*</span></td><td><input type='text' name='gbname' style='width:245px;' class='inputbox' value='$gbname' /></td></tr>";
	if ($eb_showmail AND $eb_mailmandatory){
	$mandat = "*";
	}
	if ($eb_showmail) echo "<tr><td width='130'>"._GUESTBOOK_ENTERMAIL."<span class='small'> $mandat </span></td><td><input type='text' name='gbmail' style='width:245px;' class='inputbox' value='$gbmail' />";
			
		 //echo " <span class='small'>*</span></td></tr>";
	if ($eb_showmail) echo "<tr><td width='130'>"._GUESTBOOK_SHOWMAIL."</td><td><input type='checkbox' name='gbmailshow' class='inputbox' value='1' /></td></tr>";
    if ($eb_showhome) echo "<tr><td width='130'>"._GUESTBOOK_ENTERPAGE."</td><td><input type='text' name='gbpage' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showloca) echo "<tr><td width='130'>"._GUESTBOOK_ENTERLOCA."</td><td><input type='text' name='gbloca' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showicq)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERICQ."</td><td><input type='text' name='gbicq' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showaim)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERAIM."</td><td><input type='text' name='gbaim' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showmsn)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERMSN."</td><td><input type='text' name='gbmsn' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showyah)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERYAH."</td><td><input type='text' name='gbyah' style='width:245px;' class='inputbox' /></td></tr>";
    if ($eb_showskype)  echo "<tr><td width='130'>"._GUESTBOOK_ENTERSKYPE."</td><td><input type='text' name='gbskype' style='width:245px;' class='inputbox' /></td></tr>";

    if ($eb_showrating) {
      echo "<tr><td width='130'>"._GUESTBOOK_ENTERVOTE."</td>";
      echo "<td><select style='width:130px;' class='inputbox' size='1' name='gbvote'>";
	  echo "<option value='0'>"._GUESTBOOK_PLEASEVOTE."</option>";
      for ($i=1; $i<=$eb_maxvoting; $i++) {
        echo "<option";
        echo ">$i</option>";
      }
      echo "</select> ($eb_maxvoting - "._GUESTBOOK_VOTEGOOD.", 1 - "._GUESTBOOK_VOTEBAD.")</td></tr>";
    } else {
      $middlerate = floor($eb_maxvoting/2) + 1;
      echo "<input type='hidden' name='gbvote' value='$middlerate' />";
    }



  # Switch for BB Code support
    if ($eb_bbcodesupport) {
      echo "<tr><td width='130'> </td><td>";
      if ($eb_linksupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"url\");'><img src='components/com_easybook/images/world_link.png' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONURL."' height='16' width='16' /></a>";
      if ($eb_mailsupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"email\");'><img src='components/com_easybook/images/email_link.png' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONMAIL."' height='16' width='16' /></a>";
      if ($eb_picsupport) echo "<a href='javascript:%x()' onClick='DoPrompt(\"image\");'><img src='components/com_easybook/images/picture_link.png' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONIMAGE."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"bold\");'><img src='components/com_easybook/images/text_bold.png' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONBOLD."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"italic\");'><img src='components/com_easybook/images/text_italic.png' hspace='3' border='0' alt='' title='"._GUESTBOOK_BBCODEBUTTONITALIC."' height='16' width='16' /></a>";
      echo "<a href='javascript:%x()' onClick='DoPrompt(\"underline\");'><img src='components/com_easybook/images/text_underline.png' hspace='3' border='0' alt='' Title='"._GUESTBOOK_BBCODEBUTTONUNDERLINE."' height='16' width='16' /></a>";
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

    echo "</td><td valign='top'><textarea style='width:245px;' rows='8' cols='50' name='gbtext' class='inputbox'></textarea></td></tr>";

    # Spamfix
	if($eb_spamfix == 1)
	{
    echo "<tr><td colspan='2'>";
	echo "<table><tr>";
	echo "<td width='130'><input type='hidden' name='CodeID' value='".$codeid."' />"._GUESTBOOK_ENTERCODE."<span class='small'>*</span></td>";
	echo "<td><input type='text' name='gbcode' maxlength='5' style='width:60px;vertical-align:middle;' class='inputbox' title='"._GUESTBOOK_CODEDESCRIPTION."' /></td>";
	echo "<td rowspan='2'>&#160;&#160;<img src='./components/com_easybook/img.php?CodeID=".$codeid."' border='0' title='"._GUESTBOOK_CODEIMAGE."' alt='Code' style='vertical-align:top' id='code' width='178' height='50'/></td>";
	echo "</tr><tr>";
	echo "<td>"._GUESTBOOK_RELOADCODE."</td>";
	echo "<td><a href=\"javascript:spamfixreload('code')\"><img src='$mosConfig_live_site/components/com_easybook/images/reload.gif' title='"._GUESTBOOK_RELOADDESC."' alt='"._GUESTBOOK_RELOADDESC."' border='0'></a></td>";
	echo "</tr></table>";
	echo "</td></tr>";
	}
    echo "<tr><td width='130'><input type='button' name='send' value='"._GUESTBOOK_SENDFORM."' class='button' onclick='validate()' /></td>";
    echo "<td align='right'><input type='reset' value='"._GUESTBOOK_CLEARFORM."' name='reset' class='button' /></td></tr></table></form>";
    echo "<center><span class='small'>* "._GUESTBOOK_REQUIREDFIELD."</span></center>";



# Close RegUserOnly Check
  }

?>
