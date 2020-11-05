<?php
//require_once '../db/DB.php';
/**
 * Description of regSentEmail
 *
 * @author pedro
 */
class regSentEmail {
    private $db;
    private $user;
    private $destino;
    private $assunto;

    /**
     * 
     * @param int $u
     * @param string $d
     * @param string $a
     */
    public function __construct($u,$d,$a) {
        $this->db = new DB();
        $this->user = $u;
        $this->destino = $d;
        $this->assunto = $a;
      
    }
    /**
     * Registar o envio com sucesso
     */
    public function registOk() {
        //insere como com sucesso
        $this->db->query("INSERT INTO arq_logemail(user,destino,assunto) VALUES(:user,:destino,:assunto)",
                array(':user'=>$this->user,':destino'=>$this->destino, ':assunto'=>$this->assunto));
    }
    /**
     * Registar erro no envio
     * @param type $erro
     */
    public function registErro($erro) {
        //insere como com sucesso
        $this->db->query("INSERT INTO arq_logemail(user, destino, assunto, erro) VALUES(:user, :destino, :assunto, :erro)",
                array(':user'=>$this->user,':destino'=>$this->destino, ':assunto'=>$this->assunto, ':erro'=>$erro));
    }
}
