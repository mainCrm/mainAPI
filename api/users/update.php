<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// подключаем файл для работы с БД и объектом
include_once '../config/db.php';
include_once '../objects/users.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// подготовка объекта
$user = new User($db);

// получаем id  для редактирования
$data = json_decode(file_get_contents("php://input"));
//var_dump($data);
// установим id свойства  для редактирования
$user->id = $data->id;

// установим значения свойств
$user->dask = $data->dask;
$user->full_name = $data->full_name;
$user->lang = $data->lang;
$user->countries = $data->countries;
$user->foto = $data->foto;
$user->online = $data->online;
$user->status = $data->status;

// обновление
if ($user->update()) {

	// установим код ответа - 200 ok
	http_response_code(200);

	// сообщим пользователю
	echo json_encode(array("message" => "User: $data->full_name ,  has been updated.", "data" => $data), JSON_UNESCAPED_UNICODE);
}

// если не удается обновить , сообщим пользователю
else {

	// код ответа - 503 Сервис не доступен
	http_response_code(503);

	// сообщение пользователю
	echo json_encode(array("message" => "Unable to update."), JSON_UNESCAPED_UNICODE);
}
?>