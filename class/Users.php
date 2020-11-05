<?php
require_once 'db/DB.php';
require_once 'passwordHash.php';

/**
 * Description of Users
 *
 * @author pedro
 */
class Users {
    private $db;
    
    public function __construct() {
        $this->db = new DB();
    }
    /**
     * 
     * @return array
     */
    public function getAll() {
        return $this->db->query("SELECT id, nome, email, telefone, username, tipo, ativo FROM utilizadores");
    }
    /**
     * 
     * @param int $user
     * @return array
     */
    public function getOne($user) {
        return $this->db->query("SELECT id, nome, email, telefone, username, tipo, ativo FROM utilizadores WHERE id=:id ", array(':id'=>$user));
    }
    
    /**
     * 
     * @param array $obj
     */
    public function setUser($obj){
        $password = passwordHash::hash($obj->password); 
        !isset($obj->tipo) ? $obj->tipo=null : null;
        
        $result = $this->db->query("INSERT INTO utilizadores(nome, email, telefone, username, password, tipo) VALUES(:nome, :email, :telefone, :username, :password, :tipo) "
                , array(':nome'=>$obj->nome, ':email'=>$obj->email, ':telefone'=>$obj->telefone, ':username'=>$obj->username, ':password'=>$password, ':tipo'=>$obj->tipo ));
        $this->db->query("INSERT INTO getcontrol(user) VALUES(:user) ", array(':user'=>$this->db->lastInsertId()));
        return $this->db->lastInsertId();
    }
    /**
     * 
     * @param int $user
     * @param array $obj
     * @return type
     */
    public function update($user, $obj){
        $password = passwordHash::hash($obj->password); 
        return $this->db->query("UPDATE utilizadores SET nome=:nome, email=:email, telefone=:telefone, username=:username, password=:password, tipo=:tipo, ativo=1 WHERE id=:id "
                 ,array(':nome'=>$obj->nome, ':email'=>$obj->email, ':telefone'=>$obj->telefone, ':username'=>$obj->username, ':password'=>$password, ':tipo'=>$obj->tipo, ':id'=>$user ));
    }
    /**
     * 
     * @param int $user
     * @return result
     */
    public function delete($user) {
        //Verifica se o utilizador já tem movimentos no leads ou processo
        $result = $this->db->query("SELECT count(*) FROM leads WHERE user=:user ", array(':user'=>$user));
        if($result[0]>0){
            return $this->db->query("UPDATE utilizadores SET ativo=0 WHERE  id=:id", array(':id'=>$user));
        } else {
            return $this->db->query("DELETE FROM utilisadores WHERE id=:id", array(':id'=>$user));
        }
    }
    
}
