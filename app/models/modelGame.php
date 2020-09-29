<?php
namespace app\models;
use PDO;

class ModelGame 
{
	private $host;
	private $dbname;
	private $username;
	private $password;

	public function __construct()
	{
		$this->host 	= 'localhost';
        $this->dbname   = 'tictactoe';
        $this->username = 'root';
        $this->password = '';
	}
    /*
    *  Connection data base
    * @response $conn
    */
	public function conection()
	{
        $conn = null;
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
        } catch (PDOException $pe) {
            throw new Exception("Could not connect to the database $this->dbname :" . $pe->getMessage());
        }
        return $conn;
	}
    /*
    *  factory model
    * @response $command
    */
	public  function factory($model)
	{
        $command = $this->conection()->prepare("select * from {$model}");
        $command->execute();
        return $command;
	}


	public function all()
    {
       print_r(func_get_args());
    }
	
}
