<?php
ini_set("display_errors", 1);
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

    if (!empty($data->name) && !empty($data->password) && !empty($data->email) && !empty($data->mobile) && !empty($data->address)) {

        $user_obj->name = $data->name;
        $user_obj->password = password_hash($data->password, PASSWORD_DEFAULT);
        $user_obj->email = $data->email;
        $user_obj->mobile = $data->mobile;
        $user_obj->address = $data->address;

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(array(
                "status" => 0,
                "message" => "Invalid Email Address!",
            ));
            exit;
        }

        if(strlen($data->password) < 8) {
            http_response_code(422);
            echo json_encode(array(
                "status" => 0,
                "message" => "Your password must be at least 8 characters long!",
            ));
            exit;
        }
            

        if (strlen($data->name) < 5) {
            http_response_code(422);
            echo json_encode(array(
                "status" => 0,
                "message" => "Your name must be at least 5 characters long!",
            ));
            exit;
        }

        $data = $user_obj->checkEmail();

        if (empty($data)) {

            if ($user_obj->createUser()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Created successfully",
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "fail to Create User",
                ));
            }
        } else {
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => "Email already exist",
            ));
        }

    } else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 0,
            "message" => "fill all feilds",
        ));
    }

} else {
    http_response_code(503);
    echo json_encode(array(
        "status" => 0,
        "message" => "Access Denied",
    ));
}
