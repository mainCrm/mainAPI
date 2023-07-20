<?php
// необходимые HTTP-заголовки 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение базы данных и файл, содержащий объекты 
include_once '../config/db.php';
include_once '../objects/users.php';

// получаем соединение с базой данных 
$database = new Database();
$db = $database->getConnection();

// инициализируем объект 
$user = new User($db);
 
// запрашиваем юзеры
$stmt = $user->read();
$num = $stmt->rowCount();
// var_dump($num);
// проверка, найдено ли больше 0 записей 
if ($num>0) {

    // массив юзеров
    $user_arr=array();
    $user_arr["records"]=array();

    // получаем содержимое нашей таблицы 
    // fetch() быстрее, чем fetchAll() 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // извлекаем строку 
        extract($row);

		$user_data = array(
			"id" =>  $id,
			"dask" => $dask,
			"full_name" => $full_name,
			"lang" => $lang,
			"countries" => $countries,
			"foto" => $foto,
			"online" => $online,
			"date" => $date,
			"status" => $status,
		);

        array_push($user_arr["records"], $user_data);
    }

    // устанавливаем код ответа - 200 OK 
    http_response_code(200);

    // выводим данные в формате JSON
    echo json_encode($user_arr);
}

else {

    // установим код ответа - 404 Не найдено
    http_response_code(404);

    // сообщаем пользователи не найдены
    echo json_encode(array("message" => "User not found."), JSON_UNESCAPED_UNICODE);
}