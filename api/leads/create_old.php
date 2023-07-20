<?php
global $AFF_ID;
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// получаем соединение с базой данных
include_once '../config/db.php';

// создание объекта
include_once '../objects/leads.php';
include_once '../objects/affs.php';
include_once '../objects/users.php';

$database = new Database();
$db = $database->getConnection();

$leads = new Leads($db);
$Affiliate = new Affs($db);
$Users = new User($db);


// $leads->test();

// получаем отправленные данные
$data = json_decode(file_get_contents("php://input"));
//var_dump($data);
// $file = fopen('logs/Create.txt', "a");
// $date = date("m.d.Y, G:i:s");
// $text  = 'START------------------' . $date . "\r\n";
// $text .= json_encode($data) . "\r\n";
// $text .= "END----------------------------------------\r\n \r\n";
// fwrite($file, $text);
// fclose($file);
// убеждаемся, что данные не пусты

if (
	!empty($data->fname) &&
	!empty($data->lname) &&
	!empty($data->email) &&
	!empty($data->phone) &&
	!empty($data->country) &&
	!empty($data->lang) &&
	!empty($data->campaing_id) &&
	!empty($data->pass)
) {

	if(empty($data->c_cid)){
		$data->c_cid = "0";
	}

	// устанавливаем значения свойств
	$leads->fname = $data->fname; //string 100 characters
	$leads->lname = $data->lname; //string 100 characters
	$leads->email = $data->email; //string 100 characters
	$leads->phone = $data->phone; //string 20 characters format +1, +7
	$leads->country = $data->country; //string 3 characters format ISO A2 3166-1 US, RU...
	$leads->aff_id = $AFF_ID; // розкодируем сами с токена
	$leads->lang = $data->lang; // string 3 characters format ISO 639-1 EN, FR , RU...
	$leads->campaing_id = $data->campaing_id; // хз откуда брать пока что
	$leads->pass = $data->pass; // string - 123test123*
	$leads->currency = $data->currency; // USD, EUR...
//	$leads->terms = $data->terms;
	$leads->c_cid = $data->c_cid;
	$leads->a_aid = $data->a_aid;
	$leads->b_bid = $data->b_bid;
	$leads->ip = $data->ip;
	$leads->domain = $data->domain;
	$leads->date = date('Y-m-d H:i:s');

	$leads->brand = $leads->get_camp_brand($leads->campaing_id);

//	$autologine = 'https://backend.globalallianceltd.com/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//
//	switch ($leads->brand) {
//		case '2':
//			$autologine = 'https://backend.globalallianceltd.com/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//			break;
//
//		case '4':
//			$autologine = 'https://mngr.rockcapital.io/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//			break;
//
//	}
//
//$autologine = 'https://lightmarkets.online/login?email=' . $data->email . '&password=' . $data->pass . '';


//	$leads->click_id = $data->click_id;
//	$leads->source = $data->source;
//	$leads->ip = $data->ip;
//	$leads->domain = $data->domain;
//	$leads->utm_source = $data->utm_source;
//	$leads->utm_campaing = $data->utm_campaing;
//	$leads->utm_media = $data->utm_media;
//	$leads->utm_tern = $data->utm_tern;
//	$leads->utm_content = $data->utm_content;
//	$leads->status = $data->status;



//	$leads->created = date('Y-m-d H:i:s');
$file_name = date("Y-m-d");

$file = fopen('logs/create/'.$file_name.'.log', "a");
$date = date("m.d.Y, G:i:s");
$text  = 'START------------------' . $date . "\r\n";
$text .= json_encode($leads) . "\r\n";
$text .= "END----------------------------------------\r\n \r\n";
fwrite($file, $text);
fclose($file);


/*
* Проверяем, принадлежит ли аффилейт кампейну
*/
$aff_campaigns = $Affiliate->get_aff_camps($AFF_ID);
if(!in_array($leads->campaing_id, $aff_campaigns)){
	http_response_code(503);

	die(json_encode(array("message" => "Unable to create.", "description" => "Invalid campaign"), JSON_UNESCAPED_UNICODE));
}

	// создание
    $insert_id = $leads->create();
    $message = $tmp_id = $insert_id;

    $file = fopen('logs/create/'.$file_name.'.log', "a");
$date = date("m.d.Y, G:i:s");
$text  = 'START------------------' . $date . "\r\n";
$text .= $insert_id . "\r\n";
$text .= "END----------------------------------------\r\n \r\n";
fwrite($file, $text);
fclose($file);

	if (is_numeric($insert_id)) {

		// установим код ответа - 201 создано
		http_response_code(201);

		// сообщим пользователю
		if($leads->status == 'new'){
			echo json_encode(array("message" => "User $data->email, was created.", "id" => "$insert_id", "data" => $data), JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode(array("message" => "User $data->email, was created.", "id" => "$insert_id", "data" => $data), JSON_UNESCAPED_UNICODE);
		}
		
	} // если не удается создать , сообщим пользователю
	else {

		// установим код ответа - 503 сервис недоступен
		http_response_code(503);

		// сообщим пользователю
		echo json_encode(array("message" => "Unable to create.", "description" => "$message"), JSON_UNESCAPED_UNICODE);
	}
} // сообщим пользователю что данные неполные
else {

	// установим код ответа - 400 неверный запрос
	http_response_code(400);

	// сообщим пользователю
	echo json_encode(array("message" => "Unable to create. The data is incomplete."), JSON_UNESCAPED_UNICODE);
}

exit;