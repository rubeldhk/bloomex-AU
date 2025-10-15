<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

# Don't allow direct linking
defined( '_VALID_MOS' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );

# Don't allow passed settings
  if (mosGetParam( $_REQUEST, 'is_editor')) {
  print "<script>document.location.href='../../index.php'</script>\n";
  exit();
}


# Main Part of Subfunction
  if ($is_editor or md5($eb_notify_email) == "$md"){
    if ($submit) {
      $query1 = "DELETE FROM #__easybook WHERE gbid = $gbid";
      $database->setQuery( $query1 );
      $database->query();
      echo "<script> alert('"._GUESTBOOK_DELMESSAGE."'); document.location.href='index.php?option=com_easybook&Itemid=$Itemid';</script>";
    } else {
      $database->setQuery( "SELECT * FROM #__easybook WHERE gbid = $gbid" );
      $row = NULL;
      $database->loadObject( $row );
      #Show the Original Entry
      echo "<table width='100%' border='0' cellspacing='1' cellpadding='4'>";
      echo "<tr><td width='30%' height='20' class='sectiontableheader'>"._GUESTBOOK_NAME."</td>";
      echo "<td width='70%' height='20' class='sectiontableheader'>"._GUESTBOOK_ENTRY."</td></tr>";
      echo "<tr class='sectiontableentry1'><td width='30%' valign='top'><b>$row->gbname</b>";
      if ($row->gbloca<>"") echo "<br /><span class='small'>"._GUESTBOOK_FROM." $row->gbloca</span>";
      echo "</td>";
      $signtime = strftime("%c",$row->gbdate);
      $origtext = preg_replace("/(\015\012)|(\015)|(\012)/","&nbsp;<br />", $row->gbtext);
      foreach ($smiley as $i=>$sm) {
        $origtext = str_replace ("$i", "<img src='components/com_easybook/images/$sm' border='0' ALT='$i'>", $origtext);
      }
      echo "<td width='70%' valign='top'><span class='small'>"._GUESTBOOK_SIGNEDON." $signtime<hr></span>$origtext</td></tr>";
      echo "<tr class='sectiontableentry1'><td width='30%' valign='top'>";
      if ($row->gbmail<>"") echo "<a href='mailto:$row->gbmail'><img src='$mosConfig_live_site/components/com_easybook/images/email.png' height='16' width='16' class='png' alt='$row->gbmail' title='$row->gbmail' hspace='3' border='0' /></a>";
      if ($row->gbpage<>"") echo "<a href='$row->gbpage' target='_blank'><img src='components/com_easybook/images/world.png' height='16' width='16' class='png' alt='$row->gbpage' hspace='3' border='0'></a>";
      if ($row->gbicq<>"") echo "<a href='mailto:$row->gbicq@pager.icq.com'><img src='$mosConfig_live_site/components/com_easybook/images/im-icq.png' height='16' width='16' class='png' alt='$row->gbicq' hspace='3' border='0'></a>";
      if ($row->gbaim<>"") echo "<a href='aim:goim?screenname=$row->gbaim'><img src='$mosConfig_live_site/components/com_easybook/images/im-aim.png' height='16' width='16' class='png' alt='$row->gbaim' hspace='3' border='0'></a>";
      if ($row->gbmsn<>"") echo "<img src='$mosConfig_live_site/components/com_easybook/images/im-msn.png' height='16' width='16' class='png' alt='$row->gbmsn' hspace='3' border='0'></a>";
      if ($row->gbyah<>"") echo "<a href='ymsgr:sendIM?$row->gbyah'><img src='$mosConfig_live_site/components/com_easybook/images/im-yahoo.png' height='16' width='16' class='png' alt='$row->gbyah' hspace='3' border='0'></a>";
	  if ($row->gbskype<>"") echo "<a href='skype:" . $row->gbskype . "?call'><img src='$mosConfig_live_site/components/com_easybook/images/im-skype.png' height='16' width='16' class='png' alt='$row->gbskype' title='$row->gbskype' vspace='1' hspace='3' border='0' /></a>";
      echo "<img src='components/com_easybook/images/ip.gif' alt='$row->gbip' hspace='3' border='0'>";
      echo "</td><td width='70%' valign='top'>";
      if ($eb_showrating) {
        for($start=1;$start<=$eb_maxvoting;$start++) {
        $ratimg = $row->gbvote>=$start ? 'sun.png' : 'clouds.png';
        echo("<img src='$mosConfig_live_site/components/com_easybook/images/$ratimg' height='16' width='16' class='png'>");
        }
      }
      echo "</td></tr></table>";

      if (isset($md)){
      $md = "&md=".$md."";
	  }else{
	  $md = "";
	  }

      echo "<form method=POST action='index.php?option=com_easybook&Itemid=$Itemid&func=deleteentry&gbid=$row->gbid".$md."'>";
      echo "<input class='button' type='submit' name='submit' value='"._GUESTBOOK_DELENTRY."'></form>";
    }
  } else {
    echo "<p><a href='index.php?option=com_easybook&Itemid=$Itemid'>zurück</a>";
  }
?>