<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
$db = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}

$sql = "SELECT last_commit
        FROM `tbl_opcache_last_cleared_commit` order by id desc limit 1";

if (!$result = $db->query($sql)) {
    die('There was an error running the select query [' . $db->error . ']');
}
$row = $result->fetch_object();

$lastCommit = shell_exec("git rev-parse HEAD");

if($lastCommit != $row->last_commit){
    $output = shell_exec("git diff --name-only $row->last_commit $lastCommit");
    $changedFiles = explode("\n", trim($output));

    if($changedFiles){
        foreach($changedFiles as $changedFile){
            opcache_invalidate($_SERVER['DOCUMENT_ROOT'].'/'.$changedFile, true);
            echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$changedFile);
            echo '<br><hr><br>';
        }
    }

    $sql = "INSERT INTO tbl_opcache_last_cleared_commit (last_commit) VALUES ('$lastCommit') ";
    if (!$result = $db->query($sql)) {
        die('There was an error running the insert query [' . $db->error . ']');
    }

    echo "<p>Cache cleared for these files</p><pre>";
    print_r($changedFiles);
} else {
    echo "Last Commit changes already cleared";
}

