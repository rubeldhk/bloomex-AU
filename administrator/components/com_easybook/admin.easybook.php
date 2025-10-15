<?php
/**
* EasyBook - A Joomla Guestbook Component
* @version 1.1 Stable
* @package EasyBook
* Based on AkoBook
* @license Released under the terms of the GNU General Public License (see LICENSE.php in the Joomla! root directory)
* @Achim Raji (aka cybergurk) - David Jardin (aka SniperSister) - Cedric May - Siegmund Langsch (aka langsch2)
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
if (file_exists($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php')) {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php');
} else {
      include($mosConfig_absolute_path.'/components/com_easybook/languages/english.php');
}

require_once( $mainframe->getPath( 'class' ) );
require_once( $mainframe->getPath( 'admin_html' ) );
$task = mosGetParam( $_REQUEST, 'task' );
$gbid  = mosGetParam( $_REQUEST, 'gbid', array( 0 ) );
if (!is_array( $gbid )) {
 $gbid = array ( 0 );
}
$file = mosGetParam( $_REQUEST, 'file' );
$filecontent = mosGetParam( $_POST, 'filecontent', '1', _MOS_ALLOWRAW );
$eb_wordwrap  = mosGetParam( $_REQUEST, 'eb_wordwrap' );
$eb_spambgcolour = mosGetParam( $_REQUEST, 'eb_spambgcolour' );
$eb_spamcodecolour = mosGetParam( $_REQUEST, 'eb_spamcodecolour' );
$eb_spamlinecolour = mosGetParam( $_REQUEST, 'eb_spamlinecolour' );
$eb_spambordercolour = mosGetParam( $_REQUEST, 'eb_spambordercolour' );
$eb_spamfiletyp = mosGetParam( $_REQUEST, 'eb_spamfiletyp' );
$eb_spamfix = mosGetParam( $_REQUEST, 'eb_spamfix' );
$eb_showskype = mosGetParam( $_REQUEST, 'eb_showskype' );
$eb_maxlength = mosGetParam( $_REQUEST, 'eb_maxlength' );
$eb_mailcheck = mosGetParam( $_REQUEST, 'eb_mailcheck' );
$eb_offline = mosGetParam( $_REQUEST, 'eb_offline' );
$eb_offline_message = mosGetParam( $_REQUEST, 'eb_offline_message', '' , _MOS_ALLOWRAW );
$eb_autopublish = mosGetParam( $_REQUEST, 'eb_autopublish' );
$eb_notify = mosGetParam( $_REQUEST, 'eb_notify' );
$eb_notify_email = mosGetParam( $_REQUEST, 'eb_notify_email' );
$eb_thankuser = mosGetParam( $_REQUEST, 'eb_thankuser' );
$eb_perpage = mosGetParam( $_REQUEST, 'eb_perpage' );
$eb_sorting = mosGetParam( $_REQUEST, 'eb_sorting' );
$eb_showrating = mosGetParam( $_REQUEST, 'eb_showrating' );
$eb_maxvoting = mosGetParam( $_REQUEST, 'eb_maxvoting' );
$eb_allowentry = mosGetParam( $_REQUEST, 'eb_allowentry' );
$eb_anonentry = mosGetParam( $_REQUEST, 'eb_anonentry' );
$eb_bbcodesupport = mosGetParam( $_REQUEST, 'eb_bbcodesupport' );
$eb_linksupport = mosGetParam( $_REQUEST, 'eb_linksupport' );
$eb_mailsupport = mosGetParam( $_REQUEST, 'eb_mailsupport' );
$eb_smiliesupport = mosGetParam( $_REQUEST, 'eb_smiliesupport' );
$eb_picsupport = mosGetParam( $_REQUEST, 'eb_picsupport' );
$eb_showmail = mosGetParam( $_REQUEST, 'eb_showmail' );
$eb_mailmandatory = mosGetParam( $_REQUEST, 'eb_mailmandatory');
$eb_showhome = mosGetParam( $_REQUEST, 'eb_showhome' );
$eb_showloca = mosGetParam( $_REQUEST, 'eb_showloca' );
$eb_showicq = mosGetParam( $_REQUEST, 'eb_showicq' );
$eb_showaim = mosGetParam( $_REQUEST, 'eb_showaim' );
$eb_showmsn = mosGetParam( $_REQUEST, 'eb_showmsn' );
$eb_showyah = mosGetParam( $_REQUEST, 'eb_showyah' );
$eb_wordfilter = mosGetParam( $_REQUEST, 'eb_wordfilter' );
$eb_wordfilterfront = mosGetParam( $_REQUEST, 'eb_wordfilterfront' );
$eb_wordfiltermail = mosGetParam( $_REQUEST, 'eb_wordfiltermail' );
$eb_footer = mosGetParam( $_REQUEST, 'eb_footer' );

switch ($task) {
  case "view":
    showGuestbook( $option, $database, $task );
    break;

  case "publish":
    publishGuestbook( $gbid, 1, $option );
    break;

  case "unpublish":
    publishGuestbook( $gbid, 0, $option );
    break;

  case "new":
    editGuestbook( $option, $database, 0 );
    break;

  case "edit":
    editGuestbook( $option, $database, $gbid[0] );
    break;

  case "remove":
    removeGuestbook( $database, $gbid, $option );
    break;

  case "save":
    saveGuestbook( $option, $database );
    break;

  case "config":
    showConfig( $option );
    break;

  case "convert":
    showConvert( $option, $database );
    break;

  case "convert3.42":
    convert( $option, $database );
    break;

  case "convertyah":
    convertYah( $option, $database );
    break;

  case "savesettings":
    saveConfig ($option,$eb_wordwrap,$eb_spambgcolour,$eb_spamcodecolour,$eb_spamlinecolour,$eb_spambordercolour,$eb_spamfiletyp,$eb_spamfix,$eb_showskype ,$eb_maxlength ,$eb_mailcheck, $eb_offline, $eb_offline_message, $eb_autopublish, $eb_notify, $eb_notify_email, $eb_thankuser, $eb_perpage, $eb_sorting, $eb_showrating, $eb_maxvoting, $eb_allowentry, $eb_anonentry, $eb_bbcodesupport, $eb_linksupport, $eb_mailsupport, $eb_smiliesupport, $eb_picsupport, $eb_showmail, $eb_mailmandatory, $eb_showhome, $eb_showloca, $eb_showicq, $eb_showaim, $eb_showmsn, $eb_showyah, $eb_wordfilter, $eb_wordfilterfront,$eb_wordfiltermail, $eb_footer);
    break;

  case "about":
    showAbout();
    break;

  case "language":
    showLanguage($option);
    break;

 	case "savelanguage":
    saveLanguage($file, $filecontent, $option);
    break;

	case "words":
    showWords($option);
    break;

  case "savewords":
    saveWords($file, $filecontent, $option);
    break;

  default:
    showOverview();
    break;
}
echo "<div style='vertical-align:bottom'><p><font class='small'>Version: 1.1 Stable by <a href='http://www.easy-joomla.org/' target='_blank'>EasyJoomla</a></font></p></div>";

function showGuestbook ( $option, &$db ) {
  $search = trim( strtolower( mosGetParam( $_POST, 'search', '' ) ) );
  $limit = intval( mosGetParam( $_POST, 'limit', 10 ) );
  $limitstart = intval( mosGetParam( $_POST, 'limitstart', 0 ) );
  $task = mosGetParam( $_GET, 'task' );

  $where = array();

  if ($search) {
    $where[] = "LOWER(gbtext) LIKE '%$search%'";
  }

  // get the total number of records
  $db->setQuery( "SELECT count(*) FROM #__easybook AS a".(count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "") );
  $total = $db->loadResult();
  echo $db->getErrorMsg();


  $db->setQuery( "SELECT * FROM #__easybook"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY gbid DESC"
    . "\nLIMIT $limitstart,$limit"
  );

  $rows = $db->loadObjectList();
  if ($db->getErrorNum()) {
    echo $db->stderr();
    return false;
  }

  include_once("includes/pageNavigation.php");
  $pageNav = new mosPageNav( $total, $limitstart, $limit  );

  HTML_Guestbook::showGuestbookEntries( $option, $task, $rows, $search, $pageNav );
}

function removeGuestbook( &$db, $cid, $option ) {
  if (count( $cid )) {
    $cids = implode( ',', $cid );
    $db->setQuery( "DELETE FROM #__easybook WHERE gbid IN ($cids)" );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
    }
  }
  mosRedirect( "index2.php?option=$option&task=view" );
}

function publishGuestbook( $cid=null, $publish=1,  $option ) {
  global $database;

  if (!is_array( $cid ) || count( $cid ) < 1) {
    $action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . _GUESTBOOK_ADMIN_MARKENTRYFORACTION . $action . "'); window.history.go(-1);</script>\n";
    exit;
  }

  $cids = implode( ',', $cid );

  $database->setQuery( "UPDATE #__easybook SET published='$publish' WHERE gbid IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    exit();
  }

  mosRedirect( "index2.php?option=$option&task=view" );
}

function editGuestbook( $option, &$db, $gbid ) {
  global $mosConfig_absolute_path, $mosConfig_live_site;

  $row = new mosEasybook( $db );

  if ($gbid) {
    $db->setQuery( "SELECT * FROM #__easybook WHERE gbid = $gbid" );
    $rows = $db->loadObjectList();
    $row = $rows[0];
  } else {
    // initialise new record
    $row->published = 0;
  }

// make the select list for the image positions
	$yesno[] = mosHTML :: makeOption('0', _GUESTBOOK_ADMIN_NO);
	$yesno[] = mosHTML :: makeOption('1', _GUESTBOOK_ADMIN_YES);

// build the html select list
  $publist = mosHTML::selectList( $yesno, 'published', 'class="inputbox" size="2"', 'value', 'text', $row->published );

  HTML_Guestbook::editGuestbook( $option, $row, $publist );
}

function saveGuestbook( $option, &$db ) {
  global $my;
  $row = new mosEasybook( $db );
  if (!$row->bind( $_POST )) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  $row->gbdate = $row->gbdate = strtotime($row->gbdate, time());
  $row->_tbl_key = "gbid";

  if (!$row->store()) {
   echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }

  mosRedirect( "index2.php?option=$option&task=view" );
}

############################################################################
############################################################################
function showOverview() {
?>
<style>
/* standard form style table */
table.thisform {
	background-color: #F7F8F9;
	border: solid 1px #d5d5d5;
	width: 600px;
	padding: 10px;
	border-collapse: collapse;
}
table.thisform tr.row0 {
	background-color: #F7F8F9;
}
table.thisform tr.row1 {
	background-color: #eeeeee;
}
table.thisform th {
	font-size: 15px;
	font-weight:normal;
	font-variant:small-caps;
	padding-top: 6px;
	padding-bottom: 2px;
	padding-left: 4px;
	padding-right: 4px;
	text-align: left;
	height: 25px;
	color: #666666;
	background: url(../images/background.gif);
	background-repeat: repeat;
}
table.thisform td {
	padding: 3px;
	text-align: left;
	border: 1px;
	border-style:solid;
	border-bottom-color:#EFEFEF;
	border-right-color:#EFEFEF;
	border-left-color:#EFEFEF;
	border-top-color:#EFEFEF;
}

table.thisform2 {
	background-color: #F7F8F9;
	border: solid 1px #d5d5d5;
	width: 100%;
	padding: 5px;
}
table.thisform2 td {
	padding: 5px;
	text-align: center;
	border: 1x;
	border-style: solid;
	border-bottom-color:#EFEFEF;
	border-right-color:#EFEFEF;
	border-left-color:#EFEFEF;
	border-top-color:#EFEFEF;
}
/* .thisform2 td:hover {
	background-color: #edf1cf;
	border:	1px solid #9fb028;
}*/
.thisform2 td:hover a {
	color: #ffffff;
}
.thisform2 td:hover  {
	background-image: url(components/com_easybook/images/admin_hover_bg.png);
	background-repeat: no-repeat;
	background-position: center top;
}
img, div {behavior: url(../components/com_easybook/iepngfix.htc)}
</style>
<table cellpadding="4" cellspacing="0" border="0" align="center">
    <tr>
      <td width="100%">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;">
      </td>
	</tr>
</table>
<br />
<br />
<table class="thisform">
   <tr class="thisform">
      <td width="50%" valign="top" class="thisform">
<table width="100%" class="thisform2">
         <tr class="thisform2">
            <td align="center" height="150" width="33%" class="thisform2">
			<div class="description">
            <a href="index2.php?option=com_easybook&task=view" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_VIEWENTRYSLINK;?>">
            <img src="components/com_easybook/images/viewentry.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_VIEWENTRYSLINK;?></a></div>
            </td>

            <td align="center" height="150" width="33%" class="thisform2">
            <a href="index2.php?option=com_easybook&task=config" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_EDITCONFIGLINK;?>">
            <img src="components/com_easybook/images/config.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_EDITCONFIGLINK ;?>
            </a>
            </td>

            <td align="center" height="150" width="33%" class="thisform2">
            <a href="index2.php?option=com_easybook&task=convert" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_MIGRATORTOOLLINK;?>">
            <img src="components/com_easybook/images/convert.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_MIGRATORTOOLLINK;?>
            </a>
            </td>

         </tr>
         <tr class="thisform2">
            <td align="center" height="150" width="33%" class="thisform2">
            <a href="index2.php?option=com_easybook&task=language" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_EDITLANGUAGELINK;?>">
            <img src="components/com_easybook/images/langmanager.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_EDITLANGUAGELINK;?>
            </a>
            </td>

            <td align="center" height="150" width="33%" class="thisform2">
            <a href="index2.php?option=com_easybook&task=words" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_WORDSLINK;?>">
            <img src="components/com_easybook/images/addedit.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_WORDSLINK ;?>
            </a>
            </td>

            <td align="center" height="150" width="33%" class="thisform2">
            <a href="index2.php?option=com_easybook&task=about" style="text-decoration:none;" title="<?php echo _GUESTBOOK_ADMIN_ABOUTLINK;?>">
            <img src="components/com_easybook/images/about.png" width="48px" height="48px" align="middle" border="0"/>
            <br /><br />
            <?php echo _GUESTBOOK_ADMIN_ABOUTLINK;?>
            </a>
            </td>
         </tr>
         </table>  </td></tr></table>


<?php
}

function convert( $option, &$db ) {
  global $mosConfig_absolute_path, $database;
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
  $database->setQuery( "SELECT * FROM #__akobook");
  $oldrows = $database->loadObjectList();
  foreach( $oldrows AS $oldrow )
  {
  $row = new mosEasybook( $db );
  $row->gbip = $oldrow->gbip;
  $row->gbname = $oldrow->gbname;
  $row->gbmail = $oldrow->gbmail;
  $row->gbloca = $oldrow->gbloca;
  $row->gbpage = $oldrow->gbpage;
  $row->gbvote = $oldrow->gbvote;
  $row->gbtext = $oldrow->gbtext;
  $row->gbdate = $oldrow->gbdate;
  $row->gbcomment = $oldrow->gbcomment;
  $row->gbedit = $oldrow->gbedit;
  $row->gbeditdate = $oldrow->gbeditdate;
  $row->published = $oldrow->published;
  $row->gbicq = $oldrow->gbicq;
  $row->gbaim = $oldrow->gbaim;
  $row->gbmsn = $oldrow->gbmsn;
  $row->gbyah = NULL;
  $row->gbskype = NULL;
  if($row->store())
  {echo "<font color='green'>" . _GUESTBOOK_ADMIN_MIGRATION_OK . $oldrow->gbid . "</font><br />";}
  else
  {echo "<font color='red'>" . _GUESTBOOK_ADMIN_MIGRATION_ERROR . $oldrow->gbid . "</font><br />";}
  }
}

function convertYah( $option, &$db ) {
  global $mosConfig_absolute_path, $database;
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
  $database->setQuery( "SELECT * FROM #__akobook");
  $oldrows = $database->loadObjectList();
  foreach( $oldrows AS $oldrow )
  {
  $row = new mosEasybook( $db );
  $row->gbip = $oldrow->gbip;
  $row->gbname = $oldrow->gbname;
  $row->gbmail = $oldrow->gbmail;
  $row->gbloca = $oldrow->gbloca;
  $row->gbpage = $oldrow->gbpage;
  $row->gbvote = $oldrow->gbvote;
  $row->gbtext = $oldrow->gbtext;
  $row->gbdate = $oldrow->gbdate;
  $row->gbcomment = $oldrow->gbcomment;
  $row->gbedit = $oldrow->gbedit;
  $row->gbeditdate = $oldrow->gbeditdate;
  $row->published = $oldrow->published;
  $row->gbicq = $oldrow->gbicq;
  $row->gbaim = $oldrow->gbaim;
  $row->gbmsn = $oldrow->gbmsn;
  $row->gbyah = $oldrow->gbyah;
  $row->gbskype = NULL;
  if($row->store())
  {echo "<font color='green'>" . _GUESTBOOK_ADMIN_MIGRATION_OK . $oldrow->gbid . "</font><br />";}
  else
  {echo "<font color='red'>" . _GUESTBOOK_ADMIN_MIGRATION_ERROR . $oldrow->gbid . "</font><br />";}
  }

}

function showConvert( $option ) {
  global $mosConfig_absolute_path;
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
  ?>
	<div align="center"><a href="index2.php?option=com_easybook&task=convertyah"><?php echo _GUESTBOOK_ADMIN_MIGRATION_YAH ?></a><br />
						<a href="index2.php?option=com_easybook&task=convert3.42"><?php echo _GUESTBOOK_ADMIN_MIGRATION ?></a></div>
<?php
}

function showConfig( $option ) {
  global $mosConfig_absolute_path;
  require($mosConfig_absolute_path."/administrator/components/com_easybook/config.easybook.php");
		mosCommonHTML::loadOverlib();

?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      if (form.eb_perpage.value == ""){
        alert( "You must set entries per page greater 0!" );
      } else {
        submitform( pressbutton );
      }
    }
    </script>
  <form action="index2.php" method="post" name="adminForm" id="adminForm">
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%" class="sectionname">
        <img src="components/com_easybook/images/logo.png" height="45" width="175" style="margin-right:10px;">
    </td>
  </tr>
  </table>
  <table width="100%"><tr><td>
  <?php
  $easygbtabs = new mosTabs( 0 );
  $easygbtabs->startPane( "easy_guestbook" );
    $easygbtabs->startTab(_GUESTBOOK_ADMIN_BACKENDPAGE,"Backend-page");
    ?>
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminForm">
      <tr align="center" valign="middle">
        <td width="20%" align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_OFFLINE ?></strong></td>
        <td width="20%" align="left" valign="top">
        <?php
	$yesno[] = mosHTML :: makeOption('0', _GUESTBOOK_ADMIN_NO);
	$yesno[] = mosHTML :: makeOption('1', _GUESTBOOK_ADMIN_YES);
          echo mosHTML::yesnoRadioList( 'eb_offline', 'class="inputbox"', $eb_offline );
        ?>
        </td>
        <td width="60%" align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_OFFLINEDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_OFFLINEMSG ?></strong></td>
        <td align="left" valign="top">
          <?php $eb_offline_message = stripslashes("$eb_offline_message"); ?>
          <textarea class="inputbox" cols="30" rows="5" name="eb_offline_message"><?php echo "$eb_offline_message"; ?></textarea>
          </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_OFFLINEMSGDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_AUTOPUBLISH ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_autopublish', 'class="inputbox"', $eb_autopublish );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_AUTOPUBLISHDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MAILADMIN ?>:</strong>&nbsp;&nbsp;<?php $tip = _GUESTBOOK_ADMIN_MAILADMINTOOLTIP; echo mosToolTip($tip, _GUESTBOOK_ADMIN_USEABILITY); ?></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_notify', 'class="inputbox"', $eb_notify );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_MAILADMINDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_ADMINMAIL ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_notify_email" value="<?php echo "$eb_notify_email"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_ADMINMAILDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_THANKUSER ?></strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_thankuser', 'class="inputbox"', $eb_thankuser );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_THANKUSERDESC ?></td>
    </table>
    <?php
    $easygbtabs->endTab();
    $easygbtabs->startTab(_GUESTBOOK_ADMIN_FRONTENDPAGE,"Frontend-page");
    ?>
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminForm">
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_WORDWRAP ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_wordwrap', 'class="inputbox"', $eb_wordwrap );
        ?>
		</td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_WORDWRAPDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_WORDWRAPCOUNT ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_maxlength" value="<?php echo "$eb_maxlength"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_WORDWRAPCOUNTDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_ENTRIESPERPAGE ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_perpage" value="<?php echo "$eb_perpage"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_ENTRIESPERPAGEDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SORTING ?>:</strong></td>
        <td align="left" valign="top">
        <?php
	$gbsorting[] = mosHTML :: makeOption('DESC', _GUESTBOOK_ADMIN_SORTINGDESC);
	$gbsorting[] = mosHTML :: makeOption('ASC', _GUESTBOOK_ADMIN_SORTINGASC);
          $mc_eb_sorting = mosHTML::selectList( $gbsorting, 'eb_sorting', 'class="inputbox" size="2"', 'value', 'text', $eb_sorting );
          echo $mc_eb_sorting;
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SORTINGDESCRIPTION ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MAXRATE ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_maxvoting" value="<?php echo "$eb_maxvoting"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_MAXRATEDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_ALLOWENTRY ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_allowentry', 'class="inputbox"', $eb_allowentry );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_ALLOWENTRYDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_ANONENTRY ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_anonentry', 'class="inputbox"', $eb_anonentry );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_ANONENTRYDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_BBCODE ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_bbcodesupport', 'class="inputbox"', $eb_bbcodesupport );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_BBCODEDESC ?></td>
      </tr>
       <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MAILSUPPORT ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_mailsupport', 'class="inputbox"', $eb_mailsupport );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_MAILSUPPORTDESC ?></td>
      </tr>
       <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_LINKSUPPORT ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_linksupport', 'class="inputbox"', $eb_linksupport );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_LINKSUPPORTDESC ?></td>
      </tr>
       <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_PICSUPPORT ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_picsupport', 'class="inputbox"', $eb_picsupport );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_PICSUPPORTDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SMILIESUPPORT ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_smiliesupport', 'class="inputbox"', $eb_smiliesupport );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SMILIESUPPORTDESC ?></td>
      </tr>
	<tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWFOOTER ?>:</strong>&nbsp;&nbsp;<?php $tip = _GUESTBOOK_ADMIN_SHOWFOOTERTOOLTIP; echo mosToolTip($tip, 'Copyright - Footer'); ?></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_footer', 'class="inputbox"', $eb_footer );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SHOWFOOTERDESC ?></td>
      </tr>
    </table>
    <?php
    $easygbtabs->endTab();
    $easygbtabs->startTab(_GUESTBOOK_ADMIN_SECURITYPAGE,"Security-page");
    ?>
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminForm">
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIX ?>:</strong></td>
        <td align="left" valign="top">
		 <?php
          echo mosHTML::yesnoRadioList( 'eb_spamfix', 'class="inputbox"', $eb_spamfix );
        ?></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIXTYPE ?>:</strong></td>
        <td align="left" valign="top">
        <?php
	$gbfiletyp[] = mosHTML :: makeOption('PNG', '.PNG');
	$gbfiletyp[] = mosHTML :: makeOption('GIF', '.GIF');
	$gbfiletyp[] = mosHTML :: makeOption('JPEG', '.JPEG');
          $mc_eb_spamfiletyp = mosHTML::selectList( $gbfiletyp, 'eb_spamfiletyp', 'class="inputbox" size="3"', 'value', 'text', $eb_spamfiletyp );
          echo $mc_eb_spamfiletyp;
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXTYPEDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIXBGCOLOUR ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_spambgcolour" value="<?php echo "$eb_spambgcolour"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXBGCOLOURDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIXCODECOLOUR ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_spamcodecolour" value="<?php echo "$eb_spamcodecolour"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXCODECOLOURDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIXLINECOLOUR ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_spamlinecolour" value="<?php echo "$eb_spamlinecolour"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXLINECOLOURDESC ?></td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SPAMFIXBORDERCOLOUR ?>:</strong></td>
        <td align="left" valign="top"><input type="text" name="eb_spambordercolour" value="<?php echo "$eb_spambordercolour"; ?>"></td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_SPAMFIXBORDERCOLOURDESC ?></td>
      </tr>
        <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_WORDFILTER ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_wordfilter', 'class="inputbox"', $eb_wordfilter );
          echo "<br><a href='index2.php?option=com_easybook&task=words'>". _GUESTBOOK_ADMIN_WORDFILTEREDIT . "</a>";
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_WORDFILTERDESC ?></td>
      </tr>
		<tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_WORDFILTERFRONT ?></strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_wordfilterfront', 'class="inputbox"', $eb_wordfilterfront );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_WORDFILTERFRONTDESC ?></td>
      </tr>
      </tr>
		<tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_WORDFILTERMAIL ?></strong>&nbsp;&nbsp;<?php $tip = _GUESTBOOK_ADMIN_WORDFILTERTOOLTIP; echo mosToolTip($tip, _GUESTBOOK_ADMIN_TIP); ?></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_wordfiltermail', 'class="inputbox"', $eb_wordfiltermail );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_WORDFILTERMAILDESC ?></td>
      </tr>
        <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MAILCHECK ?>:</strong>&nbsp;&nbsp;<?php $tip = _GUESTBOOK_ADMIN_MAILCHECKTOOLTIP; echo mosToolTip($tip, _GUESTBOOK_ADMIN_EXAMPLE); ?></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_mailcheck', 'class="inputbox"', $eb_mailcheck );
        ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_MAILCHECKDESC ?></td>
      </tr>
    </table>
    <?php
    $easygbtabs->endTab();
    $easygbtabs->startTab(_GUESTBOOK_ADMIN_FIELDSPAGE,"Fields-page");
    ?>
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminForm">
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWMAIL ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showmail', 'class="inputbox"', $eb_showmail );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MAILMANDATORY ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_mailmandatory', 'class="inputbox"', $eb_mailmandatory );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
                  
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWHOMEPAGE ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showhome', 'class="inputbox"', $eb_showhome );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWLOCATION ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showloca', 'class="inputbox"', $eb_showloca );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWICQ ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showicq', 'class="inputbox"', $eb_showicq );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWAIM ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showaim', 'class="inputbox"', $eb_showaim );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWMSN ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showmsn', 'class="inputbox"', $eb_showmsn );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
	     <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWYAH ?>:</strong></td>
	     <td align="left" valign="top">
	     <?php
	       echo mosHTML::yesnoRadioList( 'eb_showyah', 'class="inputbox"', $eb_showyah );
	     ?>
	     </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
	     <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWSKYPE ?>:</strong></td>
	     <td align="left" valign="top">
	     <?php
	       echo mosHTML::yesnoRadioList( 'eb_showskype', 'class="inputbox"', $eb_showskype );
	     ?>
	     </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_SHOWRATE ?>:</strong></td>
        <td align="left" valign="top">
        <?php
          echo mosHTML::yesnoRadioList( 'eb_showrating', 'class="inputbox"', $eb_showrating );
        ?>
        </td>
        <td align="left" valign="top">&nbsp;</td>
      </tr>
    </table>
    <?php
    $easygbtabs->endTab();
	 $easygbtabs->startTab(_GUESTBOOK_ADMIN_TOOLSPAGE,"Tools-page");
    ?>
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminForm">
      <tr align="center" valign="middle">
        <td align="left" valign="top"><strong><?php echo _GUESTBOOK_ADMIN_MIGRATORTOOL ?>:</strong></td>
        <td align="left" valign="top">
       <?php echo "<br><a href='index2.php?option=com_easybook&task=convert'> Migrator-Tool</a>"; ?>
        </td>
        <td align="left" valign="top"><?php echo _GUESTBOOK_ADMIN_MIGRATORTOOLDESC ?></td>
      </tr>
    </table>
    <?php
    $easygbtabs->endTab();
  $easygbtabs->endPane();
  ?>
	</td></tr></table>
  <input type="hidden" name="id" value="">
  <input type="hidden" name="task" value="">
  <input type="hidden" name="option" value="<?php echo $option; ?>">
</form>
<?php
}

function saveConfig ($option,$eb_wordwrap,$eb_spambgcolour,$eb_spamcodecolour,$eb_spamlinecolour,$eb_spambordercolour,$eb_spamfiletyp,$eb_spamfix,$eb_showskype,$eb_maxlength , $eb_mailcheck, $eb_offline, $eb_offline_message, $eb_autopublish, $eb_notify, $eb_notify_email, $eb_thankuser, $eb_perpage, $eb_sorting, $eb_showrating, $eb_maxvoting, $eb_allowentry, $eb_anonentry, $eb_bbcodesupport, $eb_linksupport, $eb_mailsupport, $eb_smiliesupport, $eb_picsupport, $eb_showmail, $eb_mailmandatory, $eb_showhome, $eb_showloca, $eb_showicq, $eb_showaim, $eb_showmsn, $eb_showyah, $eb_wordfilter,$eb_wordfilterfront, $eb_wordfiltermail, $eb_footer) {
  $configfile = "components/com_easybook/config.easybook.php";
  @chmod ($configfile, 0766);
  $permission = is_writable($configfile);
  if (!$permission) {
		mosRedirect("index2.php?option=$option&task=config", _GUESTBOOK_ADMIN_NOTWRITABLE);
    break;
  }

  $eb_offline_message = addslashes("$eb_offline_message");

  $config = "<?php\n";
  $config .= "\$eb_offline = \"$eb_offline\";\n";
  $config .= "\$eb_offline_message = \"$eb_offline_message\";\n";
  $config .= "\$eb_autopublish = \"$eb_autopublish\";\n";
  $config .= "\$eb_notify = \"$eb_notify\";\n";
  $config .= "\$eb_notify_email = \"$eb_notify_email\";\n";
  $config .= "\$eb_thankuser = \"$eb_thankuser\";\n";
  $config .= "\$eb_perpage = \"$eb_perpage\";\n";
  $config .= "\$eb_sorting = \"$eb_sorting\";\n";
  $config .= "\$eb_showrating = \"$eb_showrating\";\n";
  $config .= "\$eb_maxvoting = \"$eb_maxvoting\";\n";
  $config .= "\$eb_allowentry = \"$eb_allowentry\";\n";
  $config .= "\$eb_anonentry = \"$eb_anonentry\";\n";
  $config .= "\$eb_bbcodesupport = \"$eb_bbcodesupport\";\n";
  $config .= "\$eb_linksupport = \"$eb_linksupport\";\n";
  $config .= "\$eb_mailsupport = \"$eb_mailsupport\";\n";
  $config .= "\$eb_smiliesupport = \"$eb_smiliesupport\";\n";
  $config .= "\$eb_picsupport = \"$eb_picsupport\";\n";
  $config .= "\$eb_showmail = \"$eb_showmail\";\n";
  $config .= "\$eb_mailmandatory = \"$eb_mailmandatory\";\n";
  $config .= "\$eb_showhome = \"$eb_showhome\";\n";
  $config .= "\$eb_showloca = \"$eb_showloca\";\n";
  $config .= "\$eb_showicq = \"$eb_showicq\";\n";
  $config .= "\$eb_showaim = \"$eb_showaim\";\n";
  $config .= "\$eb_showmsn = \"$eb_showmsn\";\n";
  $config .= "\$eb_showyah = \"$eb_showyah\";\n";
  $config .= "\$eb_showskype = \"$eb_showskype\";\n";
  $config .= "\$eb_wordfilter = \"$eb_wordfilter\";\n";
  $config .= "\$eb_wordfilterfront = \"$eb_wordfilterfront\";\n";
  $config .= "\$eb_wordfiltermail = \"$eb_wordfiltermail\";\n";
  $config .= "\$eb_mailcheck = \"$eb_mailcheck\";\n";
  $config .= "\$eb_footer = \"$eb_footer\";\n";
  $config .= "\$eb_maxlength = \"$eb_maxlength\";\n";
  $config .= "\$eb_wordwrap = \"$eb_wordwrap\";\n";
  $config .= "\$eb_spamfiletyp = \"$eb_spamfiletyp\";\n";
  $config .= "\$eb_spamfix = \"$eb_spamfix\";\n";
  $config .= "\$eb_spambgcolour = \"$eb_spambgcolour\";\n";
  $config .= "\$eb_spamcodecolour = \"$eb_spamcodecolour\";\n";
  $config .= "\$eb_spamlinecolour = \"$eb_spamlinecolour\";\n";
  $config .= "\$eb_spambordercolour = \"$eb_spambordercolour\";\n";
  $config .= "?>";

  if ($fp = fopen("$configfile", "w")) {
    fputs($fp, $config, strlen($config));
    fclose ($fp);
  }
	mosRedirect("index2.php?option=$option&task=config", _GUESTBOOK_ADMIN_CONFIGSAVED);
}

############################################################################
############################################################################

function showAbout() {
  # Show about screen to user
  HTML_Guestbook::showAbout();
}

############################################################################
############################################################################

function showLanguage($option) {

  global $mosConfig_absolute_path, $mosConfig_lang;
  if (file_exists($mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php')) {
    $file = $mosConfig_absolute_path.'/components/com_easybook/languages/'.$mosConfig_lang.'.php';
  } else {
    $file = $mosConfig_absolute_path.'/components/com_easybook/languages/english.php';
  }
  if ($mosConfig_lang == "germanf"){ $file = $mosConfig_absolute_path.'/components/com_easybook/languages/german.php';}
  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
    echo "<center><h1><font color=red>" . _GUESTBOOK_ADMIN_WARNING . "</FONT></h1><BR>";
		echo "<B>" . _GUESTBOOK_ADMIN_LANGNOTWRITABLE . "</B></center><BR><BR>";
  }

  HTML_Guestbook::showLanguage($file,$option,$permission);
}

function saveLanguage($file, $filecontent, $option) {

  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
		mosRedirect("index2.php?option=$option&task=language", _GUESTBOOK_ADMIN_NOTWRITABLE);
    break;
  }

  if ($fp = fopen( $file, "w")) {
    fputs($fp,stripslashes($filecontent));
    fclose($fp);
		mosRedirect("index2.php?option=$option&task=language", _GUESTBOOK_ADMIN_LANGSAVED);
  }
}


######################showwords##################################
function showWords($option) {

  global $mosConfig_absolute_path, $mosConfig_lang;

  if (file_exists($mosConfig_absolute_path.'/components/com_easybook/languages/wordfilter.php')) {
    $file = $mosConfig_absolute_path.'/components/com_easybook/languages/wordfilter.php';
  }
  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
    echo "<center><h1><font color=red>" . _GUESTBOOK_ADMIN_WARNING . "</FONT></h1><BR>";
    echo "<B>" . _GUESTBOOK_ADMIN_WORDFILTERNOTWRITABLE . "</B></center><BR><BR>";
  }

  HTML_Guestbook::showWords($file,$option,$permission);
}

function saveWords($file, $filecontent, $option) {

  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
    mosRedirect("index2.php?option=$option&task=words", _GUESTBOOK_ADMIN_NOTWRITABLE);
    break;
  }

  if ($fp = fopen( $file, "w")) {
    fputs($fp,stripslashes($filecontent));
    fclose($fp);
    mosRedirect( "index2.php?option=$option&task=words", _GUESTBOOK_ADMIN_WORDSAVED );
  }
}


?>