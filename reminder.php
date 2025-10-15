<?php 
require_once( 'configuration.php' );


/* Try to connect to database */
	mysql_connect( "$mosConfig_host", "$mosConfig_user", "$mosConfig_password") or
		die ( "Unable to connect to database server...");
/* Select database */
	mysql_select_db( "$mosConfig_db" ) or
		die ( "Unable to select database...");


//echo date('m')."<BR>";
//echo date('d')."<BR>";
$tomorrow  = mktime(0, 0, 0, date("m")  , date("j")+3, date("Y")); 
$recip_day=date('j',$tomorrow);
$recip_month= date('m',$tomorrow);
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Additional headers

$headers .= 'From: <wecare@bloomex.ca>' . "\r\n";
$subject = 'Bloomex Reminder';




$result=mysql_query("SELECT * FROM `mos_content` WHERE title_alias = 'Reminder' ORDER BY `title_alias` DESC LIMIT 0,1");
  if ($result){
    $row=mysql_fetch_array($result);
	$HTML="<BR>".$row['introtext'];
}

 
    
  /* Query Database */
$q="SELECT * FROM `mos_pshop_reminder` WHERE recip_month='".$recip_month."' AND recip_day='".$recip_day."' LIMIT 0,400";
$result=mysql_query($q);

while ($row=mysql_fetch_array($result)){
        $message="<html><body>Recipient :" .$row['recip_name']. "     Occasion :".$row['occasion']."<BR>";
        $message.="Occasion Date:".$row['recip_day']."/".$row['recip_month']."     Notes :".$row['subject'];  
        $message.=$HTML."</body></html>";
        $to = $row['recip_email'];
        mail($to, $subject, $message, $headers);
   }

?>