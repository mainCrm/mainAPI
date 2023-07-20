<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// подключение файла для соединения с базой и файл с объектом
include_once '../config/db.php';
include_once '../objects/users.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// подготовка объекта
$user = new User($db);

// установим свойство ID записи для чтения
$user->id = isset($_GET['id']) ? $_GET['id'] : die();

// прочитаем детали для редактирования
$user->readOne();

if ($user->full_name!=null) {

	// создание массива
	$user_data = array(
		"id" =>  $user->id,
		"dask" => $user->dask,
		"full_name" => $user->full_name,
		"lang" => $user->lang,
		"countries" => $user->countries,
		"foto" => $user->foto,
		"online" => $user->online,
		"date" => $user->date,
		"status" => $user->status,
	);

	// код ответа - 200 OK
	http_response_code(200);

	// вывод в формате json
	echo json_encode($user_data, JSON_UNESCAPED_UNICODE);
}

else {
	// код ответа - 404 Не найдено
	http_response_code(404);

	// сообщим пользователю, что не существует
	echo json_encode(array("message" => "User does not exist."), JSON_UNESCAPED_UNICODE);
}
?>