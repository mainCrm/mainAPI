<?php
global $AFF_ID;
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение базы данных и файл, содержащий объекты
include_once '../config/db.php';
include_once '../objects/leads.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// инициализируем объект
$leads = new Leads($db);
$leads->aff_id = $AFF_ID;

// запрашиваем юзеры
$stmt = $leads->read($AFF_ID);
$num = $stmt->rowCount();
// var_dump($stmt);
// проверка, найдено ли больше 0 записей
if ($num>0) {

	// массив юзеров
	$leads_arr=array();
	$leads_arr["records"]=array();

	// получаем содержимое нашей таблицы
	// fetch() быстрее, чем fetchAll()
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

		// извлекаем строку
		extract($row);

		$leads_data=array(
			"id" => $row['id'],
			"email" => $row['email'],
			"crm_status" => $row['crm_status'],
			"FTD" => $row['FTD'],
			"Date" => $row['date']

		);

		array_push($leads_arr["records"], $leads_data);
	}

	// устанавливаем код ответа - 200 OK
	http_response_code(200);

	// выводим данные в формате JSON
	echo json_encode($leads_arr);
}

else {

	// установим код ответа - 404 Не найдено
	http_response_code(404);

	// сообщаем пользователи не найдены
	echo json_encode(array("message" => "Leads not found."), JSON_UNESCAPED_UNICODE);
}