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
include_once '../objects/leads.php';

$database = new Database();
$db = $database->getConnection();

$postback = new Postback($db);

$data = json_decode(file_get_contents("php://input"));

$success = true;
//!empty($data->tr_id) &&
//!empty($data->phone) &&
//!empty($data->amount) &&
//!empty($data->tr_date)
//if(empty($data->tr_id)){
//    $success = false;
//}



if ($success) {

    $postback->tr_id = $data->tr_id;
    $postback->client_id = $data->client_id;
    $postback->cl_email = isset($data->cl_email) ? $data->cl_email : '';
    $postback->phone = $data->phone;
    $postback->full_name = isset($data->full_name) ? $data->full_name : '';
    $postback->amount = $data->amount;
    $postback->tr_date = $data->tr_date;
//    $postback->aff_id = $AFF_ID;


    $data->tr_date = date('Y-m-d H:i:s', strtotime($data->tr_date));


	if($postback->create()){
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