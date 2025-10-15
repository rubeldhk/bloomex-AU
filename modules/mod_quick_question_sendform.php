<?php

$LOGFILE="../quickQuestionLog.txt";


if ( isset($_REQUEST['input']) ) {
	$input=$_REQUEST['input'];
} else {
	$input="";
}

if ( isset($_REQUEST['myself']) ) {
	$myself=$_REQUEST['myself'];
}

if ( isset($_REQUEST['sendto']) ) {
	$sendto=$_REQUEST['sendto'];
}

if ( isset($_REQUEST['thanks']) ) {
	$thanks=$_REQUEST['thanks'];
}


if ( ! $input == "" ) {

	$to  = $sendto;

	/* message */
	$message=$input;

	/* To send HTML mail, you can set the Content-type header. */
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "From: MyPortal at ffkijken <myportal@ffkijken.com>\r\n";

	/* log */
	if ( isset($REMOTE_ADDR) ) {
		$ip=$REMOTE_ADDR;
	} else {
		$ip="0.0.0.0";
	}
	$date=date("Y-m-d H:i:s",time());
	$string="\r\n****** $date $ip $to ****** \r\n\"$message\"\r\n";

	$handle = fopen ($LOGFILE, "a+");
	fwrite($handle, $string);
	fclose($handle);
	
	/* subject */
	$subject = "Sneleensite.COM, vraag (from: ".$ip.")";

	/* and now mail it */
	mail($to, $subject, $message, $headers);
}

// header("Location: ".$myself);
echo "<script LANGUAGE=\"JavaScript\">alert(\"". $thanks ."\");</script>";
echo "<meta http-equiv=\"refresh\" content=\"0; URL=".$myself."\">";

?>