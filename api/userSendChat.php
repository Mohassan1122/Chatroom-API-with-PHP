<?php
ini_set('display_errors', 1);

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

    $all_header = getallheaders();

    if (!empty($data->message)) {

        try {

            $jwt = $all_header['Authorization'];

            $secrect_key = "owt123";

            $decode_data = JWT::decode($jwt, $secrect_key, array("HS512"));

            $user_obj->senderName = $decode_data->data->name;
            $user_obj->message = $data->message;

            if ($user_obj->sendChat()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Message Sent !!",
                ));
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Message Not Sent",
                ));
            }

        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage(),
            ));

        }

    } else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 0,
            "message" => "fill all field",
        ));
    }
} else {
    http_response_code(404);
    echo json_encode(array(
        "status" => 0,
        "message" => "Access Denied",
    ));

}
