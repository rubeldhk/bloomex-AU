var xmlHttp;
var id;

function showDataInput()
{
id = "result";
document.getElementById(id).innerHTML="Loading...";
str = document.converter.cur_from.value;
strto = document.converter.cur_to.value;
val = document.converter.val.value;
var url="components/com_currencyconverter/convert.php?from=" + str + "&to=" + strto + "&val=" + val;
xmlHttp=GetXmlHttpObject(stateChanged);
xmlHttp.open("GET", url , true);
xmlHttp.send(null);
}

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{ 
document.getElementById(id).innerHTML=xmlHttp.responseText ;
} 
} 

function GetXmlHttpObject(handler)
{ 
var objXmlHttp=null;

if (navigator.userAgent.indexOf("Opera")>=0)
{
alert("This example doesn't work in Opera") ;
return ;
}
if (navigator.userAgent.indexOf("MSIE")>=0)
{ 
var strName="Msxml2.XMLHTTP";
if (navigator.appVersion.indexOf("MSIE 5.5")>=0)
{
strName="Microsoft.XMLHTTP";
} 
try
{ 
objXmlHttp=new ActiveXObject(strName);
objXmlHttp.onreadystatechange=handler ;
return objXmlHttp;
} 
catch(e)
{ 
alert("Error. Scripting for ActiveX might be disabled");
return;
} 
} 
if (navigator.userAgent.indexOf("Mozilla")>=0)
{
objXmlHttp=new XMLHttpRequest();
objXmlHttp.onload=handler;
objXmlHttp.onerror=handler;
return objXmlHttp;
}
} 