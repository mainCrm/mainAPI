<?php

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// получаем соединение с базой данных
include_once '../config/db.php';

// создание объекта
include_once '../objects/users.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// получаем отправленные данные
$data = json_decode(file_get_contents("php://input"));
//var_dump($data);
// убеждаемся, что данные не пусты
if (
	!empty($data->full_name)
) {

	// устанавливаем значения свойств
	$user->crm_id = $data->crm_id;
	$user->full_name = $data->full_name;
	$user->dask = $data->dask;
	$user->online = $data->online;
	$user->status = $data->status;
	$user->foto = $data->foto;
	$user->role = $data->role;
	$user->email = $data->email;
	$user->password = $data->password;

	$user->date = date('Y-m-d H:i:s');

	// создание
	if ($user->create()) {

		// установим код ответа - 201 создано
		http_response_code(201);

		// сообщим пользователю
		echo json_encode(array("message" => "User $data->full_name , was created.", "data" => $data), JSON_UNESCAPED_UNICODE);
	} // если не удается создать , сообщим пользователю
	else {

		// установим код ответа - 503 сервис недоступен
		http_response_code(503);

		// сообщим пользователю
		echo json_encode(array("message" => "Unable to create."), JSON_UNESCAPED_UNICODE);
	}
}
// сообщим пользователю что данные неполные
else {

	// установим код ответа - 400 неверный запрос
	http_response_code(400);

	// сообщим пользователю
	echo json_encode(array("message" => "Unable to create. The data is incomplete."), JSON_UNESCAPED_UNICODE);
}
