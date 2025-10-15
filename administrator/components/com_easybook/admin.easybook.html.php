<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

defined( '_VALID_MOS' ) or die( 'Direkter Zugriff ist nicht erlaubt.' );

class HTML_guestbook {

  function showGuestbookEntries( $option, $task, &$rows, &$search, &$pageNav ) {

    $entrylenght   = "70";
    $commentlenght = "40";

    # Table header
    ?>
    <form action="index2.php" method="post" name="adminForm">
    <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;" />
      </td>
      <td nowrap="nowrap"><?php echo _GUESTBOOK_ADMIN_DISPLAY ?> #</td>
      <td>
        <?php echo $pageNav->writeLimitBox(); ?>
      </td>
      <td><?php echo _GUESTBOOK_ADMIN_SEARCH ?></td>
      <td>
        <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
      </td>
    </tr>
    </table>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
      <tr>
        <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
        <th class="title"><div align="center"><?php echo _GUESTBOOK_ADMIN_AUTHOR ?></div></th>
        <th class="title"><div align="left"><?php echo _GUESTBOOK_ADMIN_MESSAGE ?></div></th>
        <th class="title"><div align="center"><?php echo _GUESTBOOK_ADMIN_DATE ?></div></th>
        <th class="title"><div align="center"><?php echo _GUESTBOOK_ADMIN_RATE ?></div></th>
        <th class="title"><div align="center"><?php echo _GUESTBOOK_ADMIN_COMMENT ?></div></th>
        <th class="title"><div align="center"><?php echo _GUESTBOOK_ADMIN_PUBLISHED ?></div></th>
      </tr>
      <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      $row = &$rows[$i];
      echo "<tr class='row$k'>";
      echo "<td width='5%'><input type='checkbox' id='cb$i' name='gbid[]' value='$row->gbid' onclick='isChecked(this.checked);' /></td>";
      $stime = strftime($row->gbdate);
      $signtime = strftime("%c",$stime);
      echo "<td align='center'><a href=\"index2.php?option=".$option."&task=edit&id=cb".$i."&gbid[]=".$row->gbid."\">$row->gbname</a></td>";
      if(strlen($row->gbtext) > $entrylenght) {
        $row->gbtext  = substr($row->gbtext,0,$entrylenght-3);
        $row->gbtext .= "...";
      }
      echo "<td align='left'>$row->gbtext</td>";
      echo "<td align='center'>$signtime</td>";
      echo "<td align='center'>$row->gbvote</td>";
      if(strlen($row->gbcomment) > $commentlenght) {
        $row->gbcomment  = substr($row->gbcomment,0,$commentlenght-3);
        $row->gbcomment .= "...";
      }
      echo "<td align='center'>";
      if ($row->gbcomment <> "") {
        echo "<img src='images/tick.png' alt='$row->gbcomment' title='$row->gbcomment' />";
      } else {
        echo "&nbsp;";
      }
      echo "</td>";

      $task = $row->published ? 'unpublish' : 'publish';
      $img = $row->published ? 'publish_g.png' : 'publish_x.png';
      ?>
        <td width="10%" align="center"><a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
    </tr>
    <?php    $k = 1 - $k; } ?>
    <tr>
      <th align="center" colspan="7">
        <?php echo $pageNav->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="7">
        <?php echo $pageNav->writePagesCounter(); ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="view" />
  <input type="hidden" name="boxchecked" value="0" />
  </form>
<?php
}

function editGuestbook( $option, &$row, &$publist ) {
  global $mosConfig_absolute_path;
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      // do field validation
      if (form.gbname.value == ""){
        alert( "Entry must have an author." );
      } else if (form.gbtext.value == ""){
        alert( "Entry should have some message." );
      } else {
        submitform( pressbutton );
      }
    }
    </script>

    <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;" />
      </td>
    </tr>
    </table>
    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
    <form action="index2.php" method="post" name="adminForm" id="adminForm">
      <tr>
        <th colspan="2" class="title" >
          <?php echo $row->gbid ? _GUESTBOOK_ADMIN_EDITENTRY : _GUESTBOOK_ADMIN_CREATEENTRY;?> <?php echo _GUESTBOOK_ADMIN_GUESTBOOKENTRY ?>
        </th>
      </tr>
      <tr>
        <td width="20%" align="right"><?php echo _GUESTBOOK_ADMIN_NAME ?>:</td>
        <td width="80%">
          <input class="inputbox" type="text" name="gbname" size="50" maxlength="100" value="<?php echo htmlspecialchars( $row->gbname, ENT_QUOTES );?>" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_DATE ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbdate" value="<?php echo strftime("%Y-%m-%d %H:%M:%S",$row->gbdate); ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_EMAIL ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbmail" value="<?php echo $row->gbmail; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_HOMEPAGE ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbpage" value="<?php echo $row->gbpage; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_LOCATION ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbloca" value="<?php echo $row->gbloca; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_ICQ ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbicq" value="<?php echo $row->gbicq; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_AIM ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbaim" value="<?php echo $row->gbaim; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_MSN ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbmsn" value="<?php echo $row->gbmsn; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_YAH ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbyah" value="<?php echo $row->gbyah; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_SKYPE ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbskype" value="<?php echo $row->gbskype; ?>" size="50" maxlength="100" />
        </td>
      </tr>
      <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_IP ?>:</td>
        <td>
          <input class="inputbox" type="text" name="gbip" value="<?php echo $row->gbip; ?>" size="50" maxlength="100" />
        </td>
      </tr>

      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_MESSAGE ?>:</td>
        <td>
          <textarea class="inputbox" cols="50" rows="6" name="gbtext" style="width=500px" width="500"><?php echo htmlspecialchars( $row->gbtext, ENT_QUOTES );?></textarea>
        </td>
      </tr>

      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_COMMENT ?>:</td>
        <td>
          <textarea class="inputbox" cols="50" rows="3" name="gbcomment" style="width=500px" width="500"><?php echo htmlspecialchars( $row->gbcomment, ENT_QUOTES );?></textarea>
        </td>
      </tr>

      <tr>
        <td valign="top" align="right"><?php echo _GUESTBOOK_ADMIN_PUBLISHED ?></td>
        <td>
          <?php echo $publist; ?>
        </td>
      </tr>

    </table>
    <input type="hidden" name="gbid" value="<?php echo $row->gbid; ?>" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="" />
    </form>
<?php
  }

function showAbout() {
?>
    <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;" />
      </td>
    </tr>
    <tr>
      <td>
        <?php echo _GUESTBOOK_ADMIN_CREDITS ?>
      </td>
    </tr>
    </table>
<?php
  }

function showLanguage($file, $option,$permission) {
  $file = stripslashes($file);
  $f=fopen($file,"r");
  $content = fread($f, filesize($file));
  $content = htmlspecialchars($content);
  ?>
  <form action="index2.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;" />
      </td>
    </tr>
  </table>
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
     <tr>
       <th colspan="4"><?php echo _GUESTBOOK_ADMIN_PATH ?>: <?php echo $file; ?></td> </tr>
     <tr>
       <td> <textarea cols="80" rows="20" name="filecontent"><?php echo $content; ?></textarea>
       </td>
     </tr>
     <tr>
       <td class="error"><?php if(!$permission) echo _GUESTBOOK_ADMIN_WRITEABLE_NOTICE ?></td>
     </tr>
  </table>
  <input type="hidden" name="file" value="<?php echo $file; ?>" />
  <input type="hidden" name="option" value="<?php echo $option; ?>">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  </form>

 <?php
}
 function showWords($file, $option,$permission) {
  $file = stripslashes($file);
  $f=fopen($file,"r");
  $content = fread($f, filesize($file));
  $content = htmlspecialchars($content);
  ?>
  <form action="index2.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;" />
      </td>
    </tr>
  </table>
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
     <tr>
       <th colspan="4"><?php echo _GUESTBOOK_ADMIN_PATH ?>: <?php echo $file; ?></td> </tr>
     <tr>
       <td> <textarea cols="80" rows="20" name="filecontent"><?php echo $content; ?></textarea>
       </td>
     </tr>
     <tr>
       <td class="error"><?php if(!$permission) echo _GUESTBOOK_ADMIN_WRITEABLE_NOTICE ?></td>
     </tr>
  </table>
  <input type="hidden" name="file" value="<?php echo $file; ?>" />
  <input type="hidden" name="option" value="<?php echo $option; ?>">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  </form>



 <?php
}
//end function showCss


# End of class
}
?>