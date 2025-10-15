<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.2
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze. All rights reserved!
* @license http://www.konze.de/ Copyrighted Commercial Software
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_akoforms {

  function showAbout() {
  ?>
      <table cellpadding="4" cellspacing="0" border="0" width="100%">
      <tr>
        <td width="100%">
          <img src="components/com_akoforms/images/logo.png" />
        </td>
      </tr>
      <tr>
        <td>
          <p><img src="components/com_akoforms/images/akoforms_box.png" align="right"/><b>Program</b><br>
          AkoForms is a simple, but fully integrated email form generator component for Mambo
          Open Source. It currently supports the creation of multiple forms with multiple
          fields. All input will be email to a certain number of email adresses. If you have
          any wishes or have found a bug, please contact the author by mail:
          webmaster[at]mamboportal.com</p>
          <p><b>Author</b><br>
          Arthur Konze is one of the early eighties home computer hackers. He started with
          assembler coding on homecomputers like the Apple 2 and the Commodore C16. A few
          years later he get in touch with modem based computer networks like fido. He
          started with Internet in 1989 and concentrated on webdesign after the boom years.
          Currently he is the publisher of Mamboportal.com, which is one of the biggest
          MOS communities worldwide.</p>
          <p><b>License</b><br>
          AkoForms is a copyrighted work of Arthur Konze protected by the laws of the
          European Union. By installing and using AkoForms on your server, you agree to
          the following terms and conditions. Such agreement is either on your own behalf
          or on behalf of any corporate entity which employs you or which you represent.
          In this Agreement, 'you' includes both the reader and any Corporate Licensee and
          'Arthur Konze' means Arthur Konze Webdesign:</p>
          <ul>
            <li>AkoForms licence grants you the right to run one instance (a single
            installation) of the Software on one web server and one web site for each
            licence purchased. Each licence may power one instance of the Software on one
            domain. For each installed instance of the Software, a separate licence is
            required. Modifications to the software or database to circumvent the
            one-license-one-board rule are prohibited.</li>
            <li>The Software is licensed only to you. You may not rent, lease, sublicence,
            sell, assign, pledge, transfer or otherwise dispose of the Software in any
            form, on a temporary or permanent basis, without the prior written consent of
            Arthur Konze.</li>
            <li>If you have a valid licence, you may set up one additional installation on
            a non public server in order to test code, template and database modifications.</li>
            <li>The licence is effective until terminated. You may terminate it at any
            time by uninstalling the Software and destroying any copies in any form.</li>
            <li>The Software source code may be altered (at your risk). We do not support
            any modified version of AkoForms. All AkoForms copyright notices within the
            software and the code must remain unchanged (and visible).</li>
            <li>The Software may not be used for anything that would represent or is
            associated with an Intellectual Property violation, including, but not limited
            to, engaging in any activity that infringes or misappropriates the
            intellectual property rights of others, including copyrights, trademarks,
            service marks, trade secrets, software piracy, and patents held by individuals,
            corporations, or other entities.</li>
          </ul>
          <p>If any of the terms of this Agreement are violated, Arthur Konze reserves the
          right to revoke the licence at any time. Refunds will be given at the discretion
          of Arthur Konze.</p>
          <p><b>Disclaimer of Warranty</b><br>
          The software and the accompanying files are sold &quot;As Is&quot; and without warranties
          as to performance of merchantability or any other warrantied whether expressed
          or implied. Arthur Konze cannot be held responsible and accepts no liability for
          any failure in transmission by you and where for whatever reason your
          transmission is corrupted fails to arrive or arrives after an undue delay or is
          received in an unintelligible form. You must assume the entire risk of using the
          program. ANY LIABILITY OF ARTHUR KONZE WILL BE LIMITED EXCLUSIVELY TO PRODUCT
          REPLACEMENT OR REFUND OF PURCHASE PRICE.<br>
          <br>
          In no event will Arthur Konze be liable to you for any damages, including any
          lost profits, lost savings, loss of data or any indirect, special, incidental or
          consequential damages arising out of the use of or inability to use such
          Software, even if Arthur Konze has been advised of the possibility of such
          damages. Nothing in this Agreement limits liability for fraudulent
          misrepresentation.<br>
          <br>
          This licence gives you specific legal rights and the you may have other rights
          that vary from country to country. Some jurisdictions do not allow the exclusion
          of implied warranties, or certain kinds of limitations or exclusions of
          liability, so the above limitations and exclusions may not apply to you. Other
          jurisdictions allow limitations and exclusions subject to certain conditions. In
          such a case the above limitations and exclusions shall apply to the fullest
          extent permitted by the laws of such applicable jurisdictions. If any part of
          the above limitations or exclusions is held to be void of unenforceable, such
          part shall be deemed to be deleted from this agreement and the remainder of the
          limitation or exclusion shall continue in full force and effect. Any rights that
          you may have as a consumer (i.e. a purchaser for private as opposed to business,
          academic or government use) are not affected.</p>
        </td>
      </tr>
      </table>
  <?php
    }

############################################################################

function showFields( $option, &$rows, &$lists, &$pageNav ) {
  global $my, $mosConfig_absolute_path, $AKFLANG;
  require_once($mosConfig_absolute_path."/administrator/components/com_akoforms/fields.akoforms.php");
  ?>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
  <script language="Javascript" src="../includes/js/overlib_mini.js"></script>
  <form action="index2.php" method="post" name="adminForm">
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_FIELDMANAGER; ?></td>
      <td width="25%" align="right" valign="bottom"><?php echo $pageNav->writeLimitBox()." ".$lists['categories']; ?></td>
    </tr>
  </table>
  <table class="adminlist" width="100%">
    <tr>
      <th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
      <th width="30%" nowrap="nowrap"class="left"><?php echo $AKFLANG->AKF_FIELDTITLE; ?></th>
      <th width="30%" nowrap="nowrap"><?php echo $AKFLANG->AKF_FIELDTYPE; ?></th>
      <th nowrap="nowrap"><?php echo $AKFLANG->AKF_FIELDREQUIRED; ?></th>
      <th width="30%" nowrap="nowrap"><?php echo $AKFLANG->AKF_FORMTITLE; ?></th>
      <th nowrap="nowrap"><?php echo $AKFLANG->AKF_PUBLISHED; ?></th>
      <th colspan="2"><?php echo $AKFLANG->AKF_REORDER; ?></th>
    </tr>
    <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      $row = &$rows[$i];
      ?>
      <tr class="<?php echo "row$k"; ?>">
        <td width="20">
        <?php
          if ($row->checked_out && $row->checked_out != $my->id) {
            echo "&nbsp";
          } else { ?>
            <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
          <?php } ?>
        </td>
        <td class="left" width="50%"><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editfields')"><?php echo $row->title; ?></a></td>
        <td width="10%">
          <?php
            $fieldtemp = $formfields[$row->type];
            echo $fieldtemp[field_title];
          ?>
        </td>
        <?php
          $img1 = $row->required   ? 'tick.png' : 'publish_x.png';
        ?>
        <td align="center"><img src="images/<?php echo $img1;?>" width="12" height="12" border="0" alt="" /></td>
        <td width="25%"><?php echo $row->category; ?></td>
        <?php
          $now = date( "Y-m-d h:i:s" );
          if ($now <= $row->publish_up && $row->published == "1") {
            $img = 'publish_y.png';
          } else if (($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->published == "1") {
            $img = 'publish_g.png';
          } else if ($now > $row->publish_down && $row->published == "1") {
            $img = 'publish_r.png';
          } elseif ($row->published == "0") {
            $img = "publish_x.png";
          }
          $times = '';
          if (isset($row->publish_up)) {
            if ($row->publish_up == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Start: Always</td></tr>";
            } else {
              $times .= "<tr><td>Start: $row->publish_up</td></tr>";
            }
          }
          if (isset($row->publish_down)) {
            if ($row->publish_down == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Finish: No Expiry</td></tr>";
            } else {
              $times .= "<tr><td>Finish: $row->publish_down</td></tr>";
            }
          }
        ?>
        <td align="center"><a href="javascript: void(0);" onMouseOver="return overlib('<table border=0 width=100% height=100%><?php echo $times; ?></table>', CAPTION, 'Publish Information', BELOW, RIGHT);" onMouseOut="return nd();" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? "unpublishfields" : "publishfields";?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
        <td align="center">
        <?php
          if (($i > 0 || ($i+$pageNav->limitstart > 0)) && $row->catid == @$rows[$i-1]->catid) { ?>
            <a href="#reorder" onClick="return listItemTask('cb<?php echo $i;?>','orderfieldsup')"><img src="images/uparrow.png" width="12" height="12" border="0"></a>
          <?php } else {
            echo "&nbsp;";
          } ?>
        </td>
        <td align="center">
        <?php
          if (($i < $n-1 || $i+$pageNav->limitstart < $pageNav->total-1) && $row->catid == @$rows[$i+1]->catid) { ?>
            <a href="#reorder" onClick="return listItemTask('cb<?php echo $i;?>','orderfieldsdown')"><img src="images/downarrow.png" width="12" height="12" border="0"></a>
          <?php } else {
            echo "&nbsp;";
          } ?>
        </td>
      </tr>
      <?php
      $k = 1 - $k;
    } ?>
    <tr>
      <th align="center" colspan="10">
        <?php echo $pageNav->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="10">
        <?php echo $pageNav->writePagesCounter(); ?></td>
    </tr>
  </table>
  <br />
  <table cellpadding="15">
    <tr>
      <td><img align="absmiddle" src="images/publish_y.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBPENDING; ?></td>
      <td><img align="absmiddle" src="images/publish_g.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBCURRENT; ?></td>
      <td><img align="absmiddle" src="images/publish_r.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBEXPIRED; ?></td>
      <td><img align="absmiddle" src="images/publish_x.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_UNPUBLISHED; ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="fields" />
  <input type="hidden" name="boxchecked" value="0" />
  </form>
<?php
  }

function editFields( $option, &$row, &$lists ) {
  global $AKFLANG, $mosConfig_live_site;
?>
  <!-- Include dhtml calendar -->
  <link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
  <script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
  <script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>
  <!-- Include dhtml tabulators -->
  <script language="javascript" src="<?php echo $mosConfig_live_site."/administrator/js/dhtml.js"; ?>"></script>
  <!-- Check forms before submit -->
  <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'fields') {
        submitform( pressbutton );
        return;
      }

      // do field validation
      if (form.title.value == ""){
        alert( "Please enter a field title." );
      } else if (form.catid.value == "0"){
        alert( "Please select a form." );
      } else if (form.type.value == "0"){
        alert( "Pleace choose a field type." );
      } else {
        <?php
          getEditorContents( 'editor2', 'text') ;
        ?>
        submitform( pressbutton );
      }
    }
  </script>
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $row->id ? $AKFLANG->AKF_EDITFIELD : $AKFLANG->AKF_ADDFIELD ;?></td>
      <td width="25%">&nbsp</td>
    </tr>
  </table>
  <form action="index2.php" method="post" name="adminForm" id="adminForm">
  <?php
    $akofotabs = new mosTabs( 0 );
    $akofotabs->startPane( "akoformspane" );
    $akofotabs->startTab($AKFLANG->AKF_GENERAL,"page01");
  ?>
    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_FORM; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['forms']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPFORM; ?></span></td>
      </tr>
      <tr>
        <td width="120" align="right"><b><?php echo $AKFLANG->AKF_TITLE; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="title" style="width:300px" value="<?php echo htmlspecialchars( $row->title, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTITLE; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_DESCRIPTION; ?></b></td>
        <td width="420" valign="top"><?php editorArea( 'editor2',  $row->text , 'text', 400, 150, '10', '10' ); ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPDESCRIPTION; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_FIELDTYPE,"page02");
  ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right" valign="top"><b><?php echo $AKFLANG->AKF_TYPE; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['fieldtypes']; ?> </td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTYPE; ?></span></td>
      </tr>
      <tr>
        <td width="120" align="right" valign="top"><b><?php echo $AKFLANG->AKF_VALUE; ?></b></td>
        <td width="420" valign="top"><textarea class="inputbox" name="value" rows="6" style="width:300px"><?php echo htmlspecialchars( $row->value, ENT_QUOTES );?></textarea></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPVALUE; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_STYLE; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="style" style="width:300px" value="<?php echo htmlspecialchars( $row->style, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSTYLE; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_REQUIRED; ?></b></td>
        <td width="420" valign="top">
        <?php echo $lists['required']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPREQUIRED; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_PUBLISHING,"page03");
  ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_ORDERING; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['ordering']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPORDERING; ?></span></td>
      </tr>
      <tr>
        <td width="120" align="right"><b><?php echo $AKFLANG->AKF_STARTPUBLISHING; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="publish_up" id="publish_up" style="width:280px" value="<?php echo $row->publish_up; ?>" /> <input type="reset" class="button" value="..." onClick="return showCalendar('publish_up', 'y-mm-dd');"></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSTARTFINISH; ?></span></td>
      </tr>
      <tr>
        <td width="120" align="right"><b><?php echo $AKFLANG->AKF_FINISHPUBLISHING; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="publish_down" id="publish_down" style="width:280px" value="<?php echo $row->publish_down; ?>" /> <input type="reset" class="button" value="..." onClick="return showCalendar('publish_down', 'y-mm-dd');"></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSTARTFINISH; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->endPane();
  ?>
  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="" />
  </form>
  <p />
  <?php
}

############################################################################

  function showFile($file, $option) {
    global $AKFLANG;
    $file = stripslashes($file);
    $f=fopen($file,"r");
    $content = fread($f, filesize($file));
    $content = htmlspecialchars($content);
    ?>
    <form action="index2.php?" method="post" name="adminForm" class="adminForm" id="adminForm">
    <table class="blockheading" width="100%">
      <tr>
        <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
        <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_EDITLANGUAGE; ?></td>
        <td width="25%">&nbsp</td>
      </tr>
    </table>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
       <tr>
         <th colspan="4"><?php echo $AKFLANG->AKF_PATH." ".$file; ?></td> </tr>
       <tr>
         <td> <textarea cols="80" rows="20" name="filecontent" id="filecontent"><?php echo $content; ?></textarea>
         </td>
       </tr>
       <tr>
         <td class="error"><?php echo $AKFLANG->AKF_FILEWRITEABLE; ?></td>
       </tr>
    </table>
    <input type="hidden" name="file" value="<?php echo $file; ?>" />
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    </form>
    <?php
  }

############################################################################

function editConfig( $option, $row ) {
  global $AKFLANG, $mosConfig_live_site;
  # Compile a list of email charsets
  $isocode[]  = mosHTML::makeOption( 'utf-8', 'Standard - Western European' );
  $isocode[]  = mosHTML::makeOption( 'iso-8859-6', 'Arabic' );
  $isocode[]  = mosHTML::makeOption( 'gb2312', 'Chinese Simplified' );
  $isocode[]  = mosHTML::makeOption( 'big5', 'Chinese Traditional' );
  $isocode[]  = mosHTML::makeOption( 'iso-8859-5', 'Cyrillic' );
  $isocode[]  = mosHTML::makeOption( 'iso-8859-2', 'Eastern European' );
  $isocode[]  = mosHTML::makeOption( 'iso-8859-7', 'Greek' );
  $isocode[]  = mosHTML::makeOption( 'iso-8859-8', 'Hebrew' );
  $isocode[]  = mosHTML::makeOption( 'iso-2022-jp', 'Japanese' );
  $isocode[]  = mosHTML::makeOption( 'iso-2022-kr', 'Korean' );
  $afcharsets = mosHTML::selectList( $isocode, 'akf_mailcharset', 'class="inputbox" size="1" style="width:300px"', 'value', 'text', $row->akf_mailcharset );
  ?>
  <!-- Include dhtml tabulators -->
  <script language="javascript" src="<?php echo $mosConfig_live_site."/administrator/js/dhtml.js"; ?>"></script>
  <!-- Check forms before submit -->
  <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'fields') {
        submitform( pressbutton );
        return;
      }

      if (form.akf_mailemail.value == ""){
        alert( "Please enter a valid email address." );
      } else {
        submitform( pressbutton );
      }
    }
  </script>
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_EDITSETTINGS; ?></td>
      <td width="25%">&nbsp</td>
    </tr>
  </table>
  <form action="index2.php" method="POST" name="adminForm">
  <?php
    $akofotabs = new mosTabs( 0 );
    $akofotabs->startPane( "akoformspane" );
    $akofotabs->startTab($AKFLANG->AKF_EMAILSETTINGS,"page01");
  ?>
  <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_MAILSUBJECT; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailsubject" style="width:300px" value="<?php echo $row->akf_mailsubject;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSUBJECT; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_MAILCHARSET; ?></b></td>
      <td width="420" valign="top"><?php echo $afcharsets;?></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPCHARSET; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_SENDERNAME; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailsender" style="width:300px" value="<?php echo $row->akf_mailsender;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSENDER; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_SENDEREMAIL; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailemail" style="width:300px" value="<?php echo $row->akf_mailemail;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPEMAIL; ?></span></td>
    </tr>
    <tr>
      <td colspan="3"><hr /></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_EMAILTITLECSS; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailheadcss" style="width:300px" value="<?php echo $row->akf_mailheadcss;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPEMAILTITLECSS; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_EMAILROW1CSS; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailrow1css" style="width:300px" value="<?php echo $row->akf_mailrow1css;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPEMAILROW1CSS; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right"><b><?php echo $AKFLANG->AKF_EMAILROW2CSS; ?></b></td>
      <td width="420" valign="top"><input class="inputbox" type="text" name="akf_mailrow2css" style="width:300px" value="<?php echo $row->akf_mailrow2css;?>" /></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPEMAILROW2CSS; ?></span></td>
    </tr>
  </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_LAYOUTSETTINGS,"page02");
  ?>
  <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
    <tr>
      <td width="120" align="right" valign="top"><b><?php echo $AKFLANG->AKF_LAYOUTSTART; ?></b></td>
      <td width="420" valign="top"><textarea class='inputbox' name='akf_layoutstart' style="width:300px;height:70px;"><?php echo $row->akf_layoutstart;?></textarea></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPLAYOUTSTART; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right" valign="top"><b><?php echo $AKFLANG->AKF_LAYOUTROW; ?></b></td>
      <td width="420" valign="top"><textarea class='inputbox' name='akf_layoutrow' style="width:300px;height:70px;"><?php echo $row->akf_layoutrow;?></textarea></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPLAYOUTROW; ?></span></td>
    </tr>
    <tr>
      <td width="120" align="right" valign="top"><b><?php echo $AKFLANG->AKF_LAYOUTEND; ?></b></td>
      <td width="420" valign="top"><textarea class='inputbox' name='akf_layoutend' style="width:300px;height:70px;"><?php echo $row->akf_layoutend;?></textarea></td>
      <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPLAYOUTEND; ?></span></td>
    </tr>
  </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->endPane();
  ?>
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="savesettings" />
  </form>
  <p />
  <?php
}

############################################################################

function showForms( &$rows, &$pageNav, $option ) {
  global $AKFLANG;
  ?>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
  <script language="Javascript" src="../includes/js/overlib_mini.js"></script>
  <form action="index2.php" method="post" name="adminForm">
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_FORMMANAGER; ?></td>
      <td width="25%" align="right" valign="bottom"><?php echo $pageNav->writeLimitBox(); ?></td>
    </tr>
  </table>
  <table width="100%" class="adminlist">
    <tr>
      <th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
      <th align="left" nowrap><?php echo $AKFLANG->AKF_FORMTITLE; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_NUMBEROFFIELDS; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_SENDMAIL; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_STOREDB; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_SHOWRESULT; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_FINISHING; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_PUBLISHED; ?></th>
      <th align="center" nowrap colspan="2"><?php echo $AKFLANG->AKF_REORDER; ?></th>
    </tr>
    <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      $row = &$rows[$i];
      ?>
      <tr class="<?php echo "row$k"; ?>">
        <td width="20"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);" /></td>
        <td><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $row->title; ?></a><br />
        <font style="font-size: xx-small;color: #369;">{akoforms=<?php echo $row->id; ?>}</font></td>
        <td align="center"><?php echo $row->nofields; ?></td>
        <?php
          $img1 = $row->sendmail   ? 'tick.png' : 'publish_x.png';
          $img2 = $row->savedb     ? 'tick.png' : 'publish_x.png';
          $img3 = $row->showresult ? 'tick.png' : 'publish_x.png';
        ?>
        <td align="center"><img src="images/<?php echo $img1;?>" width="12" height="12" border="0" alt="" /></td>
        <td align="center"><img src="images/<?php echo $img2;?>" width="12" height="12" border="0" alt="" /></td>
        <td align="center"><img src="images/<?php echo $img3;?>" width="12" height="12" border="0" alt="" /></td>
        <td align="center"><?php echo $row->target ? $AKFLANG->AKF_REDIRECTION : $AKFLANG->AKF_FORMPAGE; ?></td>
        <?php
          $now = date( "Y-m-d h:i:s" );
          if ($now <= $row->publish_up && $row->published == "1") {
            $img = 'publish_y.png';
          } else if (($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->published == "1") {
            $img = 'publish_g.png';
          } else if ($now > $row->publish_down && $row->published == "1") {
            $img = 'publish_r.png';
          } elseif ($row->published == "0") {
            $img = "publish_x.png";
          }
          $times = '';
          if (isset($row->publish_up)) {
            if ($row->publish_up == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Start: Always</td></tr>";
            } else {
              $times .= "<tr><td>Start: $row->publish_up</td></tr>";
            }
          }
          if (isset($row->publish_down)) {
            if ($row->publish_down == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Finish: No Expiry</td></tr>";
            } else {
              $times .= "<tr><td>Finish: $row->publish_down</td></tr>";
            }
          }
        ?>
        <td align="center"><a href="javascript: void(0);" onMouseOver="return overlib('<table border=0 width=100% height=100%><?php echo $times; ?></table>', CAPTION, 'Publish Information', BELOW, RIGHT);" onMouseOut="return nd();" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? "unpublish" : "publish";?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
        <td align="center">
          <?php
          if ($i > 0 || ($i+$pageNav->limitstart > 0)) { ?>
            <a href="#reorder" onClick="return listItemTask('cb<?php echo $i;?>','orderup')">
            <img src="images/uparrow.png" width="12" height="12" border="0">
            </a>
          <?php } else {
            echo "&nbsp;";
          } ?>
        </td>
        <td align="center">
          <?php
          if ($i < $n-1 || $i+$pageNav->limitstart < $pageNav->total-1) { ?>
            <a href="#reorder" onClick="return listItemTask('cb<?php echo $i;?>','orderdown')">
            <img src="images/downarrow.png" width="12" height="12" border="0">
            </a>
          <?php } else {
            echo "&nbsp;";
          } ?>
        </td>
      </tr>
      <?php
      $k = 1 - $k;
    }
    ?>
    <tr>
      <th align="center" colspan="10"><?php echo $pageNav->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="10"><?php echo $pageNav->writePagesCounter(); ?></td>
    </tr>
  </table>
  <br />
  <table cellpadding="15">
    <tr>
      <td><img align="absmiddle" src="images/publish_y.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBPENDING; ?></td>
      <td><img align="absmiddle" src="images/publish_g.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBCURRENT; ?></td>
      <td><img align="absmiddle" src="images/publish_r.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBEXPIRED; ?></td>
      <td><img align="absmiddle" src="images/publish_x.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_UNPUBLISHED; ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option; ?>">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  </form>
  <?php
}

function editForms( $option, &$row, &$lists ) {
  global $AKFLANG, $mosConfig_live_site;
?>
  <!-- Include dhtml calendar -->
  <link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
  <script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
  <script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>
  <!-- Include dhtml tabulators -->
  <script language="javascript" src="<?php echo $mosConfig_live_site."/administrator/js/dhtml.js"; ?>"></script>
  <!-- Check forms before submit -->
  <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }

      // do field validation
      if (form.title.value == ""){
        alert( "Please enter a form title." );
      } else {
        <?php
          getEditorContents( 'editor1', 'text') ;
          getEditorContents( 'editor2', 'thanktext') ;
        ?>
        submitform( pressbutton );
      }
    }
  </script>
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $row->id ? $AKFLANG->AKF_EDITFORM : $AKFLANG->AKF_ADDFORM ;?></td>
      <td width="25%">&nbsp</td>
    </tr>
  </table>
  <form action="index2.php" method="post" name="adminForm" id="adminForm">
  <?php
    $akofotabs = new mosTabs( 0 );
    $akofotabs->startPane( "akoformspane" );
    $akofotabs->startTab($AKFLANG->AKF_HEADER,"page01");
  ?>
    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_TITLE; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="title" style="width:300px" value="<?php echo htmlspecialchars( $row->title, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTITLE; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_DESCRIPTION; ?></b></td>
        <td width="420" valign="top"><?php editorArea( 'editor1',  $row->text , 'text', 400, 150, '10', '10' ); ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPDESCRIPTION; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_HANDLING,"page02");
  ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_SENDBYEMAIL; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['sendmail']; ?> </td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSENDMAIL; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_EMAILS; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="emails" style="width:300px" value="<?php echo htmlspecialchars( $row->emails, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPEMAILS; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_SAVETODATABASE; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['savedb']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSAVEDB; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_SHOWFORMRESULT; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['showresult']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPRESULT; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_PUBLISHING,"page03");
  ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_ORDERING; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['ordering']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPORDERING; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_STARTPUBLISHING; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="publish_up" id="publish_up" style="width:280px" value="<?php echo $row->publish_up; ?>" /> <input type="reset" class="button" value="..." onClick="return showCalendar('publish_up', 'y-mm-dd');"></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSTARTFINISH; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_FINISHPUBLISHING; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="publish_down" id="publish_down" style="width:280px" value="<?php echo $row->publish_down; ?>" /> <input type="reset" class="button" value="..." onClick="return showCalendar('publish_down', 'y-mm-dd');"></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPSTARTFINISH; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->startTab($AKFLANG->AKF_FINISHING,"page04");
  ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_ENDPAGETITLE; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="thanktitle" style="width:300px" value="<?php echo htmlspecialchars( $row->thanktitle, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTITLE; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_ENDPAGEDESCRIPTION; ?></b></td>
        <td width="420" valign="top"><?php editorArea( 'editor2',  $row->thanktext , 'thanktext', 400, 150, '10', '10' ); ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPDESCRIPTION; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_FORMTARGET; ?></b></td>
        <td width="420" valign="top"><?php echo $lists['target']; ?></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTARGET; ?></span></td>
      </tr>
      <tr>
        <td width="120" valign="top" align="right"><b><?php echo $AKFLANG->AKF_TARGETURL; ?></b></td>
        <td width="420" valign="top"><input class="inputbox" type="text" name="targeturl" style="width:300px" value="<?php echo htmlspecialchars( $row->targeturl, ENT_QUOTES );?>" /></td>
        <td valign="top"><span class="small"><?php echo $AKFLANG->AKF_HELPTARGETURL; ?></span></td>
      </tr>
    </table>
  <?php
    $akofotabs->endTab();
    $akofotabs->endPane();
  ?>
  <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="task" value="" />
  </form>
  <p />
  <?php
}

############################################################################

function showDataForms( &$rows, &$pageNav, $option ) {
  global $AKFLANG;
  ?>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
  <script language="Javascript" src="../includes/js/overlib_mini.js"></script>
  <form action="index2.php" method="post" name="adminForm">
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_STOREDFORMS; ?></td>
      <td width="25%" align="right" valign="bottom"><?php echo $pageNav->writeLimitBox(); ?></td>
    </tr>
  </table>
  <table width="100%" class="adminlist">
    <tr>
      <th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
      <th align="left" nowrap><?php echo $AKFLANG->AKF_FORMTITLE; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_NUMBEROFENTRIES; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_NUMBEROFFIELDS; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_PUBLISHED; ?></th>
    </tr>
    <?php
    $k = 0;
    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
      $row = &$rows[$i];
      ?>
      <tr class="<?php echo "row$k"; ?>">
        <td width="20"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);" /></td>
        <?php
          if ($row->noentries) {
            echo "<td><a href=\"#edit\" onclick=\"return listItemTask('cb$i','datadetails')\">$row->title</a></td>";
          } else {
            echo "<td>$row->title</td>";
          }
        ?>
        <td align="center"><?php echo $row->noentries; ?></td>
        <td align="center"><?php echo $row->nofields; ?></td>
        <?php
          $now = date( "Y-m-d h:i:s" );
          if ($now <= $row->publish_up && $row->published == "1") {
            $img = 'publish_y.png';
          } else if (($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->published == "1") {
            $img = 'publish_g.png';
          } else if ($now > $row->publish_down && $row->published == "1") {
            $img = 'publish_r.png';
          } elseif ($row->published == "0") {
            $img = "publish_x.png";
          }
          $times = '';
          if (isset($row->publish_up)) {
            if ($row->publish_up == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Start: Always</td></tr>";
            } else {
              $times .= "<tr><td>Start: $row->publish_up</td></tr>";
            }
          }
          if (isset($row->publish_down)) {
            if ($row->publish_down == '0000-00-00 00:00:00') {
              $times .= "<tr><td>Finish: No Expiry</td></tr>";
            } else {
              $times .= "<tr><td>Finish: $row->publish_down</td></tr>";
            }
          }
        ?>
        <td align="center"><a href="javascript: void(0);" onMouseOver="return overlib('<table border=0 width=100% height=100%><?php echo $times; ?></table>', CAPTION, 'Publish Information', BELOW, RIGHT);" onMouseOut="return nd();" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? "unpublish" : "publish";?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
      </tr>
      <?php
      $k = 1 - $k;
    }
    ?>
    <tr>
      <th align="center" colspan="5"><?php echo $pageNav->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="5"><?php echo $pageNav->writePagesCounter(); ?></td>
    </tr>
  </table>
  <br />
  <table cellpadding="15">
    <tr>
      <td><img align="absmiddle" src="images/publish_y.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBPENDING; ?></td>
      <td><img align="absmiddle" src="images/publish_g.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBCURRENT; ?></td>
      <td><img align="absmiddle" src="images/publish_r.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_PUBEXPIRED; ?></td>
      <td><img align="absmiddle" src="images/publish_x.png" width="12" height="12" border=0 hspace="5" /><?php echo $AKFLANG->AKF_UNPUBLISHED; ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option; ?>">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  </form>
  <?php
}

function showDataFields( $option, &$pageNav, &$senderrows, &$fieldrowid, &$fieldrowtitle, &$datarray ) {
  global $AKFLANG;
  ?>
  <form action="index2.php" method="post" name="adminForm">
  <table class="blockheading" width="100%">
    <tr>
      <td width="25%"><img src="components/com_akoforms/images/logo.png"></td>
      <td width="50%" align="center" class="sectionname"><?php echo $AKFLANG->AKF_STOREDDATA; ?></td>
      <td width="25%" align="right" valign="bottom"><?php echo $pageNav->writeLimitBox(); ?></td>
    </tr>
  </table>
  <table width="100%" class="adminlist">
    <tr>
      <th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $senderrows ); ?>);" /></th>
      <th align="left" nowrap><?php echo $AKFLANG->AKF_STOREDIP; ?></th>
      <th align="center" nowrap><?php echo $AKFLANG->AKF_STOREDDATE; ?></th>
      <?php
        $frcolspan = count( $fieldrowtitle ) + 4;
        foreach ($fieldrowtitle as $frtitle) {
          echo "<th align='center' nowrap>$frtitle</th>";
        }
      ?>
    </tr>
    <?php
    $k = 0;
    for ($i=0, $n=count( $senderrows ); $i < $n; $i++) {
      $row = &$senderrows[$i];
      $thisformid = $row->formid;
      ?>
      <tr class="<?php echo "row$k"; ?>">
        <td width="20"><input type="checkbox" id="cb<?php echo $i;?>" name="fid[]" value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);" /></td>
        <td><?php echo $row->senderip; ?></td>
        <td align="center"><?php echo $row->senderdate; ?></td>
        <?php
          foreach($fieldrowid as $frid) {
            echo "<td align='center'>".stripslashes($datarray[$row->id][$frid])."</td>";
          }
        ?>
      </tr>
      <?php
      $k = 1 - $k;
    }
    ?>
    <tr>
      <th align="center" colspan="<?php echo $frcolspan; ?>"><?php echo $pageNav->writePagesLinks(); ?></th>
    </tr>
    <tr>
      <td align="center" colspan="<?php echo $frcolspan; ?>"><?php echo $pageNav->writePagesCounter(); ?></td>
    </tr>
  </table>
  <input type="hidden" name="option" value="<?php echo $option; ?>">
  <input type="hidden" name="task" value="datadetails">
  <input type="hidden" name="boxchecked" value="0">
  <input type="hidden" name="cid" value="<?php echo $thisformid; ?>">
  </form>
  <?php
}

# End of Class
}
?>