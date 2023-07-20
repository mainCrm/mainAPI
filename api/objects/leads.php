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
    public $campaing_name;
    public $currency;
    public $pass;
    public $terms;
    public $a_aid;
    public $b_bid;
    public $c_cid;
    public $ip;
    public $domain;
    public $brand;
    public $dynamic_id = false;

    public $duplicate = false;
    public $spreadsheet_id = false;


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
    public $crm_id;
    public $master_id = false;
    public $eagles_master_id = false;


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

        $master_id_query = $this->master_id ? ", master_id=:master_id" : "";
        $eagles_master_id_query = $this->eagles_master_id ? ", eagles_master_id=:eagles_master_id" : "";


        if($this->dynamic_id === false){
            $dynamic_query = "";
        }else{
            $dynamic_query = ", dynamic_id=:dynamic_id";
        }

        // запрос для вставки (создания) записей
        $query = "INSERT INTO
					" . $this->table_name . "
				SET
				id=:id, fname=:fname, lname=:lname, email=:email, phone=:phone, country=:country, aff_id=:aff_id, lang=:lang, campaing_id=:campaing_id, date=:date, pass=:pass, currency=:currency, c_cid=:c_cid, a_aid=:a_aid, b_bid=:b_bid, ip=:ip, domain=:domain, verified=2  $master_id_query $eagles_master_id_query $dynamic_query";
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

        $crm_id = $this->new_crm_id();
//        $this->crm_id = $crm_id;

        // привязка значений
        $stmt->bindParam(":id", $crm_id);
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

        if(!empty($master_id_query)){
            $this->master_id=htmlspecialchars(strip_tags($this->master_id));
            $stmt->bindParam(":master_id", $this->master_id);
        }
        if(!empty($eagles_master_id_query)){
            $this->eagles_master_id=htmlspecialchars(strip_tags($this->eagles_master_id));
            $stmt->bindParam(":eagles_master_id", $this->eagles_master_id);
        }

        if(!empty($dynamic_query)){
            $stmt->bindParam(":dynamic_id", $this->dynamic_id);
        }


        $duplicate = false;
        if($find_leads = $this->find_lead($this->email)){
            $duplicate = true;
            $this->email = $this->email . '_1';
        }

        if(!$duplicate){
            if($find_leads = $this->find_lead_by_phone($this->phone)){
                $duplicate = true;
                $this->phone = $this->phone . '_1';
            }
        }

        if($duplicate){
            logs('duplicate', json_encode($find_leads));
            $this->duplicate = true;
            if(!$this->spreadsheet_id){
                return "User already exist";
            }
        }






        // выполняем запрос
        if ($stmt->execute()) {
//		    $Leads = new Leads();
            $lead_id = $this->conn->lastInsertId();
            $this->crm_id = $lead_id;
            $this->id = $lead_id;
            $algorithm = $this->algorithm_new();
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
        $this->fullphone = $row['phone'];
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
        $this->crm_status = $row['crm_status'];
        $this->manager = $row['manager'];
        $this->date = $row['date'];
        $this->ftd = $row['FTD'];
        $this->ftd_date = $row['FTD_date'];
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

    function get_lead_by_email($email){
        if(empty($email)) return false;
        $query = "SELECT *
                FROM leads
                WHERE email = '$email'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row;
        }

        $query = "SELECT *
                FROM leads_hidden
                WHERE email = '$email'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row;
        }

        return false;

    }


    function get_lead_by_phone($phone){
        if(empty($phone)) return false;
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $query = "SELECT *
                FROM leads
                WHERE phone LIKE '%$phone'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row;
        }

        $query = "SELECT *
                FROM leads_hidden
                WHERE phone LIKE '%$phone'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row;
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
        $only_ftd_query =  $ftd_from_query = $ftd_to_query = $reg_from_query = $reg_to_query = $amount_diff_query = $status = "";

        if($only_ftd === true){
            $only_ftd_query = "AND FTD = 'true'";

            if($AFF_ID == 70){
                $ftd_to_query = "AND FTD_date < '2021-12-01'";
            }

        }elseif ($only_ftd === false){
            $only_ftd_query = "AND (FTD IS null OR FTD = '')";
        }

        if(!empty($ftd_from)){
            // $ftd_from = strtotime($ftd_from);
            $ftd_from_query = "AND FTD_date > '" . $ftd_from . "'";

            if($AFF_ID == 70){
                $ftd_to_query = "AND FTD_date < '2021-12-01'";
            }

        }
        if(!empty($ftd_to) && (strtotime($ftd_to) != strtotime(date("Y-m-d G:i:s")))){
//            $ftd_to = strtotime($ftd_to);
            $ftd_to_query = "AND FTD_date < '" . $ftd_to . "'";
            if($AFF_ID == 70){
                if(strtotime($ftd_to) >= strtotime(date("2021-12-01 00:00:00"))){
                    $ftd_to_query = "AND FTD_date < '2021-12-01'";
                }
            }
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


        if( ($only_ftd === true) || !empty($ftd_from) ){
            $amount_diff_query = "AND ftd_amount >= 100 AND DATEDIFF(FTD_date, date) < 30";
        }


        $status = "AND (status='sent')";

        //AND amount >= 230 AND DATEDIFF(FTD_date, date) < 60

        $query = "SELECT * FROM " . $this->table_name . " WHERE (aff_id = '".$AFF_ID."' OR dynamic_id = '".$AFF_ID."') ".$only_ftd_query." ".$ftd_from_query." ".$ftd_to_query." ".$reg_from_query." ".$reg_to_query."  " . $amount_diff_query . "  " . $status . "  " . $paginate

        ;
        // var_dump($query);

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



//алгоритм без десков
    public function algorithm_new(){

        $database = new Database();
        $connection = $database->getConnection();


        $Affiliate = new Affs($connection);
        $Users = new User($connection);

        if(!$brand = $Affiliate->brand_id_algorithm($this->aff_id,$this->campaing_id)){
            $brand = $Affiliate->brand_id_algorithm($this->aff_id,$this->campaing_id);
        }

        if(!$brand){
            $brand = 2;
        }

        logs('brand', $brand);

        $managers = [];
        if(!$this->duplicate) {

//            (users.add_lead - users.end_lead)
            $algorithm_query = "SELECT DISTINCT users.*, ROUND((users.end_lead/users.add_lead)*100, 0) as weight
				   from users 
					  INNER JOIN camps_users ON users.id = camps_users.user_id
						INNER JOIN campaigns ON camps_users.campaign_id = campaigns.id
						INNER JOIN country_users ON users.id = country_users.user_id
						INNER JOIN countries ON country_users.country_id = countries.id
						INNER JOIN langs_users ON langs_users.user_id = users.id
						INNER JOIN langs ON langs.id = langs_users.lang_id
				        INNER JOIN brand_users on users.id = brand_users.user_id
				     where online = 'online' 
				     AND role IN ('manager', 'partner', 'desk_manager', 'director')
				     AND users.add_lead > 0
				     AND users.end_lead < users.add_lead
				       and brand_users.brand_id = $brand
				     and (campaigns.id = " . $this->campaing_id . ") 
				     and ((countries.name like '%" . $this->country . "%' and langs.name like '%" . $this->lang . "%') or (countries.name like '%" . $this->country . "%'))	   
				   ORDER BY weight ASC;
				   ";


            $get_manager = $connection->prepare($algorithm_query);
            $get_manager->execute();
            $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);

//            foreach ($all_managers as $manager){
//                for($i = 0; $i < $manager['weight']; $i++){
//                    array_push($managers, $manager['id']);
//                }
//            }

            logs('algorithm_query', $algorithm_query);
            logs('algorithm_manager', $managers);

        }
//        shuffle($managers);
//        return false;


        if (empty($managers)) {
            $find_manager = false;

            $default_manager = $Users->get_default_manager($brand);
            $get_manager = $connection->prepare("
        SELECT *
        from users 
        where id = $default_manager");
            $get_manager->execute();
            $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);

//            $desk_id = $managers[0]['dask'];
        }else{
            $find_manager = true;

//            $get_manager = $connection->prepare("
//        SELECT *
//        from users
//        where id = $managers[0]");
//            $get_manager->execute();
//            $manager = $get_manager->fetchAll(PDO::FETCH_ASSOC);

//            $desk_id = $managers[0]['dask'];

        }






        $manager = $managers[0];
        $desk_id = $manager['dask'];
        $manager_id = $manager['id'];

        logs('manager_id', $manager_id);

        if(!empty($manager_id)){





            $is_partner = false;
            /* parnters Start */



//            if($Users->quota_filling()){
//                $aff_step = $current_step = 0;
//            }
//            $parnters = $Affiliate->get_partners();
//            $parnters = [];

            $affiliate = $Affiliate->get_one($this->aff_id);

            if($affiliate['type'] == 'partner'){

                $aff_step = $affiliate['leads_step'];
                $current_step = !empty($affiliate['leads_current_step']) ? $affiliate['leads_current_step'] : 0;
                $current_step += 1;

                if($current_step >= $aff_step){
                    if($spreadsheet_id = $Affiliate->get_last_spreadsheet($this->aff_id, $this->campaing_id)){
                        $Affiliate->send_to_spreadsheet($spreadsheet_id, $this);
                        $is_partner = true;
                    }
                    $current_step = 0;
                }else{

                }

                $update_aff_query = "update affiliate set leads_current_step = $current_step where id = $this->aff_id;";
                $update_aff = $connection->prepare($update_aff_query);
                $update_aff->execute();

            }


            /* parnters End */

            if(!$is_partner){

                $Users = new User($this->conn);
                $Users->add_lead_count($manager_id);

                $this->set_first_manager($this->crm_id, $manager_id);

                $desk_query = "";

                if($desk_id){
                    $desk_query = ", dask_id = " . $desk_id;
                }

                if(!empty($desk_id) && $find_manager){
                    $update_desk_end_lead = $connection->prepare("UPDATE dasks SET get_last = UNIX_TIMESTAMP(NOW(6)), weight = weight-1 WHERE id = " . $desk_id . ";");
                    $update_desk_end_lead->execute();


                }

                $crm_status_query = "";
                if($this->duplicate){
                    $crm_status_query = ", crm_status = 'Dubl'";
                }

                $first_manager_query = $find_manager ? ", first_manager = $manager_id" : "";

                $update_lead_query = "update leads set status = 'sent', manager = " . $manager_id . ", brand = ".$brand." $desk_query $crm_status_query $first_manager_query where crm_id = " . $this->id. ";";
                logs('update_query', $update_lead_query);
                $update_status = $connection->prepare($update_lead_query);
                $update_status->execute();

                $update_end_lead = $connection->prepare("UPDATE users SET end_lead = end_lead + 1, get_last = UNIX_TIMESTAMP(NOW(6)) WHERE id = " . $manager_id . ";");
                $update_end_lead->execute();


                $Affiliate->add_quota($this->aff_id, $brand);

            }else{

                $update_lead_query = "update leads set status = 'sent', manager = 0, brand = 4 where crm_id = " . $this->id. ";";
//                logs('update_query', $manager_id);
                $update_status = $connection->prepare($update_lead_query);
                $update_status->execute();

            }


//            $log = $connection->prepare ("INSERT INTO log_desp SET manager_id = " . $manager_id . ", lead_id = " . $this->id . ", date  = NOW(), is_quota = " . $manager['quota'] . ", lingua = " . $manager['lingua'] . ", get_last = '" . $manager['get_last'] . "', campaign_id = '" . $this->campaing_id . "',aff_id = '" . $this->aff_id . "';")->execute();


            $this->status = 'sent';

            $result = 200;
            // algorithm End


            return $result;
        }else{
            $this->status = 'new';
            return 222;
        }
    }

    public function algorithm(){

        $database = new Database();
        $connection = $database->getConnection();


        // algorithm Start

        $Leads = new Leads($connection);
        $Affiliate = new Affs($connection);
        $Users = new User($connection);

        if(!$brand = $Affiliate->brand_id_algorithm($this->aff_id,$this->campaing_id)){
            $brand = $Affiliate->brand_id_algorithm($this->aff_id,$this->campaing_id);
        }

        if(!$brand){
            $brand = 2;
        }

        logs('brand', $brand);

        $desk_id = false;
        $managers = [];
        if(!$this->duplicate) {
            if (!$active_desks = $this->get_active_desks($brand)) {
                $this->set_desks_weight($brand);
                $active_desks = $this->get_active_desks($brand);
            }


            logs('active_desks', $active_desks);


            if (!empty($active_desks)) {


                if (count($active_desks) == 2) {
                    usort($active_desks, function ($a, $b) {
                        return $a['get_last'] - $b['get_last'];
                    });
                }


//        	foreach($active_desks as $active_desk){
                $desk_id = $active_desks[0]['id'];

                $algorithm_query = "SELECT users.*,
			       IF(add_lead > 0 AND end_lead != add_lead, 1, 0) as quota,
			       IF(countries.name like '%" . $this->country . "%' and langs.name like '%" . $this->lang . "%', 1, 0) as lingua
				   from users 
					  INNER JOIN camps_users ON users.id = camps_users.user_id
						INNER JOIN campaigns ON camps_users.campaign_id = campaigns.id
						INNER JOIN country_users ON users.id = country_users.user_id
						INNER JOIN countries ON country_users.country_id = countries.id
						INNER JOIN langs_users ON langs_users.user_id = users.id
						INNER JOIN langs ON langs.id = langs_users.lang_id
				     where online = 'online' 
				     AND role = 'manager'
				     AND dask = $desk_id
				     and (campaigns.id = " . $this->campaing_id . ") 
				     and ((countries.name like '%" . $this->country . "%' and langs.name like '%" . $this->lang . "%') or (countries.name like '%" . $this->country . "%'))	   
				   ORDER BY quota DESC, lingua DESC, get_last ASC;
				   ";


                foreach ($active_desks as $active_desk) {
                    $desk_id = $active_desk['id'];

                    $algorithm_query = "SELECT users.*,
			       IF(countries.name like '%" . $this->country . "%' and langs.name like '%" . $this->lang . "%', 1, 0) as lingua
				   from users 
					  INNER JOIN camps_users ON users.id = camps_users.user_id
						INNER JOIN campaigns ON camps_users.campaign_id = campaigns.id
						INNER JOIN country_users ON users.id = country_users.user_id
						INNER JOIN countries ON country_users.country_id = countries.id
						INNER JOIN langs_users ON langs_users.user_id = users.id
						INNER JOIN langs ON langs.id = langs_users.lang_id
				     where online = 'online' 
				     AND role IN ('manager', 'partner', 'desk_manager', 'director')
				     AND dask = $desk_id
				     AND add_lead > 0
				     AND end_lead < add_lead
				     and (campaigns.id = " . $this->campaing_id . ") 
				     and ((countries.name like '%" . $this->country . "%' and langs.name like '%" . $this->lang . "%') or (countries.name like '%" . $this->country . "%'))	   
				   ORDER BY lingua DESC, get_last ASC;
				   ";

                    $get_manager = $connection->prepare($algorithm_query);
                    $get_manager->execute();
                    $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);


                    logs('algorithm_query', $algorithm_query);
                    logs('algorithm_manager', $managers);

                    if (!empty($managers)) break;
                }
            }
        }


        if (empty($managers)) {
            $find_manager = false;

            $default_manager = $Users->get_default_manager($brand);
            $get_manager = $connection->prepare("
        SELECT *
        from users 
        where id = $default_manager");
            $get_manager->execute();
            $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);

            $desk_id = $managers[0]['dask'];
        }else{
            $find_manager = true;
        }






        $manager = $managers[0];

        $manager_id = $manager['id'];

        logs('manager_id', $manager_id);

        if(!empty($manager_id)){





            $is_partner = false;
            /* parnters Start */



//            if($Users->quota_filling()){
//                $aff_step = $current_step = 0;
//            }
//            $parnters = $Affiliate->get_partners();
//            $parnters = [];

            $affiliate = $Affiliate->get_one($this->aff_id);

            if($affiliate['type'] == 'partner'){

                $aff_step = $affiliate['leads_step'];
                $current_step = !empty($affiliate['leads_current_step']) ? $affiliate['leads_current_step'] : 0;
                $current_step += 1;

                if($current_step >= $aff_step){
                    if($spreadsheet_id = $Affiliate->get_last_spreadsheet($this->aff_id, $this->campaing_id)){
                        $Affiliate->send_to_spreadsheet($spreadsheet_id, $this);
                        $is_partner = true;
                    }
                    $current_step = 0;
                }else{

                }

                $update_aff_query = "update affiliate set leads_current_step = $current_step where id = $this->aff_id;";
                $update_aff = $connection->prepare($update_aff_query);
                $update_aff->execute();

            }


            /* parnters End */

            if(!$is_partner){

                $Users = new User($this->conn);
                $Users->add_lead_count($manager_id);

                $this->set_first_manager($this->crm_id, $manager_id);

                $desk_query = "";

                if($desk_id){
                    $desk_query = ", dask_id = " . $desk_id;
                }

                if(!empty($desk_id) && $find_manager){
                    $update_desk_end_lead = $connection->prepare("UPDATE dasks SET get_last = UNIX_TIMESTAMP(NOW(6)), weight = weight-1 WHERE id = " . $desk_id . ";");
                    $update_desk_end_lead->execute();


                }

                $crm_status_query = "";
                if($this->duplicate){
                    $crm_status_query = ", crm_status = 'Dubl'";
                }

                $update_lead_query = "update leads set status = 'sent', manager = " . $manager_id . ", brand = ".$brand." $desk_query $crm_status_query  where crm_id = " . $this->id. ";";
                logs('update_query', $update_lead_query);
                $update_status = $connection->prepare($update_lead_query);
                $update_status->execute();

                $update_end_lead = $connection->prepare("UPDATE users SET end_lead = end_lead + 1, get_last = UNIX_TIMESTAMP(NOW(6)) WHERE id = " . $manager_id . ";");
                $update_end_lead->execute();


                $Affiliate->add_quota($this->aff_id, $brand);

            }else{

                $update_lead_query = "update leads set status = 'sent', manager = 0, brand = 4 where crm_id = " . $this->id. ";";
//                logs('update_query', $manager_id);
                $update_status = $connection->prepare($update_lead_query);
                $update_status->execute();

            }


//            $log = $connection->prepare ("INSERT INTO log_desp SET manager_id = " . $manager_id . ", lead_id = " . $this->id . ", date  = NOW(), is_quota = " . $manager['quota'] . ", lingua = " . $manager['lingua'] . ", get_last = '" . $manager['get_last'] . "', campaign_id = '" . $this->campaing_id . "',aff_id = '" . $this->aff_id . "';")->execute();


            $this->status = 'sent';

            $result = 200;
            // algorithm End


            return $result;
        }else{
            $this->status = 'new';
            return 222;
        }
    }



    public function get_active_desks($brand_id){
        $database = new Database();
        $connection = $database->getConnection();

//    	$query = "SELECT DISTINCT dasks.* from dasks
//		    INNER join users on dasks.id = users.dask
//		WHERE users.online = 'online'
//		AND dasks.type = 'sales'
//		ORDER BY dasks.get_last ASC;";

        $query = "SELECT DISTINCT dasks.*
        from dasks
            INNER join users on dasks.id = users.dask
        WHERE users.online = 'online'
          AND users.role IN ('manager', 'desk_manager', 'director')
          AND dasks.type = 'sales'
        AND dasks.weight > 0
          AND dasks.brand_id = $brand_id
AND users.add_lead > 0
  AND users.end_lead < users.add_lead
        ORDER BY dasks.weight DESC, dasks.get_last ASC ;";


        $desks = $connection->prepare($query);
        $desks->execute();
        $desks = $desks->fetchAll(PDO::FETCH_ASSOC);

        return $desks;
    }


    public function set_desks_weight($brand_id){
        $database = new Database();
        $connection = $database->getConnection();

        $connection->prepare("UPDATE dasks SET weight = 0 WHERE brand_id = $brand_id")->execute();

        $query = "SELECT DISTINCT dasks.*, count(users.id) as desk_weight from dasks
            INNER join users on dasks.id = users.dask
        WHERE users.online = 'online'
          AND users.role IN ('manager', 'desk_manager', 'director')
          AND dasks.type = 'sales'
          AND dasks.brand_id = $brand_id
          AND users.add_lead > users.end_lead
        GROUP BY dasks.id";

        $desks = $connection->prepare($query);
        $desks->execute();
        $desks = $desks->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($desks)){
            foreach ($desks as $desk){
                $desk_id = $desk['id'];
                $weight = $desk['desk_weight'];
                $connection->prepare("UPDATE dasks SET weight = $weight WHERE id = $desk_id;")->execute();
            }
        }

    }





    public function get_camp_brand($camp_id){

        $database = new Database();
        $connection = $database->getConnection();

        $brands = $connection->prepare("SELECT * FROM brand_camps WHERE camp_id = '$camp_id';"); //LIMIT 1
        $brands->execute();
        $brands = $brands->fetchAll(PDO::FETCH_ASSOC);


        $brand = $brands[0];
        $brand_id = $brand['brand_id'];

        if(empty($brand_id)) return false;
        return $brand_id;
    }


    public function get_brand_users($brand_id){

        $database = new Database();
        $connection = $database->getConnection();

        $users = $connection->prepare("SELECT * FROM brand_users WHERE brand_id = '$brand_id';"); //LIMIT 1
        $users->execute();
        $users = $users->fetchAll(PDO::FETCH_ASSOC);

        $managers = [];

        foreach ($users as $key => $value) {
            if(!empty($value['user_id'])) array_push($managers, $value['user_id']);
        }

        if(empty($managers)) return false;
        return $managers;


    }


    // public function get_brand_camps($brand_id){

    //     $database = new Database();
    //     $connection = $database->getConnection();

    //     $brands = $connection->prepare("SELECT * FROM brand_camps WHERE brand_id = '$brand_id';"); //LIMIT 1
    //     $brands->execute();
    //     $brands = $brands->fetchAll(PDO::FETCH_ASSOC);

    //     $campaigns = [];

    //     foreach ($brands as $key => $value) {
    //     	if(!empty($value['camp_id'])) array_push($campaigns, $value['camp_id']);
    //     }

    //     if(empty($campaigns)) return false;
    //     return $campaigns;


    // }


    public function find_lead($email){

        $database = new Database();
        $connection = $database->getConnection();

        $leads = $connection->prepare("SELECT * FROM leads WHERE email = '$email';"); //LIMIT 1
        $leads->execute();
        $leads = $leads->fetchAll(PDO::FETCH_ASSOC);

        if(empty($leads)) return false;
        return $leads;
    }

    public function find_lead_by_phone($phone){

        $database = new Database();
        $connection = $database->getConnection();

        $leads = $connection->prepare("SELECT * FROM leads WHERE phone = '$phone';"); //LIMIT 1
        $leads->execute();
        $leads = $leads->fetchAll(PDO::FETCH_ASSOC);

        if(empty($leads)) return false;
        return $leads;
    }

    public function new_crm_id(){
        $database = new Database();
        $connection = $database->getConnection();

        $crm_id = $connection->prepare("SELECT * FROM vtiger_crmentity_seq"); //
        $crm_id->execute();
        $crm_id = $crm_id->fetchAll(PDO::FETCH_ASSOC);

        if(empty($crm_id)) return 0;

        $update_crm_id = $connection->prepare("UPDATE vtiger_crmentity_seq SET id=LAST_INSERT_ID(id+1)");
        $update_crm_id->execute();

        return $crm_id[0]['id']+1;
    }




    public function test(){


        $this->campaing_id = 123;
        $this->country = 'ru';
        $this->lang = 'ru';

        $database = new Database();
        $connection = $database->getConnection();

        // algorithm Start

        $Leads = new Leads($connection);
        $brand = $Leads->get_camp_brand($this->campaing_id);
        $users = $Leads->get_brand_users($brand);




        $ids = join("','",$users);


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


        var_dump($get_manager);

        $get_manager->execute();
        $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);

        var_dump($managers);


    }

    public function test1(){


        $this->campaing_id = 11;
        $this->country = 'ru';
        $this->lang = 'ru';

        $database = new Database();
        $connection = $database->getConnection();

        // algorithm Start

        $Leads = new Leads($connection);
        $brand = $Leads->get_camp_brand($this->campaing_id);
        $users = $Leads->get_brand_users($brand);




        $ids = join("','",$users);


        $get_manager = $connection->prepare("SELECT * FROM users  where id IN ('$ids') "); //LIMIT 1


        var_dump($get_manager);

        $get_manager->execute();
        $managers = $get_manager->fetchAll(PDO::FETCH_ASSOC);

        var_dump($managers);


    }


    public function set_first_manager($lead_id, $manager_id){
        $database = new Database();
        $connection = $database->getConnection();

        $Users = new User($this->conn);

        if(!$manager_name = $Users->get_manager_name($manager_id)) return false;
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO history_lead
                    SET
                    lead_id = $lead_id,
                    date = '$date',
                    manager_id = $manager_id,
                    manager_name = '$manager_name',
                    field = 'first-manager',
                    `to` = '$manager_id'";

        logs('set_first_manager', $query);

        $connection->prepare($query)->execute();

    }



    public function send_to_spreadsheet($spreadsheets_id){

        $postfields = [
            'spreadsheet_id' => $spreadsheets_id,
            'crm_id' => $this->crm_id,
            'first_name' => $this->fname,
            'last_name' => $this->lname,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'aff_id' => $this->aff_id,
            'lang' => $this->lang,
            'campaing_id' => $this->campaing_id,
            'pass' => $this->pass,
            'currency' => $this->currency,
            'c_cid' => $this->c_cid,
            'a_aid' => $this->a_aid,
            'b_bid' => $this->b_bid,
            'ip' => $this->ip,
            'domain' => $this->domain,
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

        $json = json_decode($response);


    }

}
?>