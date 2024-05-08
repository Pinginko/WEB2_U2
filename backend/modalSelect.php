<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require 'dbConnect.php';

$sql = "SELECT * from classroom; SELECT * from subject; SELECT * from type; SELECT DISTINCT day from timetable";
if ($conn->multi_query($sql)) {
    $data = array();
    do {
        if ($result = $conn->store_result()) {
            while ($row = $result->fetch_all()) {
                $data[] = $row;
            }
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
}



$data['room'] = $data[0];
unset($data[0]);
$data['subject'] = $data[1];
unset($data[1]);
$data['type'] = $data[2];
unset($data[2]);
$data['day'] = $data[3];
unset($data[3]);


echo json_encode($data);

?>