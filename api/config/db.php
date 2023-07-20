<?php
date_default_timezone_set('Europe/Kiev');
global $AFF_ID;
$AFF_ID = 0;
$api_key = htmlspecialchars(strip_tags($_GET['api_key']));
 $conn = new mysqli('157.90.209.115', 'mAdmin', 'GhjcnjGfhjkm2022', 'main', '3306');

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
if($api_key == 'b95e2bacad80383cc5c1658356783760'){
    $affiliate = [
        'id' => 0,
    ];
}else{
    $query = "SELECT * FROM affiliate WHERE token = '$api_key'";
    $result = $conn->query($query);

    $affiliate = mysqli_fetch_assoc($result);
}

if($affiliate){
    $AFF_ID = $affiliate['id'];
class Database {

     private $host = "157.90.209.115";
     private $db_name = "main";
     private $username = "mAdmin";
     private $password = "GhjcnjGfhjkm2022";

    
    public $conn;

    // получаем соединение с БД 
    public function getConnection(){

        $this->conn = null;
if (empty($_GET['api_key'])){ echo 'error';die;}
else{
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
//	$query = mysqli_query("SELECT *
//                FROM
//                    `token` WHERE
//                    `api_key` = '".$_GET['api_key']."'");
//        $result = mysqli_fetch_assoc($query);
//        if ($result){
//
//		}
}
        return $this->conn;
    }
}
}else{
    echo 'error';die;
}


    function logs($filename, $data)
    {
//        return false;
        $root = realpath(dirname(__FILE__) . "/../");

        ob_start();
        var_dump($data);
        $output = ob_get_clean();
        $path = $root . '/logs/';


        $dir_name = date("Y-m-d");
        if(!file_exists($path . $dir_name)){
            mkdir( $path . $dir_name, 0777);
        }
        $file_path = $path . $dir_name . '/' . $filename . '.log';
        $file = fopen($file_path, "a");
        $date = date("Y.m.d, h:i:s");
        $text  = '[' . $date . ']' . "\r\n";
        $text .= $output . "\r\n";
        fwrite($file, $text);
        fclose($file);


    }