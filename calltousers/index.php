<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Select Extension</title>
</head>
<body>
<div align="center" style="position: absolute; top: 30%; left: 40%; height: 145px;background: #4169E1; width: 300px">
			<form action="callpage.php" accept-charset="utf8" method="GET">
			<img width="100" src="/templates/bloomex7/images/bloomexlogo.png" alt="logo"><br>
			<b><font color="#B22222"></font></b><br>
                        <b><font color="white">Enter your extension</font></b> <br><input type="text" style="  width: 50px;"  onkeypress='validate(event)' name="ext" maxlength="3">
			<input type="submit" value="Ok">
			</form></div>
    <script>
        
        function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
    </script>
</body>

</html>
