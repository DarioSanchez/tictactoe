<?php
namespace app\models;

use app\models\ModelGame;

class Tictactoe extends ModelGame
{
	const WINNING_COMBINATIONS = [
        [1,2,3],
        [4,5,6],
        [7,8,9],
        [1,4,7],
        [2,5,8],
        [3,6,9],
        [1,5,9],
        [3,5,7],
        [5,1,9],
        [5,3,7],
        [5,9,1],
        [5,4,3],
        [5,2,8]
        ];
	/*
	 * Create activity on database for click in letter
	 * @params $data json
	 */
    public function createActivity($data)
    {
        $stmt = $this->conection()->prepare("INSERT INTO activity (id,session_id,activity,created_at) 
        VALUES (null,'".sessionUser()."','".$data."','".now()."')");
        try {
            $stmt->execute();
        }catch (\Exception $e){
            $this->conection()->rollback();
            print_r($e->getMessage());
            $e->getMessage();
        }
    }
    /*
    * Verify that the user's session exists
    * @params
    */
    public function existSessionUser()
    {
        $select = $this->conection()->prepare("Select count(id) from records where session_id='".sessionUser()."'");
        try {
            $select->execute();
        }catch (\Exception $e){
            $this->conection()->rollback();
            $this->createActivity($e->getMessage());
        }
        return $select->fetchColumn();
    }
    /*
    * Get data registry
    */
    public  function  getRegistry()
    {
        $select = $this->conection()->prepare("Select registry from records where session_id='".sessionUser()."'");
        $select->execute();
        return $select->fetch();
    }
    /*
    * Insert data records json in the table
    * @params $data json
    */
    public function insertRecords($data)
    {
        $insert = $this->conection()->prepare("INSERT INTO records (id,session_id,registry,created_at,update_at)
        VALUES (null,'".sessionUser()."','[".$data."]','".now()."',null)");
        try {
            $insert->execute();
            $this->createActivity('Insert letter in records');
        }catch (\Exception $e){
            $this->conection()->rollback();
            $this->createActivity($e->getMessage());
        }
    }
    /*
    * Update data records json in the table
    * @params $data json
    */
    public function updateRecords($data)
    {
        $obj = new \stdClass();

        try {
            $select = $this->conection()->prepare("Select * from records where session_id='".sessionUser()."'");
            $select->execute();
            $obj->registry = json_decode($select->fetchAll()[0]['registry']);
            $obj->registry[] = json_encode($data);
            $obj->registrydb = str_replace(array('\\', '""'),'',json_encode($obj->registry));
            $update = $this->conection()->prepare("UPDATE records 
                SET registry    = ".json_encode($obj->registrydb)."
                WHERE session_id='".sessionUser()."' ");
            $update->execute();
            $this->createActivity('Insert letter in records and update json');
        }catch (\Exception $e){
            $this->conection()->rollback();
            $this->createActivity($e->getMessage());
        }
    }
    /*
    * Create data records json in the table
    * @params $data json
    */
    public function createRecords($data)
    {
        if ($this->existSessionUser() > 0) {
            $this->updateRecords($data);
        } else {
            $this->insertRecords($data);
        }
    }


}