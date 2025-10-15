function writeToDiv(id, text){
	if (document.createTextNode){
		var mytext=document.createTextNode(text)
		document.getElementById(id).appendChild(mytext)
		var br = document.createElement("br");
		document.getElementById(id).appendChild(br)
	}
}