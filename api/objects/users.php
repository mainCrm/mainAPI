<?php

require_once("../Utility/Hash.php");

class User {

    // подключение к базе данных и таблице 'users' 
    private $conn;
    private $table_name = "users";

    // свойства объекта 
    public $id;
    public $crm_id;
    public $dask;
    public $full_name;
//    public $lang;
//    public $countries;
	public $online;
	public $date;
	public $status;
    public $foto;
    public $role;
    public $email;
    public $password;





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

    // метод create - создание user 
    public function create(){

		// запрос для вставки (создания) записей
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
				crm_id=:crm_id, 
				full_name=:full_name, 
				dask=:dask, 
				online=:online, 
				status=:status, 
				foto=:foto, 
				role=:role, 
				date=:date, 
				password=:password, 
				email=:email";

		// подготовка запроса
		$stmt = $this->conn->prepare($query);

		$password = $this->password;
		$password = Utility\Hash::generate($password);

		// очистка
		$this->crm_id=htmlspecialchars(strip_tags($this->crm_id));
		$this->full_name=htmlspecialchars(strip_tags($this->full_name));
		$this->dask=htmlspecialchars(strip_tags($this->dask));
		$this->online=htmlspecialchars(strip_tags($this->online));
		$this->status=htmlspecialchars(strip_tags($this->status));
		$this->foto=htmlspecialchars(strip_tags($this->foto));
		$this->role=htmlspecialchars(strip_tags($this->role));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->password=htmlspecialchars(strip_tags($this->password));
		$this->status=htmlspecialchars(strip_tags($this->status));
//		$password=htmlspecialchars(strip_tags($password));

		// привязка значений
		$stmt->bindParam(":crm_id", $this->crm_id);
		$stmt->bindParam(":full_name", $this->full_name);
		$stmt->bindParam(":dask", $this->dask);
		$stmt->bindParam(":online", $this->online);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":foto", $this->foto);
		$stmt->bindParam(":role", $this->role);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":date", $this->date);
		$stmt->bindParam(":password", $password);


		// выполняем запрос
		if ($stmt->execute()) {
			return true;
		}

		return false;
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


    /*
     * Получаем кол-во лидов менеджера за сегодня
     * */
    public function get_manager_leads($manager_id){

        $database = new Database();
        $connection = $database->getConnection();

        $date = date('Y-m-d 00:00:00');

        $query = "SELECT *
                FROM manager_leads_count
                WHERE manager_id = $manager_id
                AND date = '$date'";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch();
        if($row){
            return $row['count'];
        }else{
            return 0;
        }
    }

    public function add_lead_count($manager_id){

        $database = new Database();
        $connection = $database->getConnection();

        $count = $this->get_manager_leads($manager_id);

        $date = date('Y-m-d 00:00:00');
        if($count){
            $count = $count+1;

            $query = "UPDATE manager_leads_count SET count = $count WHERE manager_id = $manager_id AND date = '$date'";
        }else{
            $query = "INSERT INTO manager_leads_count SET manager_id = $manager_id, count = 1, date  = '$date';";
        }
        $connection->prepare($query)->execute();

    }

    public function get_manager_name($manager_id){

        $database = new Database();
        $connection = $database->getConnection();

        $query = "SELECT *
                FROM users
                WHERE id = $manager_id";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch();
        if($row){
            return $row['full_name'];
        }
        return false;
    }

    public function quota_filling(){

        $database = new Database();
        $connection = $database->getConnection();

        $query = "SELECT SUM(add_lead) as add_lead,
                    SUM(end_lead) as end_lead
                FROM users
                WHERE online = 'online'
                AND role = 'manager'";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch();
        if($row){
            if($row['end_lead'] >= $row['add_lead']){
                return true;
            }
        }
        return false;
    }

    public function get_default_manager($brand_id){
        $manager_id = false;
        switch ($brand_id){
            case 2:
                $manager_id = 1053;
                break;
            case 3:
                $manager_id = 27;
                break;
            case 6:
                $manager_id = 357;
                break;
            case 12:
                $manager_id = 864;
                break;
            case 15:
                $manager_id = 1754;
                break;
            case 16:
                $manager_id = 1861;
                break;
        }
        return $manager_id;
    }

    public function get_users($role = ''){

        $database = new Database();
        $connection = $database->getConnection();

        if(!empty($role)){
            $role_query = "WHERE role = '$role'";
        }else{
            $role_query = "";
        }
        $query = "SELECT *
                FROM users
                $role_query";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll();
        if($result){
            return $result;
        }
        return false;
    }

    public function get_brand_users($brand_id){

        $database = new Database();
        $connection = $database->getConnection();

        $query = "SELECT user_id
                FROM brand_users
                WHERE brand_id = $brand_id";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if($result){
            return $result;
        }
        return false;
    }



}
