<?php
require_once 'db/DB.php';

/**
 * Description of Entidades
 *
 * @author pedro
 */
class Entidades {
    private $db;
    
    public function __construct() {
        $this->db = new DB();
    }
    /**
     * 
     * @return array
     */
    public function getAll() {
        return $this->db->query("SELECT * FROM entidades");
    }
    /**
     * 
     * @param int $id
     * @return array
     */
    public function getOne($id) {
        return $this->db->query("SELECT * FROM entidades WHERE id=:id ", array(':id'=>$id));
    }
    
    /**
     * 
     * @param array $obj
     */
    public function setEntidade($obj){
        !isset($obj->telefone) ? $obj->telefone=null : null;
        $result = $this->db->query("INSERT INTO entidades(entidade, email, telefone, modelopdf) VALUES(:entidade, :email, :telefone, :modelopdf ) "
                ,array(':entidade'=>$obj->entidade, ':email'=>$obj->email, ':telefone'=>$obj->telefone, ':modelopdf'=>$obj->modelopdf ));
        return $this->db->lastInsertId();
    }
    /**
     * 
     * @param int $id
     * @param array $obj
     * @return type
     */
    public function update($id, $obj){
        return $this->db->query("UPDATE entidades SET entidade=:entidade, email=:email, telefone=:telefone, modelopdf=:modelopdf, ativo=1 WHERE id=:id "
                 ,array(':nome'=>$obj->nome, ':email'=>$obj->email, ':telefone'=>$obj->telefone, ':modelopdf'=>$obj->modelopdf, ':id'=>$id ));
    }
    /**
     * 
     * @param int $id
     * @return result
     */
    public function delete($id) {
        //Verifica se a entidade já tem movimentos na BD
        $result = $this->db->query("SELECT count(*) FROM mapadividas WHERE entidade=:entidade ", array(':entidade'=>$id));
        if($result[0]>0){
            return $this->db->query("UPDATE entidades SET ativo=0 WHERE  id=:id", array(':id'=>$id));
        } else {
            return $this->db->query("DELETE FROM entidades WHERE id=:id", array(':id'=>$id));
        }
    }
    
}
