<?php
require_once 'db/DB.php';

/**
 * Description of ListaDocs
 *
 * @author pedro
 */
class ListaDocs {
    private $db;
    
    public function __construct() {
        $this->db = new DB();
    }
    /**
     * 
     * @return array
     */
    public function getAll() {
        return $this->db->query("SELECT * FROM listadocs");
    }
    /**
     * 
     * @param int $id
     * @return array
     */
    public function getOne($id) {
        return $this->db->query("SELECT * FROM listadocs WHERE id=:id ", array(':id'=>$id));
    }
    
    /**
     * 
     * @param array $obj
     */
    public function setDoc($obj){
        !isset($obj->descricao) ? $obj->descricao=null : null;
        $result = $this->db->query("INSERT INTO listadocs(documento, descricao, proponente, sigla) VALUES(:documento, :descricao, :proponente, :sigla ) "
                ,array(':documento'=>$obj->documento, ':descricao'=>$obj->descricao, ':proponente'=>$obj->proponente, ':sigla'=>$obj->sigla ));
        return $this->db->lastInsertId();
    }
    /**
     * 
     * @param int $id
     * @param array $obj
     * @return type
     */
    public function update($id, $obj){
        return $this->db->query("UPDATE listadocs SET documento=:documento, descricao=:descricao, proponente=:proponente, sigla=:sigla, ativo=1 WHERE id=:id "
                 ,array(':documento'=>$obj->documento, ':descricao'=>$obj->descricao, ':proponente'=>$obj->proponente, ':sigla'=>$obj->sigla, ':id'=>$id ));
    }
    /**
     * 
     * @param int $id
     * @return result
     */
    public function delete($id) {
        //Verifica se o documento já tem movimentos no leads ou processo
        $result = $this->db->query("SELECT count(*) AS reg FROM docspedidos WHERE documento=:documento ", array(':documento'=>$id));
        if($result[0]['reg']>0){
            return $this->db->query("UPDATE listadocs SET ativo=0 WHERE  id=:id", array(':id'=>$id));
        } else {
            return $this->db->query("DELETE FROM listadocs WHERE id=:id", array(':id'=>$id));
        }
    }
    
}
