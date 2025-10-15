<?php
function LoadModule( $name, $params=NULL ) {
	global $mosConfig_absolute_path, $mosConfig_live_site;
	global $database, $acl, $my, $mainframe, $option;
	
	$task = mosGetParam( $_REQUEST, 'task', '' );
	// legacy support for $act
	$act = mosGetParam( $_REQUEST, 'act', '' );

	$name = str_replace( '/', '', $name );
	$name = str_replace( '\\', '', $name );
	$path = "$mosConfig_absolute_path/modules/mod_$name.php";
	if (file_exists( $path )) {
	    require $path;
	}
}
?>
<script type="text/javascript">
//** Tab Content script- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
//** Last updated: Nov 8th, 06

var enabletabpersistence=<?php echo $cookies;?> //enable tab persistence via session only cookies, so selected tab is remembered?

////NO NEED TO EDIT BELOW////////////////////////
var tabcontentIDs=new Object()

function expandcontent(linkobj){
var divid=linkobj.parentNode.parentNode.id //id of UL element
var divlist=document.getElementById(divid).getElementsByTagName("span") //get list of LIs corresponding to the tab contents
for (var i=0; i<divlist.length; i++){
divlist[i].className=""  //deselect all tabs
if (typeof tabcontentIDs[divid][i]!="undefined") //if tab content within this array index exists (exception: More tabs than there are tab contents)
document.getElementById(tabcontentIDs[divid][i]).style.display="none" //hide all tab contents
}
linkobj.parentNode.className="selected"  //highlight currently clicked on tab
document.getElementById(linkobj.getAttribute("rel")).style.display="block" //expand corresponding tab content
saveselectedtabcontentid(divid, linkobj.getAttribute("rel"))
}

function expandtab(tabcontentid, tabnumber){ //interface for selecting a tab (plus expand corresponding content)
var thetab=document.getElementById(tabcontentid).getElementsByTagName("a")[tabnumber]
if (thetab.getAttribute("rel"))
expandcontent(thetab)
}

function savetabcontentids(divid, relattribute){// save ids of tab content divs
if (typeof tabcontentIDs[divid]=="undefined") //if this array doesn't exist yet
tabcontentIDs[divid]=new Array()
tabcontentIDs[divid][tabcontentIDs[divid].length]=relattribute
}

function saveselectedtabcontentid(divid, selectedtabid){ //set id of clicked on tab as selected tab id & enter into cookie
if (enabletabpersistence==1) //if persistence feature turned on
setCookie(divid, selectedtabid)
}

function getullistlinkbyId(divid, tabcontentid){ //returns a tab link based on the ID of the associated tab content
var divlist=document.getElementById(divid).getElementsByTagName("span")
for (var i=0; i<divlist.length; i++){
if (divlist[i].getElementsByTagName("a")[0].getAttribute("rel")==tabcontentid){
return divlist[i].getElementsByTagName("a")[0]
break
}
}
}

function initializetabcontent(){
for (var i=0; i<arguments.length; i++){ //loop through passed UL ids
if (enabletabpersistence==0 && getCookie(arguments[i])!="") //clean up cookie if persist=off
setCookie(arguments[i], "")
var clickedontab=getCookie(arguments[i]) //retrieve ID of last clicked on tab from cookie, if any
var divobj=document.getElementById(arguments[i])
var dlist=divobj.getElementsByTagName("span") //array containing the LI elements within UL
for (var x=0; x<dlist.length; x++){ //loop through each LI element
var dlistlink=dlist[x].getElementsByTagName("a")[0]
if (dlistlink.getAttribute("rel")){
savetabcontentids(arguments[i], dlistlink.getAttribute("rel")) //save id of each tab content as loop runs
dlistlink.onclick=function(){
expandcontent(this)
return false
}
if (dlist[x].className=="selected" && clickedontab=="") //if a tab is set to be selected by default
expandcontent(dlistlink) //auto load currenly selected tab content
}
} //end inner for loop
if (clickedontab!=""){ //if a tab has been previously clicked on per the cookie value
var cdlistlink=getullistlinkbyId(arguments[i], clickedontab)
if (typeof cdlistlink!="undefined") //if match found between tabcontent id and rel attribute value
expandcontent(cdlistlink) //auto load currenly selected tab content
else //else if no match found between tabcontent id and rel attribute value (cookie mis-association)
expandcontent(dlist[0].getElementsByTagName("a")[0]) //just auto load first tab instead
}
} //end outer for loop
}


function getCookie(Name){ 
var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
if (document.cookie.match(re)) //if cookie found
return document.cookie.match(re)[0].split("=")[1] //return its value
return ""
}

function setCookie(name, value){
document.cookie = name+"="+value //cookie value is domain wide (path=/)
}
</script>