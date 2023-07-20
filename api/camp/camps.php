<?php
// необходимые HTTP-заголовки 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение базы данных и файл, содержащий объекты 
include_once '../config/db.php';
include_once '../objects/camps.php';

// получаем соединение с базой данных 
$database = new Database();
$db = $database->getConnection();

// инициализируем объект 
$camps = new Camps ($db);
 
// запрашиваем юзеры
$stmt = $camps->read();
$num = $stmt->rowCount();
// var_dump($num);
// проверка, найдено ли больше 0 записей 
if ($num>0) {

    // массив юзеров
	$camps_arr=array();
	$camps_arr["records"]=array();

    // получаем содержимое нашей таблицы 
    // fetch() быстрее, чем fetchAll() 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // извлекаем строку 
        extract($row);

		$camps_data = array(
			"id" =>  $id,
			"url" => $url,
			"affiliates" => $affiliates,
			"country" => $country
		);

        array_push($camps_arr["records"], $camps_data);
    }

    // устанавливаем код ответа - 200 OK 
    http_response_code(200);

    // выводим данные в формате JSON
    echo json_encode($camps_arr);
}

else {

    // установим код ответа - 404 Не найдено
    http_response_code(404);

    // сообщаем пользователи не найдены
    echo json_encode(array("message" => "User not found."), JSON_UNESCAPED_UNICODE);
}