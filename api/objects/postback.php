<?php


class Postback {
	private $conn;
	private $table_name = "postback";

	// свойства объекта
	public $per_page = 25;

    public $tr_id;
    public $client_id;
    public $cl_email;
    public $phone;
    public $full_name;
    public $amount;
    public $tr_date;
    public $aff_id;
    public $camp_id;

	public function __construct($db){
		$this->conn = $db;
	}

	public function create(){

	    $Leads = new Leads($this->conn);


		$this->client_id=htmlspecialchars(strip_tags($this->client_id));
		$this->tr_id=htmlspecialchars(strip_tags($this->tr_id));
		$this->cl_email=htmlspecialchars(strip_tags($this->cl_email));
		$this->amount=htmlspecialchars(strip_tags($this->amount));
		$this->tr_date=htmlspecialchars(strip_tags($this->tr_date));
		$this->phone=htmlspecialchars(strip_tags($this->phone));
		$this->full_name=htmlspecialchars(strip_tags($this->full_name));


		$lead = $Leads->get_lead_by_email($this->cl_email);
		if(!$lead){
            $lead = $Leads->get_lead_by_phone($this->phone);
        }
		if($lead){
            $this->aff_id = $lead['aff_id'];
            $this->camp_id = $lead['campaing_id'];
        }

        if(empty($this->aff_id)){
            $this->aff_id = 0;
        }
        if(empty($this->camp_id)){
            $this->camp_id = 0;
        }

        $postback = $this->get_postback_by_tr_id($this->tr_id);

        if($postback){

            $query = "update postback set amount=:amount where tr_id=:tr_id;";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":amount", $this->amount);
            $stmt->bindParam(":tr_id", $this->tr_id);

        }else{
            $query = "INSERT INTO $this->table_name
				SET
				client_id=:client_id,
				tr_id=:tr_id,
				full_name=:full_name,
				email=:email,
				phone=:phone,
				amount=:amount,
				tr_date=:tr_date,
				aff_id=:aff_id,
				camp_id=:camp_id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":client_id", $this->client_id);
            $stmt->bindParam(":tr_id", $this->tr_id);
            $stmt->bindParam(":full_name", $this->full_name);
            $stmt->bindParam(":email", $this->cl_email);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":amount", $this->amount);
            $stmt->bindParam(":tr_date", $this->tr_date);
            $stmt->bindParam(":aff_id", $this->aff_id);
            $stmt->bindParam(":camp_id", $this->camp_id);

        }





		if ($stmt->execute()) {
		    return true;
		}

		return false;
	}


    function get_postback_by_tr_id($tr_id){
        $query = "SELECT *
                FROM postback
                WHERE tr_id = '$tr_id'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row;
        }

        return false;

    }

	public function lead_status($data){

        $stmt = $this->conn;
        $status = true;


        $query = "UPDATE leads_hidden SET crm_status = '$data->status'  where email = '$data->email';";
        if(!$stmt->prepare($query)->execute()){
            if($stmt->affected_rows != 1){
                $status = false;
            }
        }

        if(!$status){
            $query = "UPDATE leads SET crm_status = '$data->status'  where email = '$data->email' AND manager = 0;";
            if($stmt->prepare($query)->execute()){
                if($stmt->affected_rows != 1){
                    $status = false;
                }

            }
        }

        logs('postback-lead_status', $query);

        return $status;

    }



}
?>