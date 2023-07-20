<?php

//require_once("../Utility/Hash.php");

class Camps {

	// подключение к базе данных и таблице 'users'
	private $conn;
	private $table_name = "campaigns";

	// свойства объекта
	public $id;
	public $name;
	public $url;
	public $affiliates;
	public $country;



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



    function get_one($id){

        $query = "SELECT *
                FROM campaigns
                WHERE id = $id";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    function get_one_by_dynamic_name($name){
        if(empty($name)) return false;
        $query = "SELECT *
                FROM campaigns
                WHERE dynamic_name = '$name'";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    public function get_campaign_by_name($name){
        $query = "SELECT *
                FROM campaigns
                WHERE name = '$name'";


        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    public function create($data, $add_affiliates = true){
        $App = new App($this->conn);
        $User = new User($this->conn);

        $fields = [
            "name" => $data->name,
            "url" => $data->url,
        ];
        if(isset($data->dynamic_name) && !empty($data->dynamic_name)){
            $fields['dynamic_name'] = $data->dynamic_name;
        }
        if(!$camp_id = $App->insert('campaigns', $fields)) return 'Campaign creation error';

        if($add_affiliates){
            $this->add_affiliates($camp_id, $data->affiliates);
        }

        /* Это для апи, где с таблицы парсятся лиды
        И создается кампейн, если такого нет
         */
        if(!empty($data->aff_id)){
            if(!$App->insert('cmaps_affs', [
                "aff_id" => $data->aff_id,
                "campaign_id" => $camp_id,
            ])){
                return 'Aff Bind error';
            }
        }






        $App->delete('camps_country', ['camps_id', '=', $camp_id]);
        foreach ($data->countries as $country){
            if(!$App->insert('camps_country', [
                "camps_id" => $camp_id,
                "country_id" => $country,
            ])){
                return 'Country creation error';
            }
        }

        $App->delete('camps_users', ['campaign_id', '=', $camp_id]);
        $App->delete('brand_camps', ['camp_id', '=', $camp_id]);
        foreach ($data->brands as $brand_id){
            $users = $User->get_brand_users($brand_id);
            foreach ($users as $user_id){
                if(!$App->insert('camps_users', [
                    "campaign_id" => $camp_id,
                    "user_id" => $user_id,
                ])){
                    return 'User creation error';
                }
            }

            if(!$App->insert('brand_camps', [
                "brand_id" => $brand_id,
                "camp_id" => $camp_id,
            ])){
                return 'Country-Brand creation error';
            }
        }


        return $camp_id;

    }

    public function add_affiliates($campaign_id, $data){
        $App = new App($this->conn);
        $Affiliate = new Affs($this->conn);

        $App->delete('cmaps_affs', ['campaign_id', '=', $campaign_id]);
        foreach ($data as $affiliate){
            if(!$aff = $App->get_one('affiliate', ['token', '=', $affiliate->token])){
                $aff_id = $Affiliate->create($affiliate, false);
            }else{
                $aff_id = $aff->id;
            }
            if(!$App->insert('cmaps_affs', [
                "aff_id" => $aff_id,
                "campaign_id" => $campaign_id,
            ])){
                return 'Campaign creation error';
            }
        }
        return true;
    }

    public function check_brand($aff_id, $camp_id, $brand_id){

        $database = new Database();
        $connection = $database->getConnection();

        $brands = $connection->prepare("SELECT * FROM brand_camps WHERE camp_id = $camp_id AND brand_id = $brand_id");
        $brands->execute();
        $check_campaigns = $brands->fetch(PDO::FETCH_ASSOC);


        $brands = $connection->prepare("SELECT * FROM brand_affs
                                                WHERE aff_id = $aff_id
                                                  AND brand_id = $brand_id
                                                  AND current_quota < daily_quota");
        $brands->execute();
        $check_quota = $brands->fetch(PDO::FETCH_ASSOC);

//        logs('check_brand', [$check_campaigns, $check_quota]);

        if(!$check_campaigns || !$check_quota){
            return false;
        }
        return true;
    }

    public function get_brands($camp_id){

        $query = "SELECT *
                FROM brand_camps
                WHERE camp_id = $camp_id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


}
