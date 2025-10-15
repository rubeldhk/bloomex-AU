<?php
/**
* mod_ajaxtabsjp 1.6
* @package JoomlaExtensions
* @copyright Copyright (C) 2007 JoomlaProdigy.com.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
global $mosConfig_live_site, $mainframe;
$source = $params->get('source', '');
$sourceid = trim( $params->get( 'sourceid' ) );
$tabsname = trim( $params->get( 'tabsname' ) );
$tabsname_default = $params->get( 'tabsname_default');
$count = intval($params->get('count',1));
$timeformat = $params->get('timeformat', '');
$lastimage = $params->get('lastimage', '');
$linkedtitle = $params->get('linkedtitle', '');
$linkedimage = $params->get('linkedimg', '');
$linkedsource = $params->get('linkedsource', '');
$chars = intval($params->get('chars',''));
$words = intval($params->get('words',''));
$readmore = $params->get('readmore','');
$custumimage = trim ($params->get('custumimage'));
$custumtext = $params->get('custumtext');
$align = $params->get ('align', '');
$width = $params->get ('width', '');
$height = $params->get ('height', '');
$textheight = trim ($params->get ('textheight'));

if ($custumimage){
    $custumimagea =  explode(",", $custumimage);
    }

if ($sourceid){
$sourceida = explode(",", $sourceid);
}
 
$i=0;

foreach ($sourceida as $sourceid){
 $html=""; 
$sql = "SELECT * FROM #__content";
switch($source){
  case 'section':
		$sql.="\n WHERE sectionid=$sourceid";
		$sourcelink = "index.php?option=com_content&amp;task=section&amp;id=";
  break;
  case 'category': 
		$sql.="\n WHERE catid=$sourceid";
		$sourcelink = "index.php?option=com_content&amp;task=blogcategory&amp;id=";
    break;
  case 'item':
	  	$sql.="\n WHERE id=$sourceid";
	  	$sourcelink = "index.php?option=com_content&amp;task=view&amp;id=";
  	break;
  default:
    break;
}
$sql .= "\n AND ( publish_up = '0000-00-00 00:00:00' OR publish_up <= now() )
            AND ( publish_down = '0000-00-00 00:00:00' OR publish_down >= now() )
            AND state != -1 AND state != 0";
$sql .= "\n ORDER BY created DESC";

if (is_numeric($count)){
  $sql .= "\n LIMIT $count";
}

$database->setQuery($sql);

$items= $database->loadObjectList();
setlocale(LC_TIME, $mosConfig_locale);

foreach($items as $item){

     
$month = strftime("%b", strtotime($item->created));
$date =strftime("%d", strtotime($item->created) );
$year =strftime("%Y", strtotime($item->created) );
$html .= '<div style="orthopal_tab">';

// get itemid if contentitem in menu
	list(,$r) = each($items);
		// get itemid if contentitem in menu
			$contentid = $r->id;
				$query = "select id from #__menu where published = '1' and type = 'content_item_link' and componentid='$contentid';";
				$database->setQuery( $query );
				$Itemid = $database->loadresult();
			if (!$Itemid) {
				// needed to reduce queries used by getItemid		
			$bs = $mainframe->getBlogSectionCount();
			$bc = $mainframe->getBlogCategoryCount();
			$gbs = $mainframe->getGlobalBlogSectionCount();
			// get Itemid
			$Itemid = $mainframe->getItemid( $r->id, 0, 0, $bs, $bc, $gbs );
			}    
			// Blank itemid checker for SEF
			if ($Itemid == NULL) {
			$Itemid = '';
			} else {
			$Itemid = '&amp;Itemid='. $Itemid;
			}

 $link = sefRelToAbs( 'index.php?option=com_content&amp;task=view&amp;id='. $item->id . $Itemid);
 
 

 if ($linkedtitle){
    $title = "<h3><a href=\"$link\">$item->title</a></h3>";
  }else{    
$title = "<h3>$item->title</h3>";
  }
  $html .= $title;
  

	if ($timeformat=='classic'){
$html .=strftime("%b %d, %Y", strtotime($item->created));
$html .="<br/><br/><div>";} 
	else if ($timeformat=='artistic'){
$html .='<div class="orthopaltab_date">';
$html .="<span class=\"ort_month\">$month<br/></span>";
$html .="<span class=\"ort_day\">$date<br/></span>";
$html .="<span class=\"ort_year\">$year<br/></span>";
$html .='</div>';
$html .='<div class="orthopaltab_content">';
}else{
$html .="";
}
  //Image fetching
  if ($lastimage){
    $item->images 	= explode( "\n", $item->images );
    $item->lastimage = end($item->images);
    $item->lastimage = explode ("|", $item->lastimage);
    $imageurl=$item->lastimage[0];
    if (isset($item->lastimage[2])){
      $imagealt=$item->lastimage[2];
    }else{
      $imagealt="";
    }
   }else if ($custumimage) {
      $image = "<img src=\"$mosConfig_live_site/images/stories/$custumimagea[$i]\" class=\"img\" alt=\"$imagealt\" align=\"$align\" width=\"$width\" height=\"$height\"/>";
    }else{
    $image="";
    }
  if (isset($imageurl) AND $imageurl!=''){
    if ($linkedimage){
      $image = "<a href=\"$link\" ><img src=\"$mosConfig_live_site/images/stories/$imageurl\" class=\"img\" alt=\"$imagealt\" align=\"$align\" width=\"$width\" height=\"$height\"/></a>";
    }else{
      $image = "<img src=\"$mosConfig_live_site/images/stories/$imageurl\" class=\"img\" alt=\"$imagealt\" align=\"$align\" width=\"$width\" height=\"$height\"/>";
    }
    }
    
  $html .= $image;
  //image fetching done
  $item->introtext = preg_replace('/{([a-zA-Z0-9\-_]*)\s*(.*?)}/i', '', $item->introtext);
  $to_omit = "#{*(.*?)}#s";
  $item->introtext = preg_replace($to_omit, '', $item->introtext);

  $text = $item->introtext;
  if ($chars!=''){
    $text = substr($text, 0, $chars) . "...";
  }else if ($words!=''){
    $prevwords = count(explode(" ",$text));
    $text = implode(" ", array_slice(explode(" ",$text), 0, $words));
   if (count(explode(" ",$text))<$prevwords){
      $text .= "...";
   }
  }
  $html .= $text;
  if ($readmore){
    $html .= "<a class=\"readon\" href=\"$link\">Read More...</a>";
    }  
  $html .='</div>';
  $html .='<div class="clr"></div>';
  $html .= '</div>';
  
}
if ($linkedsource){
 $sourcelink = sefRelToAbs( "$sourcelink". $sourceid . $Itemid);
 $html .= "<a class=\"readon\" href=\"$sourcelink\">Read All</a><br/>";
 }
$xml=fopen("modules/mod_ajaxtabsjp/content_tab$i.htm",'w');
fwrite($xml,utf8_encode($html));
fclose($xml);
$i=$i+1;
 } 
 ?>

<link rel="stylesheet" type="text/css" href="modules/mod_ajaxtabsjp/ajaxtabs/ajaxtabs.css" />

<script type="text/javascript" src="modules/mod_ajaxtabsjp/ajaxtabs/ajaxtabs.js">

/***********************************************
* Ajax Tabs Content script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

</script>
<?php
echo "<ul id=\"maintab\" class=\"shadetabs\">";
$tabsnamea = explode(",", $tabsname);
$k=0;
if ($custumtext){
echo "<li class=\"selected\"><a href=\"#default\" rel=\"ajaxcontentarea\">$tabsname_default</a></li>";
foreach ($tabsnamea as $tabsname){
echo "<li><a href=\"modules/mod_ajaxtabsjp/content_tab$k.htm\" rel=\"ajaxcontentarea\">$tabsname</a></li>";
$k=$k+1;
}
}
else {
echo "<li class=\"selected\"><a href=\"modules/mod_ajaxtabsjp/content_tab$k.htm\" rel=\"ajaxcontentarea\">$tabsname_default</a></li>";
foreach ($tabsnamea as $tabsname){
$k=$k+1;
echo "<li><a href=\"modules/mod_ajaxtabsjp/content_tab$k.htm\" rel=\"ajaxcontentarea\">$tabsname</a></li>";
}
}
echo "</ul>";

 echo "<div id=\"ajaxcontentarea\" class=\"contentstyle\" style=\"height:$textheight\">";
?>
<p><?php
preg_match("/<script(.*)>(.*)<\/script>/", $custumtext, $matches);
if ($matches) {
foreach ($matches as $i=>$match) {
  $clean_js = preg_replace("/<br \/>/", "", $match);
  $custumtext = str_replace($match, $clean_js, $custumtext);
}
}
echo $custumtext;
?>
</p>
</div>

<script type="text/javascript">
//Start Ajax tabs script for UL with id="maintab" Separate multiple ids each with a comma.
startajaxtabs("maintab")
</script>