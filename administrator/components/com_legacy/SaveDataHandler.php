<?php
$file = "http://flowersinfo.org/bloomex.ca/spider/result.txt";
$db_table = "spider_legacy";
require_once '../../../configuration.php';
$mysql = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
$link = mysql_select_db($mosConfig_db);
if( !$link ) die( json_encode( array( 'result' => 'false', 'message' => '<span style="color: #FF0000;">No connected to database.</span>' ) ) );

$date = date( "Y-m-d" );
// SELECT OLD //////////////
$qDelete = "SELECT pid FROM $db_table WHERE date='$date'";
$result = mysql_query( $qDelete ); 
$issetPid = array();
while ($row = mysql_fetch_array($result)) {
    $issetPid[] = $row['pid'];
}
/*
echo '<pre>';
sort($issetPid);
var_dump($issetPid) ;
echo '</pre>';
*/
///////////////////////////
$txt = file_get_contents( $file );
$fileContent = ( $txt ) ? explode( "\r\n", $txt ): array();
$count = count( $fileContent );
$dataset = null;

$q = "INSERT INTO $db_table VALUES ";
for( $i = 0; $i < $count; $i++ )
{
    $parts = explode( '[--1--]', $fileContent[$i] );
    if(!in_array($parts[0], $issetPid))
    {
        if( $parts[0] != '' && (int)$parts[1] > 1 && isset($parts[2]) && $parts[2] != '' && $parts[3] != '' )
        {
            $datasetParts = null;
            for ($index = 0; $index < count($parts); $index++) $datasetParts .= " ,'$parts[$index]' ";
            if( $datasetParts ) $datasetParts .= " ,'http://www.legacy.com/webservices/ns/FuneralInfo.svc/GetFuneralInfoJson?fhid=$parts[1]&pid=$parts[0]&cobrand=$parts[4]' ";
            if( $datasetParts ) $dataset .= ( ( $dataset ) ? "," : " " )."( NULL $datasetParts, '$date' )";
        }
    }
}

if( $dataset )
{
    $result = mysql_query( $q.$dataset );
    if( $result ) die( json_encode( array( 'result' => 'false', 'message' => '<span style="color: #0000FF;">Insert the data successfully.</span>' ) ) );
}

die( json_encode( array( 'result' => 'false', 'message' => '<center style="color: #0000FF;">No new data.</center>' ) ) );
?>