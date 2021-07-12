<?php
ini_set("display_errors", 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once '../config/Database2.php';
include_once '../Mudoles/Class.php';

$database = new Database();
$db = $database->getConnection();

$user_obj = new User($db);

if ($_SERVER['REQUEST_METHOD'] === "GET") {

    $chats = $user_obj->chatRoom();

    if ($chats->num_rows > 0) {

        $chats_array = array();

        while ($row = $chats->fetch_assoc()) {

            $chats_array[] = array(
                "id" => $row["id"],
                "SENT BY" => $row["senderName"],
                "MESSAGE" => $row["message"],
                "SENT AT" => $row["createdAt"],
            );

        }
        http_response_code(200);
        echo json_encode(array(
            "status" => 1,
            "chatRoom" => $chats_array,
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "status" => 0,
            "message" => "fail to insert",
        ));
    }
}
