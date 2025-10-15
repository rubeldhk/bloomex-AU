<?php

$feedstart = time() - 60*60*24;
if (isset($argv[1])) {
    $_GET['type'] = $argv[1];
}
switch ($_GET['type']) {
    case "lfp":       
        $feeder = "localflorist";
        $feeder_host = 'db3.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com';
        $feeder_user = 'LFCOMA_prod';
        $feeder_password = 'Dmq7lLjwIiIaOJ48';
        $feeder_db = 'LFCOMA_prod';
        break;
    case "ofs":
    default:
        $feeder = "onlinefloristsydney";
        $feeder_host = "db1.cbkfsmxfdx3h.us-west-2.rds.amazonaws.com";
        $feeder_user = "OFS_prod";
        $feeder_password = "vqYDDDgclVSi";
        $feeder_db = "OFS_prod";
       break;
}
