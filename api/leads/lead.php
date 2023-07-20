<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// подключение файла для соединения с базой и файл с объектом
include_once '../config/db.php';
include_once '../objects/leads.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// подготовка объекта
$lead = new Leads($db);

// установим свойство ID записи для чтения
$lead->email = isset($_GET['s']) ? $_GET['s'] : '';
//$lead->company_id = isset($_GET['id']) ? $_GET['id'] : '';
// прочитаем детали для редактирования
$lead->readOne();

if($AFF_ID != $lead->aff_id){
	http_response_code(404);

	exit(json_encode(array("message" => "Lead does not exist."), JSON_UNESCAPED_UNICODE));
}


if ($lead->email!=null) {

$ftd = false;
if($lead->ftd == 'true'){
	$ftd = true;
}
	// создание массива
	$lead_data = array(
		"id" => $lead->id,
//		"lead_id" => $lead_id,
		"fname" => $lead->fname,
		"lname" => $lead->lname,
		"email" => $lead->email,
		"fullphone" => $lead->fullphone,
//		"country" => $country,
//		"aff_id" => $aff_id,
		"campaing_id" => $lead->campaing_id,
//		"click_id" => $click_id,
//		"source" => $source,
//		"ip" => $ip,
//		"pass" => $pass,
//		"domain" => $domain,
//		"utm_source" => $utm_source,
//		"utm_campaing" => $utm_campaing,
//		"utm_media" => $utm_media,
//		"utm_tern" => $utm_tern,
//		"utm_content" => $utm_content,
		"status" => $lead->crm_status,
//		"manager" => $manager,
		"date" => $lead->date,
		// "ftd" => $ftd,
	);
	if($ftd){
		// $lead_data['ftd_date'] = $lead->ftd_date;
	}

	// код ответа - 200 OK
	http_response_code(200);

	// вывод в формате json
	echo json_encode($lead_data, JSON_UNESCAPED_UNICODE);
}

else {
	// код ответа - 404 Не найдено
	http_response_code(404);

	// сообщим пользователю, что не существует
	echo json_encode(array("message" => "Lead $lead->id, does not exist."), JSON_UNESCAPED_UNICODE);
}
?>