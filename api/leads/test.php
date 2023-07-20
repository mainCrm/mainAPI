<?php


for($i = 0; $i < 300; $i++){
    $curl = curl_init();

    $time = time() . $i;

    $postfields = [

        "fname" => "test",
        "lname" => "test",
        "email" => $time."@test.com",
        "phone" => $time,
        "country" => "ru",
        "lang" => "ru",
        "dynamic" => "test aff_test",
        "pass" => "test",
        "currency" => "USD",
        "ip" => "1.1.1.1",
        "domain" => "tett.com",
        "c_cid" => "test",
        "b_bid" =>"test",

    ];
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.tdsgosekta1.com/leads/create.php?api_key=b95e2bacad80383cc5c1658356783760',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($postfields),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}

exit;
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
include_once '../objects/camps.php';
include_once '../objects/app.php';
include_once '../objects/Brands.php';

$database = new Database();
$db = $database->getConnection();

$leads = new Leads($db);
$Affiliate = new Affs($db);
$Users = new User($db);
$Campaings = new Camps($db);
$App = new App($db);
$Brands = new Brands($db);

$leads->campaing_id = 55;
$leads->country = 'ru';
$leads->lang = 'ru';

$leads->algorithm_new();


exit;