<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");


require 'dbConnect.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    case 'GET':

        $sql = "SELECT timetable.id, subject.name AS subject_name, type.name AS type, classroom.room_number, timetable.day, timetable.time 
                FROM timetable 
                INNER JOIN subject ON timetable.subject_name = subject.id 
                INNER JOIN type ON timetable.type = type.id 
                INNER JOIN classroom ON timetable.room = classroom.id";

        $result = $conn->query($sql);

        $data = array();
        while($row = $result->fetch_assoc()) {
        $data[] = $row;
        }

        //echo '<pre>';var_dump($data);echo'</pre>';
        echo json_encode($data);
        $conn->close();
        break;

    case 'POST':
      $subject_name = $_POST['subject'];
      $type = $_POST['type'];
      $room = $_POST['room'];
      $day = $_POST['day'];

      $sql = "INSERT INTO timetable (subject_name, type, room, day) 
              VALUES ('$subject_name', '$type', '$room', '$day')";

      if ($conn->query($sql) === TRUE) {
        http_response_code(200);
        $response = array(
          'status' => 'success', 
          'code' => 200, 
          'message' => 'Record created!',
          'data' => array(
              'subject_name' => $subject_name, 
              'type' => $type, 
              'room' => $room, 
              'day' => $day
          )
      );
        echo json_encode($response);
        die();
      } else {
          http_response_code(500);
          echo json_encode(array('status' => 'fail', 'code' => 500, 'message' => 'Failed to create record !'));
      }

      $conn->close();
      header ('http://node64.webte.fei.stuba.sk:81/');
      break;



  case 'DELETE' :
  
      $sql = "DELETE FROM timetable WHERE id = ?";

      $result = $conn->prepare($sql);


      if ($result === false) {
          die("Failed to prepare statement: " . $mysqli->error);
      }

      $id = $_GET['id'];
      //var_dump($id);
      $result->bind_param('i', $id);

      
      

      if ($result->execute()) {
        http_response_code(200);
        echo json_encode(array('status' => 'success', 'code' => 200, 'message' => 'Record deleted!', 'data' => array('id' => $id) ));
      } else {
        http_response_code(500);
        echo json_encode(array('status' => 'fail', 'code' => 500, 'message' => 'Failed to delete!'));;
      }

      $result->close();
      $conn->close();

      break;
    }
?>