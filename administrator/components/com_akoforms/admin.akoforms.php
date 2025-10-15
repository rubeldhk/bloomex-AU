<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.2
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze. All rights reserved!
* @license http://www.konze.de/ Copyrighted Commercial Software
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

# Load language if it exists
if (file_exists($mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php')) {
  include($mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php');
} else {
  include($mosConfig_absolute_path.'/components/com_akoforms/languages/english.php');
}

require_once($mosConfig_absolute_path."/administrator/components/com_akoforms/config.akoforms.php");
require_once($mosConfig_absolute_path."/administrator/components/com_akoforms/class.akoforms.php");
require_once( $mainframe->getPath( 'admin_html' ) );

switch ($task) {
  case "about":
    showAbout();
    break;

  ##############################################

  case "language":
    showLanguage($option);
    break;

  case "savefile":
    saveLanguage($file, $filecontent, $option);
    break;

  ##############################################

  case "settings":
    showConfig( $option );
    break;

  case "savesettings":
    saveConfig ($option);
    break;

  ##############################################

  case "data":
    showDataForms( $option );
    break;

  case "datadetails":
    showDataFields( $option, $cid[0] );
    break;

  case "removedata":
    removeDataForms( $option, $cid );
    break;

  case "removedetails":
    removeDataFields( $option, $fid );
    break;

  case "exportdata":
    exportDataForms( $option, $cid );
    break;

  ##############################################

  case "fields":
    showFields( $option );
    break;

  case "newfields":
    editFields( $option, 0 );
    break;

  case "editfields":
    editFields( $option, $cid[0] );
    break;

  case "orderfieldsup":
    orderFields( $cid[0], -1, $option );
    break;

  case "orderfieldsdown":
    orderFields( $cid[0], 1, $option );
    break;

  case "publishfields":
    publishFields( $cid, 1, $option );
    break;

  case "unpublishfields":
    publishFields( $cid, 0, $option );
    break;

  case "savefields":
    saveFields( $option );
    break;

  case "removefields":
    removeFields( $cid, $option );
    break;

  ##############################################

  case "addmenu":
    addmenuForms( $cid, $option );
    break;

  case "orderup":
    orderForms( $cid[0], -1, $option );
    break;

  case "orderdown":
    orderForms( $cid[0], 1, $option );
    break;

  case "publish":
    publishForms( $cid, 1, $option );
    break;

  case "unpublish":
    publishForms( $cid, 0, $option );
    break;

  case "new":
    editForms( $option, 0 );
    break;

  case "edit":
    editForms( $option, $cid[0] );
    break;

  case "save":
    saveForms( $option );
    break;

  case "remove":
    removeForms( $cid, $option );
    break;

  default:
    showForms( $option );
    break;
}
echo "<p><font class='smalldark'><b>AkoForms V1.2</b> - &copy Copyright 2004 by Arthur Konze</font></p>";

############################################################################

function getbrowser($browser='[none]') {
  if ($browser=='[none]') {
    $browser = strtolower(getenv("HTTP_USER_AGENT"));
  } else {
    $browser = strtolower($browser);
  }
  // OPERA
  if (preg_match("/.*opera[\/ ](\d{1,2})\.(\d{1,2}).*/",$browser,$match)) {
    $this->browser = "Opera";
    $this->majorver = $match[1];
    $this->minorver = $match[2];
  }
  // MSIE
  elseif (preg_match("/.*msie (\d{1,2})\.(\d{1,2}).*/",$browser,$match)) {
    $this->browser = "MSIE";
    $this->majorver = $match[1];
    $this->minorver = $match[2];
  }
  // NETSCAPE 6+
  elseif (preg_match("/mozilla\/\d\.\d.*netscape6?\/(\d)\.(\d).*/",$browser,$match)) {
    $this->browser = "NN";
    $this->majorver = $match[1];
    $this->minorver = $match[2];
  }
  //MOZILLA
  elseif (preg_match("/mozilla\/\d\.\d.*rv:(\d)\.(\d(\.\d)?).*gecko/",$browser,$match)) {
    $this->browser = "Moz";
    $this->majorver = $match[1];
    $this->minorver = $match[2];
  }
  //NETSCAPE <4
  elseif (preg_match("/mozilla\/(\d).(\d{1,2})/",$browser,$match)) {
    $this->browser = "NN";
    $this->majorver = $match[1];
    $this->minorver = $match[2];
  }
  return $this;
}

function string_clean($dirtystring) {
  $dirtystring = preg_replace('/(\r|\n)/','',$dirtystring);
  $dirtystring = stripslashes($dirtystring);
  return $dirtystring;
}


############################################################################

function showAbout() {
  HTML_akoforms::showAbout();
}

############################################################################

function showLanguage($option) {

  global $mosConfig_absolute_path, $mosConfig_lang;

  if (file_exists($mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php')) {
    $file = $mosConfig_absolute_path.'/components/com_akoforms/languages/'.$mosConfig_lang.'.php';
  } else {
    $file = $mosConfig_absolute_path.'/components/com_akoforms/languages/english.php';
  }
  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
    echo "<center><h1><font color=red>Warning...</FONT></h1><BR>";
    echo "<B>You need to chmod the file to 766 in order to save your updates.</B></center><BR />";
  }

  HTML_akoforms::showFile($file,$option);
}

function saveLanguage($file, $filecontent, $option) {

  @chmod ($file, 0766);
  $permission = is_writable($file);
  if (!$permission) {
    mosRedirect("index2.php?option=$option&task=language", "File not writeable!");
    break;
  }

  if ($fp = fopen( $file, "w")) {
    fputs($fp,stripslashes($filecontent));
    fclose($fp);
    mosRedirect( "index2.php?option=$option&task=language", "Language file saved" );
  }
}

############################################################################

function showConfig( $option ) {
  $row = new mosAkoformsconfig();
  $row->bindGlobals();
  HTML_akoforms::editConfig( $option, $row );
}

function saveConfig ($option) {
  global $mosConfig_absolute_path, $AKFLANG;

  $row = new mosAkoformsconfig();
  $row->bindGlobals();
  if (!$row->bind( $_POST )) mosRedirect( "index2.php?option=$option", $row->getError() );

  $config = "<?php\n";
  $config .= $row->getVarText();
  $config .= "?>";

  if ($fp = fopen($mosConfig_absolute_path.'/administrator/components/com_akoforms/config.akoforms.php', "w")) {
    fputs($fp, $config, strlen($config));
    fclose ($fp);
    mosRedirect( "index2.php?option=$option&task=settings", $AKFLANG->AKF_SETTINGSSAVED );
  } else {
    mosRedirect( "index2.php?option=$option&task=settings", $AKFLANG->AKF_SETTINGSNOTSAVED );
  }
}

############################################################################

function showDataForms($option) {
  global $database, $mainframe, $mosConfig_absolute_path;
  $limit           = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
  $limitstart      = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
  $database->setQuery( "SELECT count(*) FROM #__akoforms" );
  $total           = $database->loadResult();
  require_once($mosConfig_absolute_path."/administrator/includes/pageNavigation.php");
  $pageNav         = new mosPageNav( $total, $limitstart, $limit );
  $sql = "SELECT a.*, COUNT(DISTINCT b.id) AS noentries, COUNT(DISTINCT c.id) AS nofields"
  . "\nFROM #__akoforms AS a"
  . "\nLEFT JOIN #__akoforms_sender AS b ON b.formid = a.id"
  . "\nLEFT JOIN #__akoforms_fields AS c ON c.catid = a.id"
  . "\nGROUP BY a.id"
  . "\nLIMIT $pageNav->limitstart,$pageNav->limit";
  $database->setQuery($sql);
  if(!$result = $database->query()) {
    echo $database->stderr();
    return false;
  }
  $rows = $database->loadObjectList();
  HTML_akoforms::showDataForms( $rows, $pageNav, $option );
}

function showDataFields($option, $catid) {
  global $database, $mainframe, $AKFLANG;

  $limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
  $limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

  // get the total number of records
  $database->setQuery( "SELECT count(*) FROM #__akoforms_sender AS a WHERE formid=$catid" );
  $total = $database->loadResult();
  echo $database->getErrorMsg();

  include_once( "includes/pageNavigation.php" );
  $pageNav = new mosPageNav( $total, $limitstart, $limit  );

  // Compile list of fields
  $database->setQuery( "SELECT * FROM #__akoforms_fields WHERE catid=$catid" );
  $fieldsrows = $database->loadObjectList();
  if ($database->getErrorNum()) {
    echo $database->stderr();
    return false;
  }
  foreach ($fieldsrows as $fieldsrow) {
    $fieldrowid[]    = $fieldsrow->id;
    $fieldrowtitle[] = $fieldsrow->title;
  }

  // Get all entries by sender
  $database->setQuery( "SELECT * FROM #__akoforms_sender WHERE formid=$catid ORDER BY senderdate DESC LIMIT $pageNav->limitstart,$pageNav->limit" );
  $senderrows = $database->loadObjectList();
  if ($database->getErrorNum()) {
    echo $database->stderr();
    return false;
  }

  // Get all needed data
  foreach($senderrows as $srow) {
    $senderlist[] = $srow->id;
  }
  $senderlist = implode( ',', $senderlist );
  $database->setQuery( "SELECT * FROM #__akoforms_data WHERE senderid IN ($senderlist)" );
  $datarows = $database->loadObjectList();
  if ($database->getErrorNum()) {
    echo $database->stderr();
    return false;
  }
  foreach($datarows as $drow) {
    $datarray[$drow->senderid][$drow->fieldid] = $drow->data;
  }

  HTML_akoforms::showDataFields( $option, $pageNav, $senderrows, $fieldrowid, $fieldrowtitle, $datarray );
}

function removeDataForms( $option, $cid ) {
  global $database;
  $cids = implode( ',', $cid );
  # Get Sender to remove
  $database->setQuery( "SELECT id FROM #__akoforms_sender WHERE formid IN ($cids)" );
  $rows = $database->loadObjectList();
  foreach ($rows as $row) {
    $remsender[] = $row->id;
  }
  $remsenders = implode( ',', $remsender );
  # Remove all senders
  $database->setQuery( "DELETE FROM #__akoforms_sender WHERE formid IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  # Remove all submitted data
  $database->setQuery( "DELETE FROM #__akoforms_data WHERE senderid IN ($remsenders)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  mosRedirect( "index2.php?option=$option&task=data" );
}

function removeDataFields( $option, $fid ) {
  global $database;
  $cids = implode( ',', $fid );
  # Remove all senders
  $database->setQuery( "DELETE FROM #__akoforms_sender WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  # Remove all submitted data
  $database->setQuery( "DELETE FROM #__akoforms_data WHERE senderid IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  mosRedirect( "index2.php?option=$option&task=data" );
}

function exportDataForms( $option, $cids ) {
  global $database, $AKFLANG;
  $separator = ";";

  foreach ($cids as $cid) {
    // Compile list of headersfields
    $database->setQuery( "SELECT id, title FROM #__akoforms_fields WHERE catid=$cid ORDER BY ordering" );
    $fieldsrows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }
    foreach ($fieldsrows as $fieldsrow) {
      $fieldrowid[]    = $fieldsrow->id;
      $fieldrowtitle[] = $fieldsrow->title;
    }
    $csvfile .= $AKFLANG->AKF_STOREDIP.$separator.$AKFLANG->AKF_STOREDDATE.$separator.implode($separator,$fieldrowtitle);
    // Get all entries by sender
    $database->setQuery( "SELECT * FROM #__akoforms_sender WHERE formid=$cid ORDER BY senderdate DESC" );
    $senderrows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }
    // Get all needed data
    foreach($senderrows as $srow) {
      $arsenderlist[] = $srow->id;
    }
    $senderlist = implode( ',', $arsenderlist );
    $database->setQuery( "SELECT * FROM #__akoforms_data WHERE senderid IN ($senderlist)" );
    $datarows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }
    foreach($datarows as $drow) {
      $datarray[$drow->senderid][$drow->fieldid] = string_clean($drow->data);
    }
    // Compile CSV Data Part
    foreach($senderrows as $row) {
      $thisformid = $row->formid;
      $csvfile .= "\r\n".$row->senderip.$separator.$row->senderdate.$separator;
      foreach($fieldrowid as $frid) {
        $tmparray[] = $datarray[$row->id][$frid];
      }
      $csvfile .= implode($separator,$tmparray);
      unset($tmparray);
    }
    $csvfile .= "\r\n";
    unset($arsenderlist);
    unset($fieldrowid);
    unset($fieldrowtitle);
  }

  // Send file to browser
  $browser = getbrowser();
  if ($browser->browser=='MSIE' OR $browser->browser=='Opera') {
    $contenttype = "application/octetstream";
  } else {
    $contenttype = "application/force-download";
  }
  @ob_end_clean();
  @ini_set('zlib.output_compression', 'Off');

  header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header("Cache-Control: private");
  header("Content-Type: ".$contenttype);
  header("Content-Disposition: attachment; filename=\"akoforms.csv\"");
  header("Content-Length: ".strlen($csvfile));
  echo $csvfile;
  exit;
}


############################################################################

function showFields($option) {
  global $database, $mainframe, $AKFLANG;

  $catid = $mainframe->getUserStateFromRequest( "catid{$option}", 'catid', 0 );
  $limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
  $limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

  $where = array();

  if ($catid > 0) {
    $where[] = "a.catid='$catid'";
  }

  // get the total number of records
  $database->setQuery( "SELECT count(*) FROM #__akoforms_fields AS a"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
  );
  $total = $database->loadResult();
  echo $database->getErrorMsg();

  include_once( "includes/pageNavigation.php" );
  $pageNav = new mosPageNav( $total, $limitstart, $limit  );

  $database->setQuery( "SELECT a.*, cc.title AS category"
    . "\nFROM #__akoforms_fields AS a"
    . "\nLEFT JOIN #__akoforms AS cc ON cc.id = a.catid"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY a.catid, a.ordering"
    . "\nLIMIT $pageNav->limitstart,$pageNav->limit"
  );

  $rows = $database->loadObjectList();
  if ($database->getErrorNum()) {
    echo $database->stderr();
    return false;
  }

  # Compile list of all forms
  $categories[] = mosHTML::makeOption( '0', $AKFLANG->AKF_SELECTFORM );
  $categories[] = mosHTML::makeOption( '-1', $AKFLANG->AKF_ALLFORMS );
  $database->setQuery( "SELECT id AS value, title AS text FROM #__akoforms ORDER BY ordering" );
  $categories = array_merge( $categories, $database->loadObjectList() );
  $lists['categories'] = mosHTML::selectList( $categories, 'catid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $catid );

  HTML_akoforms::showFields( $option, $rows, $lists, $pageNav );
}


function editFields( $option, $uid ) {
  global $database, $mosConfig_absolute_path, $AKFLANG;
  require_once($mosConfig_absolute_path."/administrator/components/com_akoforms/fields.akoforms.php");
  $row = new mosAkoformsfields( $database );
  $row->load( $uid );
  # Compile list of forms
  $forms[] = mosHTML::makeOption( '0', $AKFLANG->AKF_SELECTFORM );
  $database->setQuery( "SELECT id AS value, title AS text FROM #__akoforms ORDER BY ordering" );
  $forms = array_merge( $forms, $database->loadObjectList() );
  if (count( $forms ) < 2) mosRedirect( "index2.php?option=$option", 'You must add a form first.' );
  $lists['forms'] = mosHTML::selectList( $forms, 'catid', 'class="inputbox" size="1" style="width:300px"', 'value', 'text', intval( $row->catid ) );
  # Prepare dates for publishing
  if ($uid) {
    if (trim( $row->publish_down ) == "0000-00-00 00:00:00") {
      $row->publish_down = "Never";
    }
  } else {
    $row->ordering     = 9999;
    $row->required     = 0;
    $row->published    = 0;
    $row->publish_up   = date( "Y-m-d", time() );
    $row->publish_down = "Never";
  }
  # Compile list for ordering
  if ($uid) {
    $order = mosGetOrderingList( "SELECT ordering AS value, title AS text FROM #__akoforms_fields WHERE catid='$row->catid' ORDER BY ordering" );
    $lists['ordering'] = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1" style="width:300px"', 'value', 'text', intval( $row->ordering ) );
  } else {
    $lists['ordering'] = "<input type=\"hidden\" name=\"ordering\" value=\"$row->ordering\" />New fields go to the last place.";
  }
  # Compile list with types of fields
  $fieldlist[] = mosHTML::makeOption( '0', $AKFLANG->AKF_SELECTFIELD );
  foreach ($formfields as $fieldkey => $singlefield)
    $fieldlist[] = mosHTML::makeOption( $fieldkey , $singlefield[field_title] );
  $lists['fieldtypes'] = mosHTML::selectList( $fieldlist, 'type', 'class="inputbox" size="1" style="width:300px"', 'value', 'text', $row->type );
  # Compile list for required fields
  $yesno[]             = mosHTML::makeOption( '0', $AKFLANG->AKF_NO );
  $yesno[]             = mosHTML::makeOption( '1', $AKFLANG->AKF_YES );
  $lists['required']   = mosHTML::yesnoRadioList( 'required', 'class="inputbox"', $row->required );
  # Bring it on the screen
  HTML_akoforms::editFields( $option, $row, $lists );
}

function orderFields( $uid, $inc, $option ) {
  global $database;
  $row = new mosAkoformsfields( $database );
  $row->load( $uid );
  $row->move( $inc, "published >= 0" );
  mosRedirect( "index2.php?option=$option&task=fields" );
}

function publishFields( $cid=null, $publish=1,  $option ) {
  global $database;
  $cids = implode( ',', $cid );
  $database->setQuery( "UPDATE #__akoforms_fields SET published='$publish' WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    exit();
  }
  mosRedirect( "index2.php?option=$option&task=fields" );
}

function removeFields( $cid, $option ) {
  global $database;
  $cids = implode( ',', $cid );
  $database->setQuery( "DELETE FROM #__akoforms_fields WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  mosRedirect( "index2.php?option=$option&task=fields" );
}

function saveFields( $option ) {
  global $database;
  $row = new mosAkoformsfields( $database );
  if (!$row->bind( $_POST )) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  if (!$row->store()) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  $row->updateOrder( "catid='$row->catid'" );
  mosRedirect( "index2.php?option=$option&task=fields" );
}

############################################################################

function addmenuForms( $cid, $option ) {
  global $database;
  foreach($cid as $formid) {
    $database->setQuery("SELECT title from #__akoforms WHERE id='$formid'");
    $formtitle = $database->loadResult();
    $database->setQuery( "INSERT INTO #__menu (`id`,`menutype`,`name`,`link`,`type`,`published`,`parent`,`componentid`,`sublevel`,`ordering`,`checked_out`,`checked_out_time`,`pollid`,`browserNav`,`access`,`utaccess`,`params`)"
                        ."\n VALUES ('','mainmenu','".$formtitle."','index.php?option=com_akoforms&func=showform&formid=".$formid."','url','0','0','0','0','9999','0','0000-00-00 00:00:00','0','0','0','3','');" );
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    }
  }
  echo "<SCRIPT>alert('Form added to Mainmenu'); document.location.href='index2.php?option=$option';</SCRIPT>";
}

function orderForms( $uid, $inc, $option ) {
  global $database;
  $row = new mosAkoforms( $database );
  $row->load( $uid );
  $row->move( $inc, "published >= 0" );
  mosRedirect( "index2.php?option=$option" );
}

function publishForms( $cid=null, $publish=1,  $option ) {
  global $database;
  $cids = implode( ',', $cid );
  $database->setQuery( "UPDATE #__akoforms SET published='$publish' WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
    exit();
  }
  mosRedirect( "index2.php?option=$option" );
}

function removeForms( $cid, $option ) {
  global $database;
  $cids = implode( ',', $cid );
  $database->setQuery( "DELETE FROM #__akoforms WHERE id IN ($cids)" );
  if (!$database->query()) {
    echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  }
  mosRedirect( "index2.php?option=$option" );
}

function editForms( $option, $uid ) {
  global $database, $mosConfig_absolute_path, $AKFLANG;
  $lists = array();
  # oop database connector
  $row = new mosAkoforms( $database );
  # load the row from the db table
  $row->load( $uid );
  # Prepare dates for publishing
  if ($uid) {
    if (trim( $row->publish_down ) == "0000-00-00 00:00:00") {
      $row->publish_down = "Never";
    }
  } else {
    $row->sendmail     = 1;
    $row->savedb       = 0;
    $row->showresult   = 1;
    $row->published    = 0;
    $row->ordering     = 9999;
    $row->target       = 0;
    $row->publish_up   = date( "Y-m-d", time() );
    $row->publish_down = "Never";
  }
  # Build some needed yes/no lists
  $yesno[]             = mosHTML::makeOption( '0', $AKFLANG->AKF_NO );
  $yesno[]             = mosHTML::makeOption( '1', $AKFLANG->AKF_YES );

  $lists['sendmail']   = mosHTML::yesnoRadioList( 'sendmail', 'class="inputbox"', $row->sendmail );
  $lists['savedb']     = mosHTML::yesnoRadioList( 'savedb', 'class="inputbox"', $row->savedb );
  $lists['showresult'] = mosHTML::yesnoRadioList( 'showresult', 'class="inputbox"', $row->showresult );
  $formtarget[]        = mosHTML::makeOption( '0', $AKFLANG->AKF_SHOWENDPAGE );
  $formtarget[]        = mosHTML::makeOption( '1', $AKFLANG->AKF_REDIRECTTOURL );
  $lists['target']     = mosHTML::selectList( $formtarget, 'target', 'class="inputbox" size="2" style="width:300px"', 'value', 'text', $row->target );
  # Build up list for ordering
  if ($uid) {
    $order = mosGetOrderingList( "SELECT ordering AS value, title AS text FROM #__akoforms ORDER BY ordering" );
    $lists['ordering'] = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1" style="width:300px"', 'value', 'text', intval( $row->ordering ) );
  } else {
    $lists['ordering'] = "<input type=\"hidden\" name=\"ordering\" value=\"$row->ordering\" />".$AKFLANG->AKF_NEWFORMSLAST;
  }
  HTML_akoforms::editForms( $option, $row, $lists);
}

function saveForms( $option ) {
  global $database;
  $row = new mosAkoforms( $database );
  if (!$row->bind( $_POST )) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  if (!$row->store()) {
    echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
    exit();
  }
  $row->updateOrder( "published >= 0" );
  mosRedirect( "index2.php?option=$option" );
}

function showForms($option) {
  global $database, $mainframe, $mosConfig_absolute_path;
  $limit           = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
  $limitstart      = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
  $database->setQuery( "SELECT count(*) FROM #__akoforms" );
  $total           = $database->loadResult();
  require_once($mosConfig_absolute_path."/administrator/includes/pageNavigation.php");
  $pageNav         = new mosPageNav( $total, $limitstart, $limit );
  $sql = "SELECT a.*, COUNT(DISTINCT b.id) AS nofields"
  . "\nFROM #__akoforms AS a"
  . "\nLEFT JOIN #__akoforms_fields AS b ON b.catid = a.id"
  . "\nGROUP BY a.id"
  . "\nLIMIT $pageNav->limitstart,$pageNav->limit";
  $database->setQuery($sql);
  if(!$result = $database->query()) {
    echo $database->stderr();
    return false;
  }
  $rows = $database->loadObjectList();
  HTML_akoforms::showForms( $rows, $pageNav, $option );
}


?>