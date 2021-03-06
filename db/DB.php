<?php
require_once 'configs.php';
/**
 * Description of newPHPClass
 *
 * @author pedro
 */
class DB {
    private $pdo;
    private $lastId;
    
    public function __construct() {
        $pdo = new PDO('mysql:host='.host.';dbname='.database.';charset=utf8',username,password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }
    
    public function query($query,$params=array()){
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $this->lastId = $this->pdo->lastInsertId();
        
        if(explode(' ',$query)[0]=='SELECT'){
            $data = $statement->fetchAll();
            return $data;
        }
    }
    
    public function lastInsertId() {
        return $this->lastId;
    }
}
