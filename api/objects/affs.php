<?php

//require_once("../Utility/Hash.php");

class Affs {

	// подключение к базе данных и таблице 'users'
	private $conn;
	private $table_name = "affiliate";

	// свойства объекта
	public $id;
	public $aff_id;
	public $name;
	public $campaign;
	public $email;

    public $dynamic_affiliates = [
        0, 1,
    ];



	// конструктор для соединения с базой данных
	public function __construct($db){
		$this->conn = $db;
	}

	// метод read() - получение user
	public function read(){

		// выбираем все записи

		$query = "SELECT *
                FROM
                    " . $this->table_name . "
                ORDER BY
                    id DESC";

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		// выполняем запрос
		$stmt->execute();

		return $stmt;
//		var_dump($query);
	}

    public function create($data, $add_campaigns = true){
        $result = [];
        $App = new App($this->conn);
        $Campaign = new Camps($this->conn);
        $fields = [
            "name" => $data->name,
            "email" => $data->email,
            "postback" => $data->postback,
            "token" => $data->token,
            "payout_CPA" => $data->cpa,
            "payout_CPL" => $data->cpl,
            "ip" => $data->ip,
        ];
        if(isset($data->dynamic_name) && !empty($data->dynamic_name)){
            $fields['dynamic_name'] = $data->dynamic_name;
        }
        if(!$aff_id = $App->insert('affiliate', $fields)) return 'Affiliate creation error';


        if($add_campaigns){
            $result = $this->add_campaigns($aff_id, $data->campaigns);
            if(!is_array($result)){
                return $result;
            }
        }else{
            $result = $aff_id;
        }



        $App->delete('country_affs', ['aff_id', '=', $aff_id]);
        logs('affiliate-create', 'delete country');
        foreach ($data->countries as $country){
            if(!$App->insert('country_affs', [
                "aff_id" => $aff_id,
                "country_id" => $country,
            ])){
                return 'Country creation error';
            }
        }


        $Brands = new Brands($this->conn);
        $all_brands = $Brands->get_all();
        $App->delete('brand_affs', ['aff_id', '=', $aff_id]);
        foreach ($all_brands as $brand){
            if(!$App->insert('brand_affs', [
                "brand_id" => $brand['id'],
                "aff_id" => $aff_id,
            ])){
                return 'Country-Brand creation error';
            }
        }



        return $result;

    }

	// используется при заполнении формы обновления
	function readOne() {

		// запрос для чтения одной записи
		if ($this->dask == '') {
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
                dask = '" . $this->dask . "' 
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
		$this->dask = $row['dask'];
		$this->full_name = $row['full_name'];
		$this->lang = $row['lang'];
		$this->countries = $row['countries'];
		$this->foto = $row['foto'];
		$this->online = $row['online'];
		$this->date = $row['date'];
		$this->status = $row['status'];
	}

	// метод update() - обновление
	function update(){

		// запрос для обновления записи
		$query = "UPDATE
                " . $this->table_name . "
            SET
                dask = :dask,
                full_name = :full_name,
                lang = :lang,
                countries = :countries,
                foto = :foto,
                online = :online,
                status = :status
            WHERE
                id = :id";

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

//		$password = $this->password;
//		$password = Utility\Hash::generate($password);
		// очистка
		$this->dask=htmlspecialchars(strip_tags($this->dask));
		$this->full_name=htmlspecialchars(strip_tags($this->full_name));
		$this->lang=htmlspecialchars(strip_tags($this->lang));
		$this->countries=htmlspecialchars(strip_tags($this->countries));
		$this->foto=htmlspecialchars(strip_tags($this->foto));
		$this->online=htmlspecialchars(strip_tags($this->online));
		$this->status=htmlspecialchars(strip_tags($this->status));
//		$password=htmlspecialchars(strip_tags($password));

//		$this->id=htmlspecialchars(strip_tags($this->id));
//		$this->password=htmlspecialchars(strip_tags($this->password));

		// привязываем значения
		$stmt->bindParam(':dask', $this->dask);
		$stmt->bindParam(':full_name', $this->full_name);
		$stmt->bindParam(':lang', $this->lang);
		$stmt->bindParam(':countries', $this->countries);
		$stmt->bindParam(':foto', $this->foto);
		$stmt->bindParam(':online', $this->online);
		$stmt->bindParam(':status', $this->status);
//		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':id', $this->id);

		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

//	 метод delete - удаление
	function delete(){

		// запрос для удаления записи
		$query = "DELETE FROM " . $this->table_name . " WHERE id =".$this->id;

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		// очистка
		$this->id=htmlspecialchars(strip_tags($this->id));

		// привязываем id записи для удаления
		$stmt->bindParam(1, $this->id);

		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}

		return false;
	}


    function get_one($aff_id){
        $query = "SELECT *
                FROM affiliate
                WHERE id = $aff_id";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    function get_one_by_dynamic_name($name){
        if(empty($name)) return false;
        $query = "SELECT *
                FROM affiliate
                WHERE dynamic_name = '$name'";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


	function get_aff_camps($aff_id){
		$query = "SELECT *
                FROM cmaps_affs
                WHERE aff_id = $aff_id";


		$stmt = $this->conn->prepare($query);

		$stmt->execute();

		$campaigns = [];

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

			array_push($campaigns, $row['campaign_id']);

		}
		return $campaigns;
	}

//	 метод search - поиск
//	function search($keywords){
//
//		// выборка по всем записям
//		$query = "SELECT
//                id, email,  forename, surname, password
//            FROM
//                " . $this->table_name . "
//
//            WHERE
//                email = ".$keywords."
//            LIMIT
//                0,1";
//
//		// подготовка запроса
//		$stmt = $this->conn->prepare($query);
//
//		// очистка
//		$keywords=htmlspecialchars(strip_tags($keywords));
//		$keywords = "%{$keywords}%";
//
//		// привязка
//		$stmt->bindParam(1, $keywords);
////		$stmt->bindParam(2, $keywords);
////		$stmt->bindParam(3, $keywords);
//
//		// выполняем запрос
//		$stmt->execute();
//		var_dump($stmt);
//		return $stmt;
//	}

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



    public function get_partners(){

        $query = "SELECT *
                FROM affiliate
                WHERE type = 'partner'";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($result, $row['id']);
        }
        return $result;

    }


    public function send_to_spreadsheet($spreadsheets_id, $lead){

	    if(!isset($lead->campaing_name)){
            $lead->campaing_name = $lead->campaing_id;
        }
        $postfields = [
            'spreadsheet_id' => $spreadsheets_id,
            'crm_id' => $lead->crm_id,
            'first_name' => $lead->fname,
            'last_name' => $lead->lname,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'country' => $lead->country,
            'aff_id' => $lead->aff_id,
            'lang' => $lead->lang,
            'campaing_id' => $lead->campaing_id,
            'campaing_name' => $lead->campaing_name,
            'pass' => $lead->pass,
            'currency' => $lead->currency,
            'c_cid' => $lead->c_cid,
            'a_aid' => $lead->a_aid,
            'b_bid' => $lead->b_bid,
            'ip' => $lead->ip,
            'domain' => $lead->domain,
            'date' => date('Y-m-d H:i:s'),
        ];

        logs('spreadsheets-postfields', $postfields);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://45.90.108.168/partners.php',
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
        $err = curl_error($curl);

        curl_close($curl);

        logs('spreadsheets-response', $response);

        if(!empty($response)){
            $json = json_decode($response);
            if($json->message == 'Good'){
                $query = "UPDATE spritesheets 
                            SET current_quota = current_quota + 1, 
                            get_last = UNIX_TIMESTAMP(NOW(6)) 
                            WHERE spritesheet = '$spreadsheets_id';";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();

                return true;
            }

        }

        return false;


    }

    public function get_last_spreadsheet($aff_id, $camp_id){

        $query = "SELECT *
                    FROM spritesheets
                    WHERE aff_id = $aff_id
                      AND active = 1
                    AND current_quota < daily_quota
                    AND campaigns LIKE '%\"$camp_id\"%'
                    ORDER BY get_last ASC";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($row)){
            return $row[0]['spritesheet'];
        }

        return false;

    }
    public function add_brand($aff_id, $brand_id){

        // запрос для вставки (создания) записей
        $query = "INSERT INTO brand_affs
				SET
				aff_id=:aff_id,
				brand_id=:brand_id
				";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":aff_id", htmlspecialchars(strip_tags($aff_id)));
        $stmt->bindParam(":brand_id", htmlspecialchars(strip_tags($brand_id)));


        if ($stmt->execute()) {
            if($id = $this->conn->lastInsertId()){
                return $id;
            }
        }
        return false;
    }

    public function add_country($aff_id, $country_id){

        // запрос для вставки (создания) записей
        $query = "INSERT INTO country_affs
				SET
				aff_id=:aff_id,
				country_id=:country_id
				";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":aff_id", htmlspecialchars(strip_tags($aff_id)));
        $stmt->bindParam(":country_id", htmlspecialchars(strip_tags($country_id)));


        if ($stmt->execute()) {
            if($id = $this->conn->lastInsertId()){
                return $id;
            }
        }
        return false;
    }

    public function add_campaigns($aff_id, $data){

        $result = [];
        $App = new App($this->conn);
        $Campaign = new Camps($this->conn);
        $App->delete('cmaps_affs', ['aff_id', '=', $aff_id]);
        foreach ($data as $master_id => $campaign){
            $has_campaign = true;
            $campaign_id = $campaign->tds_id;
            if($campaign_id){
                if(!$tds_campaign = $App->get_one('campaigns', ['id', '=', $campaign_id])){
                    $has_campaign = false;
                }
            }else{
                $has_campaign = false;
            }

            if(!$has_campaign){
                $campaign_data = [
                    "name" => $campaign->info->name,
                    "url" => $campaign->info->url,
                    "countries" => $campaign->countries,
                ];
                $campaign_data = (object) $campaign_data;
                $campaign_id = $Campaign->create($campaign_data, false);
            }

            if(is_numeric($campaign_id)){
                if(!$App->insert('cmaps_affs', [
                    "aff_id" => $aff_id,
                    "campaign_id" => $campaign_id,
                ])){
                    return 'Campaign creation error';
                }
                $result[$master_id] = $campaign_id;
            }else{
                return $campaign_id;
            }

        }

        return $result;
    }

    public function get_brands($aff_id){

        $query = "SELECT *
                FROM brand_affs
                WHERE aff_id = $aff_id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function get_queue($aff_id){
        $query = "SELECT brands_queue
                FROM affiliate
                WHERE id = $aff_id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['brands_queue'] === 'none') return false;
        $json = json_decode($row['brands_queue']);
        if(!empty($json)){
            return $json;
        }else{
            $json = $this->set_queue($aff_id);
            return $json;
        }
    }

    public function set_queue($aff_id){

        $brands = $this->get_brands_quota($aff_id);
        $queue = [];
        foreach ($brands as $brand_id => $quota){
            for($i = 0; $i < $quota; $i++){
                array_push($queue, $brand_id);
            }
        }

        shuffle($queue);
        $queue_json = json_encode($queue);
        $query = "UPDATE affiliate
        SET brands_queue = '$queue_json'
                WHERE id = $aff_id";
        $this->conn->prepare($query)->execute();

        return $queue;

    }

    public function get_brands_quota($aff_id){
        $query = "SELECT *
                FROM brand_affs
                WHERE aff_id = $aff_id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $brands = [];
        if(sizeof($result) == 1){
            $brands[$result[0]['brand_id']] = 10;
        }else{
            foreach ($result as $affiliate){
//                if(empty($affiliate['quota'])) continue;
//                $quota = !empty($affiliate['quota']) ? $affiliate['quota'] : 3;
                $brands[$affiliate['brand_id']] = $affiliate['quota'];
            }
        }

        return $brands;
    }

    public function brand_id_algorithm($aff_id, $camp_id){
        $Campaings = new Camps($this->conn);
        $queue = $this->get_queue($aff_id);
        $brand = false;
        if($queue){
            foreach ($queue as $key => $brand_id) {
                if($Campaings->check_brand($aff_id, $camp_id, $brand_id)){
                    $brand = $brand_id;
                    unset($queue[$key]);

                    break;
                }
            }
            if(!$brand){
                $this->set_queue($aff_id);
            }
        }else{
//            $Leads = new Leads($this->conn);
//            $brand = $Leads->get_camp_brand($camp_id);
            $Brands = new Brands($this->conn);

            $brands = $this->get_brands_quota($aff_id);
            $n = 0;
            while (!$Campaings->check_brand($aff_id, $camp_id, $brand)){
                if($n >= sizeof($brands)){
                    $brand = false;
                    break;
                }
                $brand = $Brands->get_last_brand($aff_id);
                $n++;
            }
            if($brand) return $brand;
        }


        if($brand){
            $queue = array_values($queue);
            $queue_json = json_encode($queue);
            $query = "UPDATE affiliate
        SET brands_queue = '$queue_json'
                WHERE id = $aff_id";
            $this->conn->prepare($query)->execute();
            return $brand;
        }
        return false;
    }

    public function add_quota($aff_id, $brand_id){

        $query = "UPDATE brand_affs 
                    SET current_quota = current_quota + 1,
                        last_get_lead = UNIX_TIMESTAMP(NOW(6))
                    WHERE aff_id = '$aff_id'
                        AND brand_id = '$brand_id';";
        $this->conn->prepare($query)->execute();

    }

    public function generate_token(){
        $App = new App($this->conn);
        return md5($App->get_last_id('affiliate')+1 . '_' . time());
    }



}
