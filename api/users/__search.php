<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение необходимых файлов
include_once '../config/core.php';
include_once '../config/db.php';
include_once '../objects/users.php';

// создание подключения к БД
$database = new Database();
$db = $database->getConnection();

// инициализируем объект
$user = new User($db);

// получаем ключевые слова
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
$user->email = $keywords;
// запрос
$stmt = $user->search($keywords);

// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// var_dump($row);
$num = $stmt->rowCount();



// проверяем, найдено ли больше 0 записей
if ($num>0) {

	// массив
	$user_data=array();
	$user_data["records"]=array();

	// получаем содержимое нашей таблицы
	// fetch() быстрее чем fetchAll()
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// извлечём строку
		extract($row);

		$user_arr=array(
			"id" => $id,
			"email" => $email,
			"forename" => $forename,
			"surname" => $surname,
		);

		array_push($user_data["records"], $user_arr);
	}

	// код ответа - 200 OK
	http_response_code(200);

	// покажем
	echo json_encode($user_data);
}

else {
	// код ответа - 404 Ничего не найдено
	http_response_code(404);

	// скажем пользователю, что  не найдены
	echo json_encode(array("message" => "User не найдены."), JSON_UNESCAPED_UNICODE);
}
?>