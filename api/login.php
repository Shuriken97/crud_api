<?php
require_once 'headers.php';
include_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$con = new mysqli('db4free.net','fypfacerecog','b436a8bf','fypfacerecog');

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $uname = $data->username;
    $password = $data->password;
    $sql = $con->query("SELECT * FROM admin WHERE username = '$uname'");
    if($sql->num_rows > 0) {
        $user = $sql->fetch_assoc();
        if(password_verify($password, $user['pass'])) {
            $key = "YOUR_SECRET_KEY";
            $payload = array(
                'admin_id' => $user['admin_id']
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
