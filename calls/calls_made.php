<?php

include_once './config.php';
include_once 'curl_number.php';

$date = date('Y-m-d');
$type = $_POST['type'];
$extension = $_POST['ext'];

$query = "SELECT * 
    FROM tbl_numbers_to_give 
    WHERE ext = '" . $extension . "' AND STR_TO_DATE(`datetime`, '%Y-%m-%d') = '" . $date . "' AND `type` = '" . $type . "';";

$result = $mysqli->query($query);


if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $csvData = '';
    $headers = array_keys($data[0]);
    $csvData .= implode(',', $headers) . "\n";
    foreach ($data as $row) {
        $csvData .= implode(',', $row) . "\n";
    }

    echo json_encode(array('status' => 'success', 'csvData' => $csvData));
    exit;
}

echo json_encode(array('status' => 'error', 'message' => 'No data found.'));
exit;