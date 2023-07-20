<?php

//require_once("../Utility/Hash.php");

class Leads {

	// подключение к базе данных и таблице 'users'
	private $conn;
	private $table_name = "leads";

	// свойства объекта
	public $per_page = 25;
	public $id;
	public $lead_id;
	public $fname;
	public $lname;
	public $email;
	public $phone;
	public $country;
	public $lang;
	public $aff_id;
	public $campaing_id;
	public $currency;
	public $pass;
	public $terms;
	public $a_aid;
	public $b_bid;
	public $c_cid;
		public $ip;
		public $domain;


//	public $click_id;
//	public $source;
//	public $ip;
//	public $pass;
//	public $domain;
//	public $utm_source;
//	public $utm_campaing;
//	public $utm_media;
//	public $utm_tern;
//	public $utm_content;
	public $status;
	public $FTD;
//	public $manager;
	public $date;


	// конструктор для соединения с базой данных
	public function __construct($db){
		$this->conn = $db;
	}

	// метод read() - получение user
	public function read($AFF_ID){

		// выбираем все записи



		$query = "SELECT * FROM " . $this->table_name . " WHERE  aff_id = '".$AFF_ID."' ";


		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		// очистка

		$AFF_ID=htmlspecialchars(strip_tags($AFF_ID));

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		// выполняем запрос
		$stmt->execute();

		return $stmt;
//		var_dump($query);
	}

	// метод create - создание user
	public function create(){


		// запрос для вставки (создания) записей
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
				fname=:fname, lname=:lname, email=:email, phone=:phone, country=:country, aff_id=:aff_id, lang=:lang, campaing_id=:campaing_id, date=:date, pass=:pass, currency=:currency, c_cid=:c_cid, a_aid=:a_aid, b_bid=:b_bid, ip=:ip, domain=:domain ";
//		var_dump($query);
		// подготовка запроса
		$stmt = $this->conn->prepare($query);
//		var_dump($stmt);
//		$password = $this->password;
//		$password = Utility\Hash::generate($password);

		// очистка
		$this->fname=htmlspecialchars(strip_tags($this->fname));
		$this->lname=htmlspecialchars(strip_tags($this->lname));
//		$this->email=strip_tags($this->email);
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->phone=htmlspecialchars(strip_tags($this->phone));
		$this->country=htmlspecialchars(strip_tags($this->country));
		$this->aff_id=htmlspecialchars(strip_tags($this->aff_id));
		$this->lang=htmlspecialchars(strip_tags($this->lang));
		$this->campaing_id=htmlspecialchars(strip_tags($this->campaing_id));
		$this->pass=htmlspecialchars(strip_tags($this->pass));
		$this->currency=htmlspecialchars(strip_tags($this->currency));
		$this->terms=htmlspecialchars(strip_tags($this->terms));
		$this->c_cid=htmlspecialchars(strip_tags($this->c_cid));
		$this->a_aid=htmlspecialchars(strip_tags($this->a_aid));
		$this->b_bid=htmlspecialchars(strip_tags($this->b_bid));
		$this->date=htmlspecialchars(strip_tags($this->date));
		$this->ip=htmlspecialchars(strip_tags($this->ip));
		$this->domain=htmlspecialchars(strip_tags($this->domain));

	// 	$file = fopen('Create.txt', "a");
	// $date = date("m.d.Y, G:i:s");
	// $text  = 'START------------------' . $date . "\r\n";
	// $text .= $this . "\r\n";
	// $text .= "END----------------------------------------\r\n \r\n";
	// fwrite($file, $text);
	// fclose($file);

//		$this->click_id=htmlspecialchars(strip_tags($this->click_id));
//		$this->source=htmlspecialchars(strip_tags($this->source));
//		$this->ip=htmlspecialchars(strip_tags($this->ip));
//		$this->domain=htmlspecialchars(strip_tags($this->domain));
//		$this->utm_source=htmlspecialchars(strip_tags($this->utm_source));
//		$this->utm_campaing=htmlspecialchars(strip_tags($this->utm_campaing));
//		$this->utm_media=htmlspecialchars(strip_tags($this->utm_media));
//		$this->utm_tern=htmlspecialchars(strip_tags($this->utm_tern));
//		$this->utm_content=htmlspecialchars(strip_tags($this->utm_content));
//		$this->status=htmlspecialchars(strip_tags($this->status));

//		$password=htmlspecialchars(strip_tags($password));
//		echo $this->fname;
//		echo "\n";
//		echo $this->lname;
//		echo "\n";
//		echo $this->email;
//		echo "\n";
//		echo $this->phone;
//		echo "\n";
//		echo $this->country;
//		echo "\n";
//		echo $this->aff_id;
//		echo "\n";
//		echo $this->lang;
//		echo "\n";
//		echo $this->campaing_id;
//		echo "\n";
//		echo $this->pass;
//		echo "\n";
//		echo $this->currency;
//		echo "\n";
//		echo $this->terms;
//		echo "\n";
//		echo $this->c_cid;
//		echo "\n";
//		echo $this->a_aid;
//		echo "\n";
//		echo $this->b_bid;
//		echo "\n";
//		echo $this->date;
//		echo "\n";

		// привязка значений
		$stmt->bindParam(":fname", $this->fname);
		$stmt->bindParam(":lname", $this->lname);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":phone", $this->phone);
		$stmt->bindParam(":country", $this->country);
		$stmt->bindParam(":aff_id", $this->aff_id);
		$stmt->bindParam(":lang", $this->lang);
		$stmt->bindParam(":campaing_id", $this->campaing_id);
		$stmt->bindParam(":pass", $this->pass);
		$stmt->bindParam(":currency", $this->currency);
//		$stmt->bindParam(":terms", $this->terms);
		$stmt->bindParam(":c_cid", $this->c_cid);
		$stmt->bindParam(":a_aid", $this->a_aid);
		$stmt->bindParam(":b_bid", $this->b_bid);
		$stmt->bindParam(":date", $this->date);
		$stmt->bindParam(":ip", $this->ip);
		$stmt->bindParam(":domain", $this->domain);
//
//		$stmt->bindParam(":click_id", $this->click_id);
//		$stmt->bindParam(":source", $this->source);
//		$stmt->bindParam(":ip", $this->ip);
//
//		$stmt->bindParam(":domain", $this->domain);
//		$stmt->bindParam(":utm_source", $this->utm_source);
//		$stmt->bindParam(":utm_campaing", $this->utm_campaing);
//		$stmt->bindParam(":utm_media", $this->utm_media);
//		$stmt->bindParam(":utm_tern", $this->utm_tern);
//		$stmt->bindParam(":utm_content", $this->utm_content);
//		$stmt->bindParam(":status", $this->status);

//		$stmt->bindParam(":password", $password);

	

		// выполняем запрос
		if ($stmt->execute()) {
//		    $Leads = new Leads();
            $lead_id = $this->conn->lastInsertId();
            $this->id = $lead_id;
		    $algorithm = $this->algorithm();
		    if(is_numeric($algorithm)){
                return $lead_id;
            }else{
                return $algorithm;
            }

			// return true;
		}

		return false;
	}

	// используется при заполнении формы обновления
	function readOne() {

		if ($this->email == '') {
			$query = "SELECT *
            FROM
                " . $this->table_name . "
                
            WHERE
                id = " . $this->id . " 
            LIMIT
                0,1";
		}if ($this->id == ''){
			$query = "SELECT *
            FROM
                " . $this->table_name . "
                
            WHERE
                email = '" . $this->email . "' 
            LIMIT
                0,1";
		}

		// подготовка запроса
		$stmt = $this->conn->prepare( $query );

		// привязываем id , который будет обновлен
		$stmt->bindParam(1, $this->id);

		// выполняем запрос
		$stmt->execute();

		// получаем извлеченную строку
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		// установим значения свойств объекта
		$this->id = $row['id'];
		$this->lead_id = $row['lead_id'];
		$this->fname = $row['fname'];
		$this->lname = $row['lname'];
		$this->email = $row['email'];
		$this->fullphone = $row['fullphone'];
		$this->country = $row['country'];
		$this->aff_id = $row['aff_id'];
		$this->campaing_id = $row['campaing_id'];
		$this->click_id = $row['click_id'];
		$this->source = $row['source'];
		$this->ip = $row['ip'];
		$this->pass = $row['pass'];
		$this->domain = $row['domain'];
		$this->utm_source = $row['utm_source'];
		$this->utm_campaing = $row['utm_campaing'];
		$this->utm_media = $row['utm_media'];
		$this->utm_tern = $row['utm_tern'];
		$this->utm_content = $row['utm_content'];
		$this->status = $row['status'];
		$this->manager = $row['manager'];
		$this->date = $row['date'];
	}

	// метод update() - обновление
	function update(){

		// запрос для обновления записи
		$query = "UPDATE
                " . $this->table_name . "
            SET
                FTD = :FTD
            WHERE
                email = :email";

		// подготовка запроса
		$stmt = $this->conn->prepare($query);
var_dump($stmt);
//		$password = $this->password;
//		$password = Utility\Hash::generate($password);
		// очистка
//		$this->status=htmlspecialchars(strip_tags($this->status));
		$this->FTD=htmlspecialchars(strip_tags($this->FTD));
//		$password=htmlspecialchars(strip_tags($password));
		$this->email=htmlspecialchars(strip_tags($this->email));


		// привязываем значения

//		$stmt->bindParam(':status', $this->status);
		$stmt->bindParam(':FTD', $this->FTD);
//		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':email', $this->email);

		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	// метод delete - удаление
//	function delete(){
//
//		// запрос для удаления записи
//		$query = "DELETE FROM " . $this->table_name . " WHERE id =".$this->id;
//
//		// подготовка запроса
//		$stmt = $this->conn->prepare($query);
//
//		// очистка
//		$this->id=htmlspecialchars(strip_tags($this->id));
//
//		// привязываем id записи для удаления
//		$stmt->bindParam(1, $this->id);
//
//		// выполняем запрос
//		if ($stmt->execute()) {
//			return true;
//		}
//
//		return false;
//	}

//	 метод search - поиск
	function search($keywords, $AFF_ID, $page = 0, $only_ftd, $ftd_from, $ftd_to, $reg_from, $reg_to, $paginate = true, $per_page = 25){

	    // $limit = $this->per_page;
	    $limit = $per_page;
	    $offset = $page*$limit;
		// выборка по всем записям
//		$query = "SELECT * FROM " . $this->table_name . " WHERE id = '".$keywords."' and aff_id = '".$AFF_ID."' ";

        //conditions
        $only_ftd_query =  $ftd_from_query = $ftd_to_query = $reg_from_query = $reg_to_query = "";

        if($only_ftd === true){
            $only_ftd_query = "AND FTD = 'true'";
        }elseif ($only_ftd === false){
            $only_ftd_query = "AND (FTD IS null OR FTD = '')";
        }

        if(!empty($ftd_from)){
            $ftd_from = strtotime($ftd_from);
            $ftd_from_query = "AND FTD_date > " . $ftd_from;
        }
        if(!empty($ftd_to) && (strtotime($ftd_to) != strtotime(date("Y-m-d G:i:s")))){
//            $ftd_to = strtotime($ftd_to);
            $ftd_to_query = "AND FTD_date < '" . $ftd_to . "'";
        }

        if(!empty($reg_from)){
//            $reg_from = strtotime($reg_from);
            $reg_from_query = "AND date > '" . $reg_from . "'";
        }
        if(!empty($reg_to) && (strtotime($reg_to) != strtotime(date("Y-m-d G:i:s")))){
//            $reg_to = strtotime($reg_to);
            $reg_to_query = "AND date < '" . $reg_to . "'";
        }

        if($paginate){
            $paginate = "LIMIT $limit OFFSET $offset";
        }else{
            $paginate= "";
        }

		$query = "SELECT * FROM " . $this->table_name . " WHERE  aff_id = '".$AFF_ID."' ".$only_ftd_query." ".$ftd_from_query." ".$ftd_to_query." ".$reg_from_query." ".$reg_to_query." " . $paginate;
//        var_dump($query);

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		// очистка
		$keywords=htmlspecialchars(strip_tags($keywords));
		$AFF_ID=htmlspecialchars(strip_tags($AFF_ID));
		$keywords = "%{$keywords}%";

		// привязка
		$stmt->bindParam(1, $keywords);
//		$stmt->bindParam(2, $keywords);
//		$stmt->bindParam(3, $keywords);

		// выполняем запрос
		$stmt->execute();
//		var_dump($stmt);
		return $stmt;
	}

    public function lead_count($keywords, $AFF_ID){

        $query = "SELECT * FROM " . $this->table_name . " WHERE  aff_id = '".$AFF_ID."' ";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $keywords=htmlspecialchars(strip_tags($keywords));
        $AFF_ID=htmlspecialchars(strip_tags($AFF_ID));
        $keywords = "%{$keywords}%";

        // привязка
        $stmt->bindParam(1, $keywords);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function build_url(array $parts) {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

//	public function readPaging($from_record_num, $records_per_page){
//
//		// выборка
//		$query = "SELECT
//                     id, email,  forename, surname, password
//                FROM
//                    " . $this->table_name . "
//                ORDER BY
//                    forename DESC
//            LIMIT ?, ?";
//
//		// подготовка запроса
//		$stmt = $this->conn->prepare( $query );
//
//		// свяжем значения переменных
//		$stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
//		$stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
//
//		// выполняем запрос
//		$stmt->execute();
//
//		// вернём значения из базы данных
//		return $stmt;
//	}

    public function algorithm(){

        $database = new Database();
        $connection = $database->getConnection();

            // algorithm Start

            $get_manager = $connection->prepare("SELECT

       users.*,
       IF(add_lead > 0 AND end_lead != add_lead, 1, 0) as quota,
       IF(countries.name like \"%". $this->country ."%\" and langs.name like \"%" . $this->lang . "%\", 1, 0) as lingua
	   from users 
		  INNER JOIN camps_users ON users.id = camps_users.user_id
			INNER JOIN campaigns ON camps_users.campaign_id = campaigns.id
			INNER JOIN country_users ON users.id = country_users.user_id
			INNER JOIN countries ON country_users.country_id = countries.id
			INNER JOIN langs_users ON langs_users.user_id = users.id
			INNER JOIN langs ON langs.id = langs_users.lang_id
	  	
	     where online = 'online' and (campaigns.id = " . $this->campaing_id . ") and ((countries.name like \"%". $this->country ."%\" and langs.name like \"%" . $this->lang . "%\") or (countries.name like \"%" . $this->country . "%\"))	   
	   # GROUP BY users.id
	   ORDER BY quota DESC, lingua DESC, get_last ASC;
	   "); //LIMIT 1

            $get_manager->execute();
            $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);
            if (empty($managers)) {
                $get_manager = $connection->prepare("
        SELECT
            users.*,
            IF(add_lead > 0 AND end_lead != add_lead, 1, 0) as quota
        from users where online = 'online' and role = 'super_manager'
        ORDER BY quota DESC, get_last ASC
        limit 1;");
                $get_manager->execute();
                $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);
            }



            $manager = $managers[0];

        $manager_id = $manager['id'];

        if(!empty($manager_id)){

//            var_dump(sprintf('lead %s, manager %s, crm_id %s', $this->id, $manager['id'],$manager['crm_id']));

            $update_status = $connection->prepare("update leads set status = 'crm', manager = " . $manager_id . ", crm_id = " . $manager['crm_id'] . "  where id = " . $this->id. ";");
            $update_status->execute();

            if ($manager['quota'] == 1) {
                $update_end_lead = $connection->prepare("UPDATE users SET end_lead = end_lead + 1, get_last = UNIX_TIMESTAMP(NOW(6)) WHERE id = " . $manager_id . ";");
                $update_end_lead->execute();

                $update_end_lead = $connection->prepare("UPDATE users SET end_lead = 0, add_lead = 0 WHERE end_lead = add_lead");
                $update_end_lead->execute();
            } else {
                $update_end_lead = $connection->prepare("UPDATE users SET get_last = UNIX_TIMESTAMP(NOW(6)) WHERE id = " . $manager_id . ";");
                $update_end_lead->execute();
            }

            $log = $connection->prepare ("INSERT INTO log_desp SET manager_id = " . $manager_id . ", lead_id = " . $this->id . ", date  = NOW(), is_quota = " . $manager['quota'] . ", lingua = " . $manager['lingua'] . ", get_last = '" . $manager['get_last'] . "', campaign_id = '" . $this->campaing_id . "',aff_id = '" . $this->aff_id . "';")->execute();


        // algorithm End




        // Start

        $campaing_id = $this->campaing_id;
        $aff_id = $this->aff_id;
        $list_id = 'b7e1dfda-8539-4b8e-9920-786113da2612';
        /*desk_id start*/
        $desk_crm_id = '';
        if(!empty($manager_id)){

            $manager_desk = $connection->prepare("select * from users where id = ".$manager_id.";");
            $manager_desk->execute();
            $manager_desk = $manager_desk->fetchAll(PDO::FETCH_ASSOC);
            $manager_desk = $manager_desk[0]['dask'];

            $desk_crm_id = $connection->prepare("select * from dasks where id = ".$manager_desk.";");
            $desk_crm_id->execute();
            $desk_crm_id = $desk_crm_id->fetchAll(PDO::FETCH_ASSOC);
            $desk_crm_id = $desk_crm_id[0]['crm_id'];
        }
        /*desk_id end*/

        $camp_crm_id = $connection->prepare("select * from campaigns where id = ".$campaing_id.";");
        $camp_crm_id->execute();
        $camp_crm_id = $camp_crm_id->fetchAll(PDO::FETCH_ASSOC);
        $camp_crm_id = $camp_crm_id[0]['camp_crm_id'];

        $aff_crm = $connection->prepare("select * from affiliate where id = ".$aff_id.";");
        $aff_crm->execute();
        $aff_crm = $aff_crm->fetchAll(PDO::FETCH_ASSOC);
        $aff_crm = $aff_crm[0]['aff_crm'];

        $params=[
            "fname" => $this->fname,
            "lname"=> $this->lname,
            "email"=> $this->email,
            "country"=> $this->country,
            "language"=> $this->lang,
            "currency"=> $this->currency,
            "phone"=> $this->phone,
            "password"=> $this->pass,
            "campaign_id"=> $camp_crm_id,
            "affiliate_id"=> $aff_crm,
            "terms" => "1",
            "broker_employee_id"=> $manager['crm_id'],
            "c_cid"=>$this->c_cid,
            "a_aid"=>$this->a_aid,
            "b_bid"=>$this->b_bid,
            "desk_id" => $desk_crm_id
        ];



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://backend.globalallianceltd.com/api/peoples',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);
//        var_dump($json);

        $file = fopen('backend.txt', "a");
        $date = date("m.d.Y, G:i:s");
        $text  = 'START------------------' . $date . "\r\n";
        $text .= $response . "\r\n";
        $text .= "END----------------------------------------\r\n \r\n";
        fwrite($file, $text);
        fclose($file);

        $file = fopen('backend-params.txt', "a");
        $date = date("m.d.Y, G:i:s");
        $text  = 'START------------------' . $date . "\r\n";
        $text .= json_encode($params) . "\r\n";
        $text .= "END----------------------------------------\r\n \r\n";
        fwrite($file, $text);
        fclose($file);


        $status = $json->meta->errors->code;
        $crm_id = $json->data->id;

        $result = 0;
        if ($status == 200){
            $update_status = $connection->prepare("update leads set status = 'sent'  where  id = " . $this->id . ";");
            $update_status->execute();

            $update_crm_ID = $connection->prepare("update leads set crm_id = $crm_id where  id = " . $this->id . ";");
            $update_crm_ID->execute();

            $this->status = 'sent';

            $result = $status;

        }if ($status == 500){
            $update_status = $connection->prepare("update leads set status = 'duplicate' where  id = " . $this->id . ";");
            $update_status->execute();

            $update_crm_ID = $connection->prepare("update leads set crm_id = $crm_id where  id = " . $this->id . ";");
//		$update_crm_ID->execute();

            $result = $json->meta->errors->message;
        }



        // End

        $params_sendgrid = [
            "list_ids" => [
                $list_id
            ],
            "contacts" => [
                [
                    "alternate_emails" => [
                        $this->email,
                    ],
                    "country" => $this->country,
                    "email" => $this->email,
                    "first_name" => $this->fname,
                    "last_name" => $this->lname,
                    "phone_number" => $this->phone,

//                    "custom_fields" => ["e2_T" => $companyName, "e3_T" => $FullCountry, "e4_T" => $status],



                ]
            ]
        ];
        $params_sendgrid = json_encode($params_sendgrid);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $params_sendgrid,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer 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",
                "Content-Type: application/json"
            ),
        ));


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $file = fopen('sendgrid-params.txt', "a");
        $date = date("m.d.Y, G:i:s");
        $text  = 'START------------------' . $date . "\r\n";
        $text .= json_encode($params_sendgrid) . "\r\n";
        $text .= "END----------------------------------------\r\n \r\n";
        fwrite($file, $text);
        fclose($file);

        $file = fopen('sendgrid-resp.txt', "a");
        $date = date("m.d.Y, G:i:s");
        $text  = 'START------------------' . $date . "\r\n";
        $text .= $response . "\r\n";
        $text .= "END----------------------------------------\r\n \r\n";
        fwrite($file, $text);
        fclose($file);
        return $result;
    }else{
    	$this->status = 'new';
    	return 222;
    }
    }

}
?>