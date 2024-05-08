<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require 'dbConnect.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    case 'GET':
        require_once 'curlTimetable.php';
        http_response_code(200);
        echo json_encode(array('status' => 'success', 'code' => 200, 'message' => 'Data created!'));
        break;
        
        case 'DELETE':
            $conn->query("SET FOREIGN_KEY_CHECKS=0");
            $tables = ['timetable', 'subject', 'type', 'classroom'];
    
            foreach ($tables as $table) {
                $query = "TRUNCATE TABLE $table";
                if ($conn->query($query) === TRUE) {    
                    http_response_code(200);
                    echo json_encode(array('status' => 'success', 'code' => 200, 'message' => 'Data deleted!'));
                } else {
                    
                    http_response_code(500);
                    echo json_encode(array('status' => 'fail', 'code' => 500, 'message' => 'Data failed to delete!'));
                }
            }
            $conn->close();
            break;
}

?>