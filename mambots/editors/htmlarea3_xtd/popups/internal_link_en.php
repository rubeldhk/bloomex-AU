<?php

// $Id: internal_link.php, v 1.02en 2004/04/02 15:44:29 bpfeifer Exp $
/**
* HTMLArea3 addon - add/edit internal and external hyperlinks
* Based on HTMLArea's original createlink function and inspired by Travis Swicegood's "Published Content" extension
* @package Mambo Open Source
* @Copyright © 2004 Bernhard Pfeifer aka novocaine
* @ All rights reserved
* @ Mambo Open Source is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.02en $
**/

define( "_VALID_MOS", 1 );
require_once( "../../../../configuration.php" );
require_once( "../../../../includes/mambo.php" );
global $database;

$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
$database->setQuery( "SELECT id AS value, CONCAT( title, ' (', title_alias, ')' ) AS text FROM #__content ORDER BY id" );
$content = $database->loadObjectList( );
$select =  "<select size=\"5\" name=\"articles\" id=\"articles\" style=\"width: 100%\" onChange=\"document.getElementById('i_href').value= homeurl + contenturl + document.getElementById('articles').value;\">\n"; 
foreach($content as $objElement) { 
	$select .= "<option value='{$objElement->value}'>{$objElement->text}</option>\n"; 
} 
$select .=  "</select>\n"; 
?>

<html>

<head>
  <title>Create/edit hyperlink</title>
  <script type="text/javascript" src="popup.js"></script>
  <script type="text/javascript">
    window.resizeTo(480, 450);
    
var homeurl = '<?php echo $mosConfig_live_site ?>';
var contenturl = '/index.php?option=content&task=view&id=';

I18N = window.opener.HTMLArea.I18N.internaldialogs;

function i18n(str) {
  return (i18n[str] || str);
};


function onTargetChanged() {
  var f = document.getElementById("i_other_target");
  if (this.value == "_other") {
    f.style.visibility = "visible";
    f.select();
    f.focus();
  } else f.style.visibility = "hidden";
};

function Init() {
  __dlg_translate(i18n);
  __dlg_init();
  var param = window.dialogArguments;
  var target_select = document.getElementById("i_target");
  if (param) {
      document.getElementById("i_href").value = param["i_href"];
      document.getElementById("i_title").value = param["i_title"];
      comboSelectValue(target_select, param["i_target"]);
      if (target_select.value != param.i_target) {
        var opt = document.createElement("option");
        opt.value = param.i_target;
        opt.innerHTML = opt.value;
        target_select.appendChild(opt);
        opt.selected = true;
      }
  }
  var opt = document.createElement("option");
  opt.value = "_other";
  opt.innerHTML = i18n("Other");
  target_select.appendChild(opt);
  target_select.onchange = onTargetChanged;
  document.getElementById("articles").focus();
  document.getElementById("i_href").select();
};

function onOK() {
var required = { 
}; 
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  // pass data back to the calling window
  var fields = ["i_href", "i_title", "i_target" ];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  if (param.i_target == "_other")
    param.i_target = document.getElementById("i_other_target").value;
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};

</script>

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: none; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}

</style>

</head>

<body onload="Init();self.focus();" onUnload="self.blur();">
<div class="title">Create/edit hyperlink</div>
<form action="" method="get">
<table border="0" style="width:100%;">
  <tr>
    <td class="label" valign="top" nowrap>Choose an article:</td>
    <td><?php echo $select; ?></td>
  </tr>
  <tr>
  <td colspan="2" align="center">or alternatively:</td>
  </tr>
  <tr>
    <td class="label"><nobr>Enter URL:</nobr></td>
    <td><input type="text" id="i_href" style="width: 100%;" /></td>
  </tr>
  <tr>
    <td class="label" nowrap>Title (optional):</td>
    <td><input type="text" id="i_title" style="width: 100%" /></td>
  </tr>
  <tr>
    <td class="label" nowrap>Target:</td>
    <td><select id="i_target">
      <option value="">Default</option>
      <option value="_blank">New window (_blank)</option>
      <option value="_self">Same frame (_self)</option>
      <option value="_top">Top frame (_top)</option>
    </select>
    <input type="text" name="i_other_target" id="i_other_target" size="10" style="visibility: hidden" />
    </td>
  </tr>
</table>

<div id="buttons">
  <button type="button" name="ok" onclick="return onOK();">OK</button>&nbsp;&nbsp;
  <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
</div>
</form>
</body>
</html>
