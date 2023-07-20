<?php
// необходимые HTTP-заголовки
global $AFF_ID;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение необходимых файлов
include_once '../config/core.php';
include_once '../config/db.php';
include_once '../objects/leads.php';

// создание подключения к БД
$database = new Database();
$db = $database->getConnection();

// инициализируем объект
$lead= new Leads($db);

// получаем ключевые слова
$keywords=isset($_GET["id"]) ? $_GET["id"] : "";
//$keywords=isset($_GET["email"]) ? $_GET["email"] : "";
//$keywords=isset($_GET["ftd"]) ? $_GET["ftd"] : "";
//$keywords=isset($_GET["crm_status"]) ? $_GET["crm_status"] : "";

$lead->id = $keywords;
$lead->aff_id = $AFF_ID;

$page = isset($_GET["p"]) ? $_GET["p"] : 0;
$page=htmlspecialchars(strip_tags($page));
if($page>0){
    $page--;
}else{
    $page = 0;
}

$only_ftd = isset($_GET["only_ftd"]) ? htmlspecialchars(strip_tags($_GET["only_ftd"])) : '0';

$ftd_from = isset($_GET["ftd_from"]) ? htmlspecialchars(strip_tags($_GET["ftd_from"])) : 0;
$ftd_to = isset($_GET["ftd_to"]) ? htmlspecialchars(strip_tags($_GET["ftd_to"])) : date("Y-m-d G:i:s");

$reg_from = isset($_GET["reg_from"]) ? htmlspecialchars(strip_tags($_GET["reg_from"])) : 0;
$reg_to = isset($_GET["reg_to"]) ? htmlspecialchars(strip_tags($_GET["reg_to"])) : date("Y-m-d G:i:s");

$per_page = isset($_GET["per_page"]) ? htmlspecialchars(strip_tags($_GET["per_page"])) : 25;


//if($per_page > 250) $per_page = 250;
$lead->per_page = $per_page;

switch ($only_ftd){
    case 'true':
        $only_ftd = true;
        break;
    case 'false':
        $only_ftd = false;
        break;
    default:
        $only_ftd = 0;
        break;

}
//var_dump($ftd_from);
//var_dump($ftd_to);
//var_dump(strtotime($ftd_from));
//var_dump(strtotime($ftd_to));
// запрос
$stmt = $lead->search($keywords,$AFF_ID, $page, $only_ftd, $ftd_from, $ftd_to, $reg_from, $reg_to, true, $per_page);

// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// var_dump($row);

$num = $stmt->rowCount();



// проверяем, найдено ли больше 0 записей
if ($num>0) {

	// массив
	$lead_data=array();
	$lead_data["records"]=array();

	// получаем содержимое нашей таблицы
	// fetch() быстрее чем fetchAll()

    $aff_70_ftd_to = strtotime(date('2021-12-01'));


	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// извлечём строку
		extract($row);

        $ftd = $row['FTD'];
        $ftd_date = $row['FTD_date'];
        if($AFF_ID == 70){
            if(strtotime($ftd_date) >= $aff_70_ftd_to){
                $ftd = null;
                $ftd_date = null;
            }
            if(strpos($crm_status, 'Full deposit') !== false){
                $crm_status = 'No answer more 9';
            }
        }
        

		$lead_arr=array(
			"id" => $row['crm_id'],
			"email" => $email,
			"crm_status" => $crm_status,
			"FTD" => $ftd,
			"Date" => $row['date'],
			"FTD_date" => $ftd_date,
//			"surname" => $surname,
		);

		array_push($lead_data["records"], $lead_arr);
	}

	// код ответа - 200 OK
	http_response_code(200);



	$total_leads = $lead->search($keywords,$AFF_ID, $page, $only_ftd, $ftd_from, $ftd_to, $reg_from, $reg_to, false, $per_page);
    $total_leads =  $total_leads->rowCount();
	$per_page = $lead->per_page;
	$current_page = $page+1;
	$total_pages = ceil($total_leads/$per_page);


	$pagination = [
	    "total" => $total_leads,
        "count" => $num,
        "per_page" => $per_page,
        "current_page" => $current_page,
        "total_pages" => $total_pages,
        "links" => [

        ]
    ];


	if($current_page > 1){
        $current_url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $current_url = parse_url($current_url);
        parse_str($current_url['query'],$query);
        if(isset($query['p'])){
            $query['p']--;
            $current_url['query'] = http_build_query($query);
            $url = $lead->build_url($current_url);
//            $url = str_replace('&', '&amp;', $url);
            $pagination['links']['prev'] = $url;
        }

    }
	if($current_page < $total_pages){
        $current_url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $current_url = parse_url($current_url);
        parse_str($current_url['query'],$query);
        if(isset($query['p'])){
            $query['p']++;
        }else{
            $query['p'] = 2;
        }
        $current_url['query'] = http_build_query($query);
        $url = $lead->build_url($current_url);
//        $url = str_replace("&", "&amp;", $url);
        $pagination['links']['next'] = $url;
    }

    $lead_data["pagination"]=array();
    array_push($lead_data["pagination"], $pagination);

    // покажем
    echo json_encode($lead_data);


}

else {
	// код ответа - 404 Ничего не найдено
	http_response_code(404);

	// скажем пользователю, что  не найдены
	echo json_encode(array("message" => "User not found."), JSON_UNESCAPED_UNICODE);
}
?>