<?php
/**
* swmenupro v5.5
* http://swmenupro.com
* Copyright 2006 Sean White
**/

//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE); 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


require_once($mosConfig_absolute_path ."/modules/mod_swmenupro/styles.php");
require_once($mosConfig_absolute_path ."/modules/mod_swmenupro/functions.php");

$css_load=0;
$preview=0;
$id=0;
$q = explode("&",$_SERVER["QUERY_STRING"]);
foreach ($q as $qi)
{
  if ($qi != "")
  {
    $qa = explode("=",$qi);
    list ($key, $val) = $qa;
    if ($val)
      $$key = urldecode($val);
  }
}
 
reset ($_POST);
while (list ($key, $val) = each ($_POST))
{
  if ($val)
    $$key = $val;
    $swmenupro[$key]=$val;
}

$database->setQuery( "SELECT * FROM #__modules WHERE id='$id'" );
$row = null;
$database->loadObject( $row );


if($preview==1){
	$id=mosGetParam( $_POST, 'mid', 0 );
	$database->setQuery( "SELECT * FROM #__modules WHERE id='$id'" );
$row = null;
$database->loadObject( $row );
	//echo $id;
	$query = "SELECT * FROM #__swmenu_config WHERE id = ".$id;
$database->setQuery( $query );
$result = $database->loadObjectList();

while (list ($key, $val) = each ($result[0]))
{
    $swmenupro[$key]=$val;
}

    $params = mosParseParams( $row->params );
    $menu = @$params->menutype ? $params->menutype : 'mainmenu';
    $moduleID = @$params->moduleID;
    $menustyle = @$params->menustyle;
    $parent_id = @$params->parentid ? $params->parentid : 0;
    $hybrid = @$params->hybrid ? $params->hybrid : 0;
    $active_menu = @$params->active_menu ? $params->active_menu : 0;
    $parent_level = @$params->parent_level ? $params->parent_level: 0;
    $levels = @$params->levels ? $params->levels: 0;
    $sub_indicator = @$params->sub_indicator ?  $params->sub_indicator :  0;
    $show_shadow = @$params->show_shadow ?  $params->show_shadow :  0;
    $selectbox_hack=@$params->selectbox_hack?$params->selectbox_hack:0;
    $padding_hack=@$params->padding_hack?$params->padding_hack:0;
	//$css_load = mosGetParam( $_POST, 'cssload', 0 );


}else if($preview==2){
	$query = "SELECT * FROM #__swmenu_config WHERE id = ".$id;
$database->setQuery( $query );
$result = $database->loadObjectList();

while (list ($key, $val) = each ($result[0]))
{
    $swmenupro[$key]=$val;
}

    $params = mosParseParams( $row->params );
    $menu = @$params->menutype ? $params->menutype : 'mainmenu';
    $moduleID = @$params->moduleID;
    $menustyle = @$params->menustyle;
    $parent_id = @$params->parentid ? $params->parentid : 0;
    $hybrid = @$params->hybrid ? $params->hybrid : 0;
    $active_menu = @$params->active_menu ? $params->active_menu : 0;
    $parent_level = @$params->parent_level ? $params->parent_level: 0;
    $levels = @$params->levels ? $params->levels: 0;
    $sub_indicator = @$params->sub_indicator ?  $params->sub_indicator :  0;
    $show_shadow = @$params->show_shadow ?  $params->show_shadow :  0;
	$css_load = @$params->cssload ?  $params->cssload :  0;
	$selectbox_hack=@$params->selectbox_hack?$params->selectbox_hack:0;
	$padding_hack=@$params->padding_hack?$params->padding_hack:0;

}else{

	$menu=mosGetParam( $_POST, 'menutype', "mainmenu" );
	$parent_id=mosGetParam( $_POST, 'parentid', 0 );
	$levels=mosGetParam( $_POST, 'levels', 0 );
   
    $moduleID = mosGetParam( $_POST, 'id', 0 );
    $hybrid = mosGetParam( $_POST, 'hybrid', 0 );
    $active_menu = mosGetParam( $_POST, 'active_menu', 0 );
    $parent_level = mosGetParam( $_POST, 'parent_level', 0 );
    $tables = mosGetParam( $_POST, 'tables', 0 );
	$selectbox_hack=mosGetParam( $_POST, 'selectbox_hack', 0 );
	$padding_hack=mosGetParam( $_POST, 'padding_hack', 0 );
   $id=$id?$id:1000000;
     $sub_indicator = mosGetParam( $_POST, 'sub_indicator', 0 );
      $show_shadow = mosGetParam( $_POST, 'show_shadow', 0 );
	$swmenupro['id']=$swmenupro['id']?$swmenupro['id']:0;
	
}

//echo $menustyle;
global $database, $my, $Itemid;
global $mosConfig_shownoauth, $mosConfig_dbprefix;

if($menu && $id && $menustyle){

$content= "\n<!--SWmenuPro5.5 ".$menustyle." by http://www.swmenupro.com-->\n";   

if($menu && $id && $menustyle){
	
	 $final_menu =array();
	 $swmenupro_array=swGetMenuLinks($menu,$id,$hybrid,1);
	 $ordered = chain('ID', 'PARENT', 'ORDER', $swmenupro_array, $parent_id, $levels);
	 $moduleid = mosGetParam( $_POST, 'moduleID', array(0) );
     $menutype = mosGetParam( $_POST, 'menutype', '' );
	 $images_preview = mosGetParam( $_POST, 'images_preview', 0 );
	
	//  $out = mosGetParam( $_POST, 'php_out', '' );
	 
	 if ($images_preview){
    		
			 $final_menu =array();
	 
	 //echo "out:".$swmenupro['php_out'];
	 
	 
	 $data3=explode("}}",$swmenupro['php_out']);
	 
	 foreach ($data3 as $dat){
		
		$data4=explode(";;",$dat);
		
		if(@$data4[3]){
		$temp_id=explode("-", $data4[0]);
		$id=@$temp_id[1]?$temp_id[1]:0;
		$temp_id=explode("-", $data4[1]);
		$parent=@$temp_id[1]?$temp_id[1]:0;
		
		$name=$data4[3];
		$browserNav=$data4[6];
		//$order=$data4[7];
		
		$link=$data4[4];
		
		$ordering=($data4[7]+1);
		if(($data4[8]!="")){
			$image=substr($data4[8],3).",".$data4[10].",".$data4[11].",".$data4[13].",".$data4[12];
		}else{

			$image="";
		}
		$showname=$data4[18];
		$showitem=$data4[22];
		$imagealign=$data4[19];
		
		$ncss=($data4[20]=="undefined")?"":$data4[20];
		$ocss=($data4[21]=="undefined")?"":$data4[21];
		if(($data4[9]!="")){
			$imageover=substr($data4[9],3).",".$data4[14].",".$data4[15].",".$data4[17].",".$data4[16];
		}else{
			$imageover="";
		}
            if($showitem) { 
          	$final_menu[] =array("TITLE" => $name, "URL" =>  'javascript:void(0);' , "ID" => $id  , "PARENT" => $parent ,  "ORDER" => $ordering, "IMAGE" => $image, "IMAGEOVER" => $imageover, "SHOWNAME" => $showname, "IMAGEALIGN" => $imagealign, "TARGETLEVEL" => 0, "TARGET" => 0,"ACCESS" => '1',"NCSS" => $ncss,"OCSS" => $ocss,"SHOWITEM" => $showitem   );
        }
	}}
       
    }else{
    	
    	 for($i=0;$i<count($ordered);$i++){
            	$swmenu=$ordered[$i];
            	$swmenu['URL'] = "javascript:void(0)";
            	if($swmenu['SHOWITEM']==null || $swmenu['SHOWITEM']==1 ){
				$swmenu['SHOWITEM']=1;
				}else{
				$swmenu['SHOWITEM']=0;
				}
				if($swmenu['SHOWITEM']) { 
            		$final_menu[] =array("TITLE" => $swmenu['TITLE'], "URL" =>  $swmenu['URL'] , "ID" => $swmenu['ID']  , "PARENT" => $swmenu['PARENT'] ,  "ORDER" => $swmenu['ORDER'], "IMAGE" => $swmenu['IMAGE'], "IMAGEOVER" => $swmenu['IMAGEOVER'], "SHOWNAME" => $swmenu['SHOWNAME'], "IMAGEALIGN" => $swmenu['IMAGEALIGN'], "TARGETLEVEL" => $swmenu['TARGETLEVEL'], "TARGET" => 0,"ACCESS" => $swmenu['ACCESS'],"NCSS" => $swmenu['NCSS'],"OCSS" => $swmenu['OCSS'],"SHOWITEM" => $swmenu['SHOWITEM']   );
             	}
    	 }
    }

	if(count($final_menu)){
		
	$swmenupro['position']="center";
	if($preview){
		$ordered = chain('ID', 'PARENT', 'ORDER', $final_menu, $parent_id, $levels);
	}else{
		$ordered = chain('ID', 'PARENT', 'ORDER', $final_menu, 0, $levels);
	}
		
		if ($menustyle == "clickmenu"){$content.= doClickMenuPreview($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
		if ($menustyle == "clicktransmenu"){$content.= doClickTransMenuPreview($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
		
		if ($menustyle == "slideclick"){$content.= doSlideClickPreview($ordered, $swmenupro, $css_load,$active_menu,$selectbox_hack,$padding_hack);}
		
		if ($menustyle == "treemenu"){$content.= doTreeMenuPreview($ordered, $swmenupro, $css_load,$active_menu);}
		if ($menustyle == "popoutmenu"){$content.= doPopoutMenuPreview($ordered, $swmenupro, $css_load, $active_menu);}
		if ($menustyle == "gosumenu" ){$content.= doGosuMenuPreview($ordered, $swmenupro, $active_menu, $css_load,$selectbox_hack,$padding_hack);}
		if ($menustyle == "transmenu"){$content.= doTransMenuPreview($ordered, $swmenupro, $active_menu, $sub_indicator, $parent_id, $css_load,0,$show_shadow,$padding_hack);}
		if ($menustyle == "tabmenu"){$content.= doTabMenuPreview($ordered, $swmenupro, $parent_id, $css_load,$active_menu);}
		if ($menustyle == "dynamictabmenu"){$content.= doDynamicTabMenuPreview($ordered, $swmenupro, $parent_id, $css_load,$active_menu);}
	}
}
$content.="\n<!--End SWmenuPro menu module-->\n";
//echo $content;
}



function doClickMenuPreview($ordered, $swmenupro, $css_load, $active_menu,$expand,$padding_hack){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/ClickShowHideMenu_Packed.js"></script>';

$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{

if ((substr(swmenuGetBrowser(),0,5)!="MSIE6")&&$padding_hack){$swmenupro = fixPadding($swmenupro);}
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo ClickMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body>";
echo ClickMenu($ordered, $swmenupro, $active_menu,$expand);
echo changeBgColor();
echo "</body></html>";
}

function doClickTransMenuPreview($ordered, $swmenupro, $css_load, $active_menu,$expand,$padding_hack){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/ClickShowHideMenu_Packed.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/transmenu_Packed.js"></script>';

$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{

if ((substr(swmenuGetBrowser(),0,5)!="MSIE6")&&$padding_hack){$swmenupro = fixPadding($swmenupro);}
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo ClickTransMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body><div align='center' >";
echo ClickTransMenu($ordered, $swmenupro, $active_menu,$expand);
echo changeBgColor();
echo "</div></body></html>";
}

function doSlideClickPreview($ordered, $swmenupro, $css_load, $active_menu,$expand,$padding_hack){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/prototype.lite.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/moo.fx.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/moo.fx.pack.js"></script>';


$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{

if ((substr(swmenuGetBrowser(),0,5)!="MSIE6")&&$padding_hack){$swmenupro = fixPadding($swmenupro);}
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo SlideClickStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body>";
if($swmenupro['orientation']!="vertical"){
	echo "<div align='left'>";
}else{
	echo "<div align='center'>";
}
echo SlideClick($ordered, $swmenupro, $active_menu,$expand);
echo "</div>".changeBgColor();
echo "</body></html>";
}

function doTreeMenuPreview($ordered, $swmenupro, $css_load,$active_menu){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/dtree.js"></script>';

$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{
echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo TreeMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body>";
echo TreeMenu($ordered, $swmenupro, $active_menu);
echo changeBgColor();
echo "</body></html>";
}


function doPopoutMenuPreview($ordered, $swmenupro, $css_load, $active_menu){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/menu.js"></script>';

$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{
echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo TigraMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body>";
echo TigraMenu($ordered, $swmenupro, $active_menu);
echo changeBgColor();
echo "</body></html>";
}


function doGosuMenuPreview($ordered, $swmenupro, $active_menu, $css_load,$selectbox_hack,$padding_hack){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/ie5.js"></script>';
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/DropDownMenuX_Packed.js"></script>';



$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{

if ((substr(swmenuGetBrowser(),0,5)!="MSIE6")&&$padding_hack){$swmenupro = fixPadding($swmenupro);}
echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo gosuMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";
}
echo "</head><body>";
echo GosuMenu($ordered, $swmenupro, $active_menu,$selectbox_hack);
echo changeBgColor();
echo "</body></html>";
}

function doTabMenuPreview($ordered, $swmenupro, $parent_id,$css_load, $active_menu){
global $mosConfig_live_site;
echo previewHead();
echo "<!--SWmenuPro CSS Tab Menu by http://www.swonline.biz-->\n";
$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo cssTabMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";

}
echo "</head><body>";
echo TabMenu($ordered, $swmenupro, $parent_id, $active_menu);
echo changeBgColor();
echo "</body></html>";

}


function doDynamicTabMenuPreview($ordered, $swmenupro, $parent_id,$css_load,$active_menu){
global $mosConfig_live_site;
echo previewHead();

$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace("\\","",$css);
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo dynamicTabMenuStyle($swmenupro,$ordered);
echo "\n-->\n";
	echo "</style>\n";

}
echo "</head><body>";
echo DynamicTabMenu($ordered, $swmenupro, $parent_id,$active_menu);
echo changeBgColor();
echo "</body></html>";

}

function doTransMenuPreview($ordered, $swmenupro, $active_menu,  $sub_indicator, $parent_id, $css_load,$selectbox_hack,$show_shadow,$padding_hack){
global $mosConfig_live_site;
echo previewHead();
echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/modules/mod_swmenupro/transmenu_Packed.js"></script>';
$manual=mosGetParam($_POST,"preview",0);
if($manual==1){
	$css=mosGetParam($_POST,"filecontent",'');
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	str_replace( '\\', '', $css );
	echo "\n-->\n";
	echo "</style>\n";
}else if($css_load){
	
echo "<link type='text/css' href='".$mosConfig_live_site."/modules/mod_swmenupro/styles/menu".$swmenupro['id'].".css' rel='stylesheet' />\n";

}else{
if ((substr(swmenuGetBrowser(),0,5)!="MSIE6")&&$padding_hack){$swmenupro = fixPadding($swmenupro);}
echo "\n<style type='text/css'>\n";
	echo "<!--\n";
echo transMenuStyle($swmenupro,$ordered,$show_shadow);
echo "\n-->\n";
	echo "</style>\n";

}
echo "</head><body>";
echo transMenu($ordered, $swmenupro, $active_menu,  $sub_indicator, $parent_id,$selectbox_hack);
echo changeBgColor();
echo "</body></html>";
}

function previewHead(){
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	echo "<head>\n<title>swMenuPro Menu Module Preview</title>\n";
	echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\" />";
	echo "\n<style type='text/css'>\n";
	echo "<!--\n";
	echo	"body{\nmargin-top:20px;\n}\n";
	echo	"#bg_table{\nposition:absolute;top:400px;left:150px;\n}\n";
	echo "\n-->\n";
	echo "</style>\n";
	?>
<script type="text/javascript">
<!--
function changeBG(){
document.body.style.backgroundColor = document.getElementById('back_color').value;
//alert(document.getElementById('back_color').value);
}

-->
</script>
<?php
 }

function changeBgColor(){
?>
<br />
<table width="300" style="border:1px solid blue;" bgcolor="yellow" id="bg_table"><tr>
<td align="center">Please Select Preview Background Color</td></tr><tr>
<td align="center">
<select name="back_color" id="back_color" onChange="changeBG();" style ="width:200px">
<option  value="white">white</option>
<option  value="red">red</option>
<option  value="blue">blue</option>
<option  value="green">green</option>
<option  value="aqua">aqua</option>
<option  value="black">black</option>
<option  value="gray">gray</option>
<option  value="lime">lime</option>
<option  value="maroon">maroon</option>
<option  value="navy">navy</option>
<option  value="olive">olive</option>
<option  value="purple">purple</option>
<option  value="silver">silver</option>
<option  value="teal">teal</option>
<option  value="yellow">yellow</option>
</select>
</td></tr>
<tr>
   <td align="center"><a href="#" onClick="window.close()">Close Window</a></td>
</tr>
</table>

    <?php
}
?>
 
    
 
