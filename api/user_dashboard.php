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

    //how to pass token inside header
    $all_header = getallheaders();
    $data = $all_header['Authorization'];

    if (!empty($data)) {

        try {
            $secrect_key = "owt123";

            $decode_data = JWT::decode($data, $secrect_key, array("HS512"));

            $user_id=$decode_data->data->id;
            $user_name=$decode_data->data->name;

            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "message" => "WELCOME TO YOUR DASHBOARD MR. $user_name",
                "user_data" => $decode_data,
                "user_id" => $user_id,

            ));
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage(),
            ));

        }
        
    }

}
