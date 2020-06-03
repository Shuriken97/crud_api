<?php
require_once 'headers.php';
include_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$con = new mysqli('us-cdbr-east-05.cleardb.net','b2bc2043d3485c','9a9fcb24','heroku_915acf922a70388');

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if (isset($_GET['id'])){ //retrieve single row
        $id = $con->real_escape_string($_GET['id']);
        $sql = $con->query("SELECT attendance.atten_id, students.name, attendance.sub_name,attendance.create_time FROM attendance JOIN students ON students.name = attendance.name WHERE atten_id='$id'");
        $data = $sql->fetch_assoc();
    } else { //retrieve all rows
        $data = array();
        $sql = $con->query("SELECT attendance.atten_id, students.name, attendance.sub_name, attendance.create_time FROM attendance JOIN students ON students.name = attendance.name");
        while ($d = $sql->fetch_assoc()){
            $data[] = $d;
        }
    }

    exit(json_encode($data)); //return as JSON
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $uname = $data->username;
    $pass = $data->pass;
    $sql = $con->query("SELECT * FROM admin WHERE username = '$uname'");
    if($sql->num_rows > 0) {
        $user = $sql->fetch_assoc();
        if(password_verify($pass, $user['pass'])) {
            $key = "YOUR_SECRET_KEY";
            $payload = array(
                'id' => $user['admin_id'],
                'username' => $user['username']
            );

            $token = JWT::encode($payload, $key);
            http_response_code(200);
            echo json_encode(array('token' => $token));
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Login Failed'));
        }
    } else {
        http_response_code(400);
        echo json_encode(array('message' => 'Login Failed!'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    if(isset($_GET['id'])){
        $id = $con->real_escape_string($_GET['id']);
        $sql = $con->query("DELETE FROM attendance WHERE atten_id='$id'");

        if ($sql) {
            exit(json_Encode(array('status' => 'success')));
        } else {
            exit(json_encode(array('status' => 'error')));
        }
    }
}
