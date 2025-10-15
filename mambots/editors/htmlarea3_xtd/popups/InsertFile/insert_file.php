<?php
// $Id: insert_file.php, v 1.01.5 2004/04/19 11:53:40 bpfeifer Exp $
/**
* HTMLArea3 XTD addon - InsertFile
* Based on Xiang Wei Zhuo's ImageManager and Al Rashid's File Manager
* @package Mambo Open Source
* @Copyright � 2004 Bernhard Pfeifer aka novocaine
* @ All rights reserved
* @ Mambo Open Source is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.01.5 $
**/
define( "_VALID_MOS", 1 );
$base_path = "../../../../..";

require_once( dirname( __FILE__ ) ."/auth.php" );

if ($my->id && ($my->gid == 2 || ($my->gid >= 19 && $my->gid <= 25))) {

	require('config.inc.php');
	?>
	
	<html style="width: 750; height: 440;">
	<head>
	<title>Insert File</title>
	<script type="text/javascript" src="../popup.js"></script>
	<script type="text/javascript" src="../../dialog.js"></script>
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
	form p {
	  margin-top: 5px;
	  margin-bottom: 5px;
	}
	.fl { width: 9em; float: left; padding: 2px 5px; text-align: right; }
	.fr { width: 6em; float: left; padding: 2px 5px; text-align: right; }
	fieldset { padding: 0px 10px 5px 5px; }
	select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
	button { width: 70px; }
	.space { padding: 2px; }
	
	.title { background: none; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
	border-bottom: 1px solid black; letter-spacing: 2px;
	}
	form { padding: 0px; margin: 0px; }
	</style>
	
	<script language="JavaScript" type="text/JavaScript">
	window.resizeTo(750, 440);
	var preview_window = null;
	
	function Init() {
	  __dlg_init();
	  var param = window.dialogArguments;
	   if (param) {
	      document.getElementById("f_url").value = param["f_url"];
	      document.getElementById("f_caption").value = param["f_caption"];
	      document.getElementById("f_addicon").value = param["f_addicon"];
	      document.getElementById("f_addsize").value = param["f_addsize"];
	      document.getElementById("f_adddate").value = param["f_adddate"];
	      document.getElementById("f_icon").value = param["f_icon"];
	      document.getElementById("f_date").value = param["f_date"];
	      document.getElementById("f_size").value = param["f_size"];
	    }
	document.getElementById("f_url").focus();
	};
	
	function onOK() {
	  var required = {
	    "f_url": "<?php echo $MY_MESSAGES['enterurl']; ?>",
	    "f_caption": "<?php echo $MY_MESSAGES['entercaption']; ?>"
	  };
	  for (var i in required) {
	    var el = MM_findObj(i);
	    if (!el.value) {
	      alert(required[i]);
	      el.focus();
	      return false;
	    }
	  }
	  // pass data back to the calling window
	  var fields = ["f_url","f_caption", "f_addicon", "f_addsize", "f_adddate", "f_icon", "f_date", "f_size"];
	  var param = new Object();
	  	param["f_addicon"] = false;
	  	param["f_addicon"] = false;
	  	
	  for (var i in fields) {
	  	var id = fields[i];
			var el = MM_findObj(id);
			param[id] = el.value;
				if (id == "f_addsize" || id =="f_addicon" || id =="f_adddate") {
						(el.checked) ?  param[id] = true : 	param[id] = false;
					 	}
				}
		
	  if (preview_window) {
	  	preview_window.close();
	  }
	  __dlg_close(param);
	  return false;
	};
	
	function onCancel() {
	  if (preview_window) {
	    preview_window.close();
	  }
	  __dlg_close(null);
	  return false;
	};
	
	function pviiClassNew(obj, new_style) { //v2.6 by PVII
	  obj.className=new_style;
	}
	
	function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}
	
	function P7_Snap() { //v2.62 by PVII
	  var x,y,ox,bx,oy,p,tx,a,b,k,d,da,e,el,args=P7_Snap.arguments;a=parseInt(a);
	  for (k=0; k<(args.length-3); k+=4)
	   if ((g=MM_findObj(args[k]))!=null) {
	    el=eval(MM_findObj(args[k+1]));
	    a=parseInt(args[k+2]);b=parseInt(args[k+3]);
	    x=0;y=0;ox=0;oy=0;p="";tx=1;da="document.all['"+args[k]+"']";
	    if(document.getElementById) {
	     d="document.getElementsByName('"+args[k]+"')[0]";
	     if(!eval(d)) {d="document.getElementById('"+args[k]+"')";if(!eval(d)) {d=da;}}
	    }else if(document.all) {d=da;}
	    if (document.all || document.getElementById) {
	     while (tx==1) {p+=".offsetParent";
	      if(eval(d+p)) {x+=parseInt(eval(d+p+".offsetLeft"));y+=parseInt(eval(d+p+".offsetTop"));
	      }else{tx=0;}}
	     ox=parseInt(g.offsetLeft);oy=parseInt(g.offsetTop);var tw=x+ox+y+oy;
	     if(tw==0 || (navigator.appVersion.indexOf("MSIE 4")>-1 && navigator.appVersion.indexOf("Mac")>-1)) {
	      ox=0;oy=0;if(g.style.left){x=parseInt(g.style.left);y=parseInt(g.style.top);
	      }else{var w1=parseInt(el.style.width);bx=(a<0)?-5-w1:-10;
	      a=(Math.abs(a)<1000)?0:a;b=(Math.abs(b)<1000)?0:b;
	      x=document.body.scrollLeft + event.clientX + bx;
	      y=document.body.scrollTop + event.clientY;}}
	   }else if (document.layers) {x=g.x;y=g.y;var q0=document.layers,dd="";
	    for(var s=0;s<q0.length;s++) {dd='document.'+q0[s].name;
	     if(eval(dd+'.document.'+args[k])) {x+=eval(dd+'.left');y+=eval(dd+'.top');break;}}}
	   if(el) {e=(document.layers)?el:el.style;
	   var xx=parseInt(x+ox+a),yy=parseInt(y+oy+b);
	   if(navigator.appName=="Netscape" && parseInt(navigator.appVersion)>4){xx+="px";yy+="px";}
	   if(navigator.appVersion.indexOf("MSIE 5")>-1 && navigator.appVersion.indexOf("Mac")>-1){
	    xx+=parseInt(document.body.leftMargin);yy+=parseInt(document.body.topMargin);
	    xx+="px";yy+="px";}e.left=xx;e.top=yy;}}
	}
	
	function MM_showHideLayers() { //v6.0
	  var i,p,v,obj,args=MM_showHideLayers.arguments;
	  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
	    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
	    obj.visibility=v; }
	}
	
	function changeLoadingStatus(state) {
		var statusText = null;
		if(state == 'load') {
			statusText = '<?php echo $MY_MESSAGES['loading']; ?> ';
		}
		else if(state == 'upload') {
			statusText = '<?php echo $MY_MESSAGES['uploading']; ?>';
		}
		if(statusText != null) {
			var obj = MM_findObj('loadingStatus');
			if (obj != null && obj.innerHTML != null)
				obj.innerHTML = statusText;
			MM_showHideLayers('loading','','show')
		}
	}
	
	function goUpDir() {
		var selection = document.forms[0].path;
		var dir = selection.options[selection.selectedIndex].value;
		if(dir != '/'){
			fileManager.goUp();	
			changeLoadingStatus('load');
		}	
	}
	
	function changeDir(selection) {
		var newDir = selection.options[selection.selectedIndex].value;
		fileManager.changeDir(newDir);
		changeLoadingStatus('load');
	}
	
	function newFolder() {
		var selection = document.forms[0].path;
		var path = selection.options[selection.selectedIndex].value;
		/*
		Dialog("newfolder.html", function(param) {
			if (!param) {	// user must have pressed Cancel
				return false;
			} else {
				var folder = param['f_foldername'];
				if (folder && folder != '') {
					fileManager.newFolder(path,folder);
				}
			}
		}, null);
		*/
		var folder = prompt('<?php echo $MY_MESSAGES['newfolder']; ?>','');
		if (folder)  		fileManager.newFolder(path,folder);
		return false
	}
	
	function doUpload() {
		var fileObj = MM_findObj('uploadFile');
		if (fileObj == null) return false;
		var regexp = /\/|\\/;
		var parts = fileObj.value.split(regexp);
		var filename = parts[parts.length-1].split(".");
		if (filename.length <= 1) {
			alert('<?php echo $MY_MESSAGES['extmissing']; ?>');
			return false;
		}
		var ext = filename[filename.length-1].toLowerCase();
	
		<?php
		
		if (is_array($MY_DENY_EXTENSIONS)) {
			echo 'var DenyExtensions = [';
			foreach($MY_DENY_EXTENSIONS as $value) echo '"'.$value.'", ';
			echo '""];
			for (i=0; i<DenyExtensions.length; i++) {
					if (ext == DenyExtensions[i]) {
						alert(\''.$MY_MESSAGES['extnotallowed'].'\');
						return false;
					}
			}';
		}
	
		if (is_array($MY_ALLOW_EXTENSIONS)) {
			echo 'var AllowExtensions = [';
			foreach($MY_ALLOW_EXTENSIONS as $value) echo '"'.$value.'", ';
			echo '""];
			for (i=0; i<AllowExtensions.length; i++) {
					if (ext == AllowExtensions[i]) {
						changeLoadingStatus(\'upload\');
						return true;
					}
			}
			alert(\''.$MY_MESSAGES['extnotallowed'].'\');
			return false;
			';
		} else {
			echo 'changeLoadingStatus(\'upload\');'."\n";
			echo 'return true'."\n";
		}
	?>
	}
	
	function refreshPath(){
		var selection = document.forms[0].path;
		changeDir(selection);
	}
	</script>
	</head>
	<body onload="Init(); P7_Snap('path','loading',120,70);">
	<div class="title"><?php echo $MY_MESSAGES['insertfile']; ?></div>
	<form action="files.php?dialogname=<?php echo $MY_NAME; ?>" name="form1" method="post" target="fileManager" enctype="multipart/form-data">
	<div id="loading" style="position:absolute; left:200px; top:130px; width:184px; height:48px; z-index:1" class="statusLayer">
	  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	      <td><div id= "loadingStatus" align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif; z-index:2;  ">
		        <?php echo $MY_MESSAGES['loading']; ?>
			</div></td>
	    </tr>
	  </table>
	</div>
	  <table width="100%" border="0" align="center" cellspacing="2" cellpadding="2">
	    <tr>
	      <td align="center">	  <fieldset>
		  <legend><?php echo $MY_MESSAGES['filemanager']; ?></legend>
	        <table width="99%" align="center" border="0" cellspacing="2" cellpadding="2">
	          <tr>
	            <td><table border="0" cellspacing="1" cellpadding="3">
	                <tr> 
	                  <td><?php echo $MY_MESSAGES['directory']; ?>:</td>
	                  <td>
			  <select name="path" id="path" style="width:35em" onChange="changeDir(this)">
				  <option value="/">/</option>
			</select>
			  </td>
	                  <td class="buttonOut" onMouseOver="pviiClassNew(this,'buttonHover')" onMouseOut="pviiClassNew(this,'buttonOut')">
					  <a href="#" onClick="javascript:goUpDir();"><img src="images/btnFolderUp.gif" width="15" height="15" border="0" alt="Up"></a></td>
	<?php if ($MY_ALLOW_CREATE_FOLDER) { ?>
	                  <!-- td><div class="separator"></div></td>
	                  <td class="buttonOut" onMouseOver="pviiClassNew(this,'buttonHover')" onMouseOut="pviiClassNew(this,'buttonOut')">
					  <a href="#" onClick="javascript:newFolder();"><img src="images/btnFolderNew.gif" width="15" height="15" border="0" alt="New Folder"></a></td -->
	<?php } ?>
	                </tr>
	              </table></td>
	          </tr>
	          <tr>
	            <td align="center" style="background:ButtonFace;"><div name="manager" class="manager">
	        <iframe src="files.php?dialogname=<?php echo $MY_NAME; ?>&refresh=1" name="fileManager" id="fileManager" style="width:100%;height:100%;" marginwidth="0" marginheight="0" align="top" scrolling="no" frameborder="0" hspace="0" vspace="0"  style="background: Window;"></iframe>
			</div>
				</td>
	            
	          </tr>
	        </table>
	       <?php if ($MY_ALLOW_UPLOAD_FILE) { ?>
			<table border="0" align="center" cellpadding="2" cellspacing="2">
	          <tr>
				 <td><div align="right"><?php echo $MY_MESSAGES['upload']; ?>:</div></td>
	           	<td><input name="uploadFile" type="file" id="uploadFile" size="72">
	             <input type="submit" style="width:5em" value="<?php echo $MY_MESSAGES['upload']; ?>" onClick="javascript:return doUpload();" /></td>
	             </tr></table>
				<?php } ?>
	
	        </fieldset></td>
	    </tr>
	    <tr>
	      <td>
	
	
		  <table border="0" cellpadding="2" cellspacing="2">
	          <tr> 
	            <td nowrap><?php echo $MY_MESSAGES['url']; ?>:</td>
	            <td><input name="url" id="f_url" type="text" style="width:20em" size="30"></td>
							<td nowrap><?php echo $MY_MESSAGES['caption']; ?>:</td>
							<td><input name="caption" id="f_caption" type="text" style="width:20em" size="30"></td>
	           </tr>
			</table>
		  <table border="0" cellpadding="2" cellspacing="2">
	          <tr> 
	            <td>
	            	<input id="f_addicon" type="checkbox" >
	            	<input id="f_icon" type="hidden" value="" >
	            	<?php echo $MY_MESSAGES['inserticon']; ?>
	            </td>
	        		<td>
	        			<input id="f_addsize" type="checkbox" >
	        			<input id="f_size" type="hidden" value="" >
	        			<?php echo $MY_MESSAGES['insertsize']; ?>
	        		</td>
	        		<td>
	        			<input id="f_adddate" type="checkbox" >
	        			<input id="f_date" type="hidden" value="" >
	        			<?php echo $MY_MESSAGES['insertdate']; ?>
	        		</td>
					</tr>
	      </table>
	      </td>
	    </tr>
	    <tr>
	      <td>
	      <table width="100%" border="0" cellpadding="0" cellspacing="0">
	      		<tr>
	      			<td colspan="2"><hr /></td>
	      		</tr>
	      		<tr>
	      			<td><button type="button" name="ok" onclick="return refreshPath();"><?php echo $MY_MESSAGES['refresh']; ?></button></td>
	      			<td align="right"><button type="button" name="ok" onclick="return onOK();">OK</button>&nbsp;&nbsp;<button type="button" name="cancel" onclick="return onCancel();"><?php echo $MY_MESSAGES['cancel']; ?></button></td>
	      		</tr>
	      	</table>
	    	</td>
	    </tr>
	  </table>
	</form>
	</body>
	</html>
<?php
}
?>
