<?php
global $AFF_ID;
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../config/db.php';


include_once '../objects/postback.php';

$database = new Database();
$db = $database->getConnection();

$postback = new Postback($db);

$data = json_decode(file_get_contents("php://input"));


if (
	!empty($data->email) &&
	!empty($data->status)
) {

	if($postback->lead_status($data)){
        http_response_code(200);
        echo json_encode(array("message" => "Success"), JSON_UNESCAPED_UNICODE);
    }else{
        http_response_code(503);
        echo json_encode(array("message" => "Error"), JSON_UNESCAPED_UNICODE);
    }



}
else {
	http_response_code(400);
	echo json_encode(array("message" => "Unable to create. The data is incomplete."), JSON_UNESCAPED_UNICODE);
}

exit;