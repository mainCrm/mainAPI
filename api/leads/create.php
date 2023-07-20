<?php
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


// $leads->test();

// получаем отправленные данные
$data = json_decode(file_get_contents("php://input"));
//var_dump($data);
// $file = fopen('logs/Create.txt', "a");
// $date = date("m.d.Y, G:i:s");
// $text  = 'START------------------' . $date . "\r\n";
// $text .= json_encode($data) . "\r\n";
// $text .= "END----------------------------------------\r\n \r\n";
// fwrite($file, $text);
// fclose($file);
// убеждаемся, что данные не пусты

if (
	!empty($data->fname) &&
	!empty($data->lname) &&
	!empty($data->email) &&
	!empty($data->phone) &&
	!empty($data->country) &&
	!empty($data->lang) &&
//	!empty($data->campaing_id) &&
	!empty($data->pass)
) {

    if(isset($data->master_id) && !empty($data->master_id)){
        $leads->master_id = $data->master_id;
    }
    if(isset($data->eagles_master_id) && !empty($data->eagles_master_id)){
        $leads->eagles_master_id = $data->eagles_master_id;
    }
    
	if(empty($data->c_cid)){
		$data->c_cid = "0";
	}




    if(isset($data->dynamic) && !empty($data->dynamic)){
        $leads->dynamic_id = $AFF_ID;
        $dynamic = explode("_", trim($data->dynamic));

        if(!isset($dynamic[0])){
            http_response_code(503);
            die(json_encode(array("message" => "Unable to create.", "description" => "Invalid affiliate"), JSON_UNESCAPED_UNICODE));
        }
        if(!isset($dynamic[1])){
            http_response_code(503);
            die(json_encode(array("message" => "Unable to create.", "description" => "Invalid campaign"), JSON_UNESCAPED_UNICODE));
        }
        $affiliate_id = $dynamic[0];
        $campaign_id = $dynamic[1];



        $affiliate = $Affiliate->get_one_by_dynamic_name($affiliate_id);
        $campaign = $Campaings->get_one_by_dynamic_name($campaign_id);

        if(!$affiliate){

            $aff_email = str_replace(' ', '_', $affiliate_id);
            $affiliate_data = [
                "dynamic_name" => $affiliate_id,
                "name" => $affiliate_id,
                "email" => $aff_email . "@" . $aff_email . ".com",
                "postback" => "",
                "token" => $Affiliate->generate_token(),
                "payout_CPA" => 0,
                "payout_CPL" => 0,
                "ip" => "",
                "countries" => $App->get_countries(),
            ];
            $affiliate_data = (object) $affiliate_data;

            $affiliate_id = $Affiliate->create($affiliate_data, false);
            if(!is_numeric($affiliate_id)){
                http_response_code(503);
                die(json_encode(array("message" => "Unable to create.", "description" => $affiliate_id), JSON_UNESCAPED_UNICODE));
            }
        }else{
            $affiliate_id = $affiliate['id'];
        }

        if(!$campaign){

//            $aff_brands = $Affiliate->get_brands($leads->aff_id);
//            if(!$aff_brands){
//                $aff_brands = [
//                    [
//                        "brand_id" => 2,
//                    ]
//
//                ];
//            }
//            $brands = [];
//            foreach ($aff_brands as $brand){
//                array_push($brands, $brand['brand_id']);
//            }

            $brands = [];

            $all_brands = $Brands->get_all();
            foreach ($all_brands as $brand){
                array_push($brands, $brand['id']);
            }

            $campaign_data = [
                "dynamic_name" => $campaign_id,
                "name" => $campaign_id,
                "url" => "",
                "aff_id" => $affiliate_id,
                "brands" => $brands,
                "countries" => $App->get_countries(),
            ];
            $campaign_data = (object) $campaign_data;

            $campaign_id = $Campaings->create($campaign_data, false);
            if(!is_numeric($campaign_id)){
                http_response_code(503);
                die(json_encode(array("message" => "Unable to create.", "description" => $campaign_id), JSON_UNESCAPED_UNICODE));
            }
        }else{
            $campaign_id = $campaign['id'];
        }

        $AFF_ID = $affiliate_id;
        $data->campaing_id = $campaign_id;

        $aff_campaigns = $Affiliate->get_aff_camps($affiliate_id);
        if(!in_array($campaign_id, $aff_campaigns)){
            $App->insert('cmaps_affs', [
                "aff_id" => $affiliate_id,
                "campaign_id" => $campaign_id,
            ]);
        }
    }
    
    
	// устанавливаем значения свойств
	$leads->fname = $data->fname; //string 100 characters
	$leads->lname = $data->lname; //string 100 characters
	$leads->email = $data->email; //string 100 characters
	$leads->phone = $data->phone; //string 20 characters format +1, +7
	$leads->country = $data->country; //string 3 characters format ISO A2 3166-1 US, RU...
	$leads->aff_id = $AFF_ID; // розкодируем сами с токена
	$leads->lang = $data->lang; // string 3 characters format ISO 639-1 EN, FR , RU...
//	$leads->campaing_id = $data->campaing_id; // хз откуда брать пока что
	$leads->pass = $data->pass; // string - 123test123*
	$leads->currency = $data->currency; // USD, EUR...
//	$leads->terms = $data->terms;
	$leads->c_cid = $data->c_cid;
	$leads->a_aid = $data->a_aid;
	$leads->b_bid = $data->b_bid;
	$leads->ip = $data->ip;
	$leads->domain = $data->domain;

    if(empty($data->date)){
        $leads->date = date('Y-m-d H:i:s');
    }else{
        $leads->date = $data->date;
    }


    if(isset($data->campaing_id) && !empty($data->campaing_id) &&
        isset($data->camp) && !empty($data->camp)
    ){
        http_response_code(503);
        die(json_encode(array("message" => "Unable to create.", "description" => "Invalid campaign"), JSON_UNESCAPED_UNICODE));
    }

    if(isset($data->camp) && !empty($data->camp)){
        $data->camp = trim($data->camp);
//        if(isset($Campaings->spreadsheets_campaigns[$data->camp])){
//            $data->camp = $Campaings->spreadsheets_campaigns[$data->camp];
//        }
        $campaign = $Campaings->get_campaign_by_name($data->camp);
        if(!$campaign){

            $aff_brands = $Affiliate->get_brands($leads->aff_id);
            if(!$aff_brands){
                $aff_brands = [
                    [
                        "brand_id" => 2,
                    ]

                ];
            }
            $brands = [];
            foreach ($aff_brands as $brand){
                array_push($brands, $brand['brand_id']);
            }


            $countries = $App->get_countries();

            $campaign_data = [];
            $campaign_data['name'] = $data->camp;
            $campaign_data['url'] = '';
            $campaign_data['countries'] = $countries;
            $campaign_data['brands'] = $brands;
            $campaign_data['aff_id'] = $leads->aff_id;
            $campaign_data = (object) $campaign_data;


            $campaign_id = $Campaings->create($campaign_data, false);
            if(!is_numeric($campaign_id)){
                http_response_code(503);
                die(json_encode(array("message" => "Unable to create.", "description" => $campaign_id), JSON_UNESCAPED_UNICODE));
            }
        }else{
            $campaign_id = $campaign['id'];

            $aff_campaigns = $Affiliate->get_aff_camps($AFF_ID);
            if(!in_array($campaign_id, $aff_campaigns)){
                $aff_brands = $Affiliate->get_brands($leads->aff_id);
                $all_camp_brands = $Campaings->get_brands($campaign_id);
                $camp_brands = [];
                foreach ($all_camp_brands as $brand){
                    array_push($camp_brands, $brand['brand_id']);
                }
                if(!$aff_brands){
                    $aff_brands = [
                        [
                            "brand_id" => 2,
                        ]
                    ];
                }
                foreach ($aff_brands as $brand){
                    if(!in_array($brand['brand_id'], $camp_brands)){
                        $App->insert('brand_camps', [
                            "brand_id" => $brand['brand_id'],
                            "camp_id" => $campaign_id,
                        ]);

//                        $App->delete('camps_users', ['campaign_id', '=', $campaign_id]);
                        $users = $Users->get_brand_users($brand['brand_id']);
                        foreach ($users as $user_id){
                            $App->insert('camps_users', [
                                "campaign_id" => $campaign_id,
                                "user_id" => $user_id,
                            ]);
                        }
                    }
                }

                $App->insert('cmaps_affs', [
                    "aff_id" => $leads->aff_id,
                    "campaign_id" => $campaign_id,
                ]);
            }

        }
        $leads->campaing_id = $campaign_id;
    }else{

        if(isset($data->campaing_id) && !empty($data->campaing_id)){
            $leads->campaing_id = $data->campaing_id;
        }else{
            http_response_code(503);
            die(json_encode(array("message" => "Unable to create.", "description" => "Invalid campaign"), JSON_UNESCAPED_UNICODE));
        }
    }

	$leads->brand = $leads->get_camp_brand($leads->campaing_id);

//	$autologine = 'https://backend.globalallianceltd.com/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//
//	switch ($leads->brand) {
//		case '2':
//			$autologine = 'https://backend.globalallianceltd.com/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//			break;
//
//		case '4':
//			$autologine = 'https://mngr.rockcapital.io/api/autologin?email=' . $data->email . '&password=' . $data->pass . '';
//			break;
//
//	}
//
//$autologine = 'https://lightmarkets.online/login?email=' . $data->email . '&password=' . $data->pass . '';


//	$leads->click_id = $data->click_id;
//	$leads->source = $data->source;
//	$leads->ip = $data->ip;
//	$leads->domain = $data->domain;
//	$leads->utm_source = $data->utm_source;
//	$leads->utm_campaing = $data->utm_campaing;
//	$leads->utm_media = $data->utm_media;
//	$leads->utm_tern = $data->utm_tern;
//	$leads->utm_content = $data->utm_content;
//	$leads->status = $data->status;



//	$leads->created = date('Y-m-d H:i:s');
$file_name = date("Y-m-d");

$file = fopen('logs/create/'.$file_name.'.log', "a");
$date = date("m.d.Y, G:i:s");
$text  = 'START------------------' . $date . "\r\n";
$text .= json_encode($leads) . "\r\n";
$text .= "END----------------------------------------\r\n \r\n";
fwrite($file, $text);
fclose($file);


/*
* Проверяем, принадлежит ли аффилейт кампейну
*/
$aff_campaigns = $Affiliate->get_aff_camps($AFF_ID);
if(!in_array($leads->campaing_id, $aff_campaigns)){
	http_response_code(503);

	die(json_encode(array("message" => "Unable to create.", "description" => "Invalid campaign"), JSON_UNESCAPED_UNICODE));
}

    $campaing = $Campaings->get_one($leads->campaing_id);
    if(isset($campaing['name'])){
        $campaing_name = $campaing['name'];
    }else{
        $campaing_name = $leads->campaing_id;
    }

    $leads->campaing_name = $campaing_name;

    if(isset($data->spreadsheet_id) && !empty($data->spreadsheet_id)){
        $leads->spreadsheet_id = htmlspecialchars(strip_tags($data->spreadsheet_id));
    }

	// создание
    $insert_id = $leads->create();
    $message = $tmp_id = $insert_id;

    $file = fopen('logs/create/'.$file_name.'.log', "a");
$date = date("m.d.Y, G:i:s");
$text  = 'START------------------' . $date . "\r\n";
$text .= $insert_id . "\r\n";
$text .= "END----------------------------------------\r\n \r\n";
fwrite($file, $text);
fclose($file);

	if (is_numeric($insert_id)) {

		// установим код ответа - 201 создано
		http_response_code(200);

		// сообщим пользователю
		if($leads->status == 'new'){
			echo json_encode(array("message" => "User $data->email, was created.", "id" => "$insert_id", "data" => $data), JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode(array("message" => "User $data->email, was created.", "id" => "$insert_id", "data" => $data), JSON_UNESCAPED_UNICODE);
		}
		
	} // если не удается создать , сообщим пользователю
	else {

		// установим код ответа - 503 сервис недоступен
		http_response_code(503);

		// сообщим пользователю
		echo json_encode(array("message" => "Unable to create.", "description" => "$message"), JSON_UNESCAPED_UNICODE);
	}
} // сообщим пользователю что данные неполные
else {

	// установим код ответа - 400 неверный запрос
	http_response_code(400);

	// сообщим пользователю
	echo json_encode(array("message" => "Unable to create. The data is incomplete."), JSON_UNESCAPED_UNICODE);
}

exit;