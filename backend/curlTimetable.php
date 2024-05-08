<?php
// Create a cURL resource
$username = "xkubala";
$password = "@Wifonkovy_Papac11";

$ch = curl_init("https://is.stuba.sk/system/login.pl");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'destination' => '/auth/?lang=sk',
    'credential_0' => $username,
    'credential_1' => $password,
    'login' => 'Prihlásiť sa',
    'credential_2' => '86400',
    'credential_cookie' => '1',
]));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

curl_exec($ch);
// Close the cURL session
//curl_close($ch);

$ch = curl_init("https://is.stuba.sk/auth/katalog/rozvrhy_view.pl?rozvrh_student_obec=1?zobraz=1;format=html;rozvrh_student=115079;lang=sk;");

curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/81.0");


$dataWeb = curl_exec($ch);
//echo $dataWeb;
curl_close($ch);

$doc = new DOMDocument();
@$doc->loadHTML($dataWeb);

$tabledataSeminar = $doc->getElementsByTagName('td');

require 'dbConnect.php';

$prednaska = "Prednáška";
$cvicenie = "Cvičenie";
$checkQuery1 = "SELECT COUNT(*) FROM type WHERE name = '$prednaska' or name = '$cvicenie'";
$result = $conn->query($checkQuery1);

if($result && $result->fetch_row()[0] === '0'){
    $insertQuery = "INSERT INTO type (name) VALUES ('$prednaska')";
    $conn->query($insertQuery);
    $insertQuery = "INSERT INTO type (name) VALUES ('$cvicenie')";
    $conn->query($insertQuery);
}



foreach ($tabledataSeminar as $tabledata) {
    if ($tabledata->getAttribute('class') === 'rozvrh-pred' || $tabledata->getAttribute('class') === 'rozvrh-cvic') {
        if($tabledata->getAttribute('class') === 'rozvrh-cvic'){
            $type = '2';
        }else{$type = '1';}
        //echo $tabledata->textContent . "<br>";
        $counter = 0;
        $tabledataAnchor =  $tabledata->getElementsByTagName('a');
        if ($tabledataAnchor->length > 0) {
            $cell = $tabledataAnchor->item(0)->parentNode->parentNode;
        }
//        $cell = $tabledataAnchor->item(0)->parentNode->parentNode;

        $time = 480-10-105;
        $day = ''; 

        while($cell){
            $cell = $cell->previousElementSibling;
            $time += 5;
            if($cell && (strlen($cell->nodeValue)==2 || strlen($cell->nodeValue)==3)){
                $day = $cell->textContent;
            }
            if($cell && strlen($cell->textContent) > 1){
                $time += 105;
            }       
        }
        //echo($day . '<br>');
        //echo(strlen('Št'));
        
        foreach($tabledataAnchor as $dataTable){

            if($counter == 0){
                $roomNumber = $dataTable->textContent;
                //echo  $roomNumber."<br>";
                $classroomIdQuery = "SELECT id FROM classroom WHERE room_number = '$roomNumber'";
                $classroomIdResult = $conn->query($classroomIdQuery);
                //var_dump($classroomIdResult);
                if($classroomIdResult->num_rows > 0){
                    $classroomId = $classroomIdResult->fetch_row()[0];
                }else{
                    $insertQuery = "INSERT INTO classroom (room_number) VALUES ('$roomNumber')";
                    $conn->query($insertQuery);
                    $classroomId = $conn->insert_id;
                
                }

               // $checkQuery = "SELECT COUNT(*) FROM classroom WHERE room_number = '$roomNumber'";
                //$result = $conn->query($checkQuery);

                //if ($result && $result->fetch_row()[0] === '0') {}
            }
        
            if($counter == 1){
                $subject = $dataTable->textContent;
                //echo $subject."<br> ";
                $subjectIdQuery = "SELECT id FROM subject WHERE name = '$subject'";
                $subjectIdResult = $conn->query($subjectIdQuery);
                if($subjectIdResult->num_rows > 0){
                    $subjectId = $subjectIdResult->fetch_row()[0];
                }else{
                    $insertQuery = "INSERT INTO subject (name) VALUES ('$subject')";
                    $conn->query($insertQuery);
                    $subjectId = $conn->insert_id;
                }
                

                $checkQuery = "SELECT COUNT(*) FROM subject WHERE name = '$subject'";
                $result = $conn->query($checkQuery);
                //if ($result && $result->fetch_row()[0] === '0') {
                    //$insertQuery = "INSERT INTO subject (name) VALUES ('$subject')";
                    //$conn->query($insertQuery);
                //}
            }
            $counter++;
            
        }
        
        $checkTimetableQuery = "SELECT COUNT(*) FROM timetable WHERE subject_name = '$subjectId' AND type = '$type' AND room = '$classroomId' AND day = '$day' AND time = '$time'";
        $checkTimetableResult = $conn->query($checkTimetableQuery);
        if ($checkTimetableResult && $checkTimetableResult->fetch_row()[0] === '0' && $time > 480){
            $insertTimetableQuery = "INSERT INTO timetable (subject_name, type, room, day, time) VALUES ('$subjectId', '$type', '$classroomId', '$day', '$time')";
            $conn->query($insertTimetableQuery);
        }
        $counter = 0;
    }
 
}
    $conn->close();

?>