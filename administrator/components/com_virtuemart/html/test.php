<center>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#000000">
<tr>
<td width="60"><b>email</b></td>
</tr>
<tr>
<td>
<?php 
$hostname = "localhost";
$username = "liveperson";
$password = "emmi0202";
$dbName = "live";

MYSQL_CONNECT($hostname, $username, $password) OR DIE("DB connection unavailable");
@mysql_select_db( "$dbName") or die( "Unable to select database");
?>
<?php 
//error message (not found message)begins
$XX = "No Record Found, to search again please close this window";
//query details table begins
$query = mysql_query("SELECT email FROM livehelp_transcripts WHERE email LIKE \"%$search%\" LIMIT 0, 50");
while ($row = @mysql_fetch_array($query))
{
$variable1=$row["email"];
//table layout for results

print ("<tr>");
print ("<td>$variable1</td>");
print ("</tr>");
}
//below this is the function for no record!!
if (!$variable1)
{
print ("$XX");
}
//end
?>
</table>
</center>




// Close the database connection
mysql_close();
?>