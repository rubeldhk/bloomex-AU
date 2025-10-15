<?php
/**
* HTMLArea3 addon - add/edit snapshots of products in your mambo-phpShop
* Remember that an installed mambo-phpShop is required ;-)
* @package mambo-phpShop
* @Copyright © 2005 Soeren Eberhardt
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @version $Id: product_snapshot.php, v 1.0 2004/06/28 15:44:29 soeren_nb Exp $
**/


/** Set flag that this is a parent file */
define( "_VALID_MOS", 1 );

$base_path = "../../../..";
global $perm, $mosConfig_live_site, $mosConfig_absolute_path, $my;

$_REQUEST['option'] = 'com_virtuemart';

require_once( dirname( __FILE__ ) ."/auth.php" );

chdir($mosConfig_absolute_path);

require_once( $mosConfig_absolute_path."/components/com_virtuemart/virtuemart_parser.php" );

if( $perm->check("admin,storeadmin" )) {

  $database->setQuery( "SELECT product_name, product_id, product_thumb_image,product_sku, CONCAT( product_name, ' (', product_sku, ')' ) AS text FROM #__vm_product ORDER BY product_name" );
  $products = $database->loadObjectList();
  $select =  "<select size=\"5\" 
  onchange=\"javascript:
  if (document.forms[0].products.options[selectedIndex].value != '') {
    var id = document.forms[0].products.options[selectedIndex].value;
    var thumb = document.getElementById(id).value;
    if (thumb=='')
      document.imagelib.src='".IMAGEURL.NO_IMAGE."';
    else
      document.imagelib.src='".IMAGEURL."product/' + thumb; 
  } 
  else {
    document.imagelib.src='".IMAGEURL.NO_IMAGE."'
  }\" name=\"products\" id=\"i_product_id\"  >\n"; 
  $hidden_thumb_image = "";
  $i = 0;
  foreach($products as $objElement) {
    $select .= "<option value='{$objElement->product_id}'>{$objElement->text}</option>\n"; 
    $hidden_thumb_image.= "<input type='hidden' name='thumbs' id='{$objElement->product_id}' value='{$objElement->product_thumb_image}' />";
  } 
  $select .=  "</select>\n";
  ?>
  <html>
  <head>
    <title>Create/edit Product Snapshot</title>
    <script type="text/javascript" src="popup.js"></script>
    <script type="text/javascript">
      window.resizeTo(680, 450);
      
  var homeurl = '<?php echo $mosConfig_live_site ?>';
  
  I18N = window.opener.HTMLArea.I18N.internaldialogs;
  
  function i18n(str) {
    return (i18n[str] || str);
  };
  
  function Init() {
    __dlg_translate(i18n);
    __dlg_init();
    var param = window.dialogArguments;
    var product_select = document.getElementById("i_product_id");
    if (param) {
        document.getElementById("i_showprice").checked = param["i_showprice"]=='true' ? true : false;
        document.getElementById("i_showdesc").checked = param["i_showdesc"]=='true' ? true : false;
        document.getElementById("i_showaddtocart").checked = param["i_showaddtocart"]=='true' ? true : false;
        
        var product_id = param["i_product_id"].slice(1,param["i_product_id"].length);
        if(product_id) {
            var thumb = document.getElementById(product_id).value;
            document.imagelib.src='<?php echo IMAGEURL ?>product/' + thumb; 
        }
        comboSelectValue(product_select, product_id);
        comboSelectValue(document.getElementById("i_align"), param["i_align"]);
    }
  
    document.getElementById("i_product_id").focus();
  
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
    var fields = ["i_showprice", "i_showdesc", "i_showaddtocart", "i_align", "i_product_id" ];
    var param = new Object();
    for (var i in fields) {
      var id = fields[i];
      var el = document.getElementById(id);
      param[id] = el.value;
    }
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
  table .label { width: 8em; }
  
  .title { background: none; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
  border-bottom: 1px solid black; letter-spacing: 2px;
  }
  
  #buttons {
        margin-top: 1em; border-top: 1px solid #999;
        padding: 2px; text-align: center;
  }
  
  </style>
  
  </head>
  
  <body onload="Init();self.focus();" onUnload="self.blur();">
  <div class="title">Create/edit Product Snapshot</div>
  <form action="" method="get">
  <?php echo $hidden_thumb_image ?>
  <table border="0" style="width: 100%;">
    <tr>
      <td class="label" valign="top" nowrap>Choose a product:</td>
      <td><?php echo $select; ?></td>
      <td rowspan="5">
        <img src="<?php echo IMAGEURL .NO_IMAGE; ?>" name="imagelib" border="0" alt="Preview" />
      </td>
    </tr>
    <tr>
      <td class="label" nowrap><label for="i_showprice">Show Product Price?:</label></td>
      <td align="left"><input value="1" type="checkbox" onchange="if(document.getElementById('i_showprice').checked==true) document.getElementById('i_showprice').value=1; else document.getElementById('i_showprice').value=0;" id="i_showprice" /></td>
    </tr>
      <tr>
      <td class="label" nowrap><label for="i_showdesc">Show Product Description?:</label></td>
      <td align="left"><input value="1" type="checkbox" onchange="if(document.getElementById('i_showdesc').checked==true) document.getElementById('i_showdesc').value=1; else document.getElementById('i_showdesc').value=0;" id="i_showdesc" /></td>
    </tr>
      <tr>
      <td class="label" nowrap><label for="i_showaddtocart">Show "Add-to-Cart"-Link ?:</label></td>
      <td align="left"><input value="1" type="checkbox" onchange="if(document.getElementById('i_showaddtocart').checked==true) document.getElementById('i_showaddtocart').value=1; else document.getElementById('i_showaddtocart').value=0;" id="i_showaddtocart" /></td>
    </tr>
    <tr>
      <td class="label" nowrap>Align:</td>
      <td><select id="i_align" name="align">
        <option value="">Default</option>
        <option value="left" selected="selected">Left</option>
        <option value="center">Center</option>
        <option value="right">Right</option>
      </select>
      </td>
    </tr>
  </table>
  
  <div id="buttons">
    <button type="button" name="ok" onclick="return onOK();">OK</button>&nbsp;&nbsp;
    <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
  </div>
  </form>
  <script language="javascript1.2" type="text/javascript">
  document.getElementById("i_showprice").value = document.getElementById("i_showprice").checked ? "1" : "0";
  document.getElementById("i_showdesc").value = document.getElementById("i_showdesc").checked  ? "1" : "0";
  document.getElementById("i_showaddtocart").value = document.getElementById("i_showaddtocart").checked ? "1" : "0";
  </script>
  </body>
  </html>
<?php
  }
  ?>
