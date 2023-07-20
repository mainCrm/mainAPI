<?php


class Brands
{

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function get_all(){
        $query = "SELECT *
                FROM brands";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function get_last_brand($aff_id){

        $query = "SELECT *
                    FROM brand_affs
                    WHERE aff_id = $aff_id
                    ORDER BY last_get_lead ASC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($row)){
            return $row[0]['brand_id'];
        }

        return false;

    }



}