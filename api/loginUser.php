<?php
require '../vendor/autoload.php';

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../Mudoles/Class.php';

$database = new Database();
$db = $database->getConnection();

$user_obj = new User($db);

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {

        $user_obj->email = $data->email;

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(array(
                "status" => 0,
                "message" => "Invalid Email Address!",
            ));
            exit;
        }

        if (strlen($data->password) < 8) {
            http_response_code(422);
            echo json_encode(array(
                "status" => 0,
                "message" => "Your password must be at least 8 characters long!",
            ));
            exit;
        }

        $user_data = $user_obj->loginUser();

        if (!empty($user_data)) {

            $name = $user_data['name'];
            $password = $user_data['password'];
            $email = $user_data['email'];
            $age = $user_data['mobile'];
            $designation = $user_data['address'];

            if (password_verify($data->password, $password)) {

                $iss = "localhost";
                $iat = time();
                $nbf = $iat + 10;
                $exp = $iat + 360;
                $aud = "myusers";
                $user_data = array(
                    "id" => $user_data['id'],
                    "name" => $user_data['name'],
                    "email" => $user_data['email'],
                    "mobile" => $user_data['mobile'],
                    "address" => $user_data['address'],
                );

                $payload_info = array(

                    "iss" => $iss,
                    "iat" => $iat,
                    "nbf" => $nbf,
                    "exp" => $exp,
                    "aud" => $aud,
                    "data" => $user_data,
                );

                $secrect_key = "owt123";

                $jwt = JWT::encode($payload_info, $secrect_key, "HS512");

                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "LOGIN SUCESSFULLY",
                    "jwt" => $jwt,
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "fail to insert",
                ));
            }
        } else {
            http_response_code(404);
            echo json_encode(array(
                "status" => 0,
                "message" => "Access Denied",
            ));
        }
    } else {
        http_response_code(503);
        echo json_encode(array(
            "status" => 0,
            "message" => "Fill All Fields",
        ));
    }
} else {
    http_response_code(422);
    echo json_encode(array(
        "status" => 0,
        "message" => "Page not found",
    ));
}
