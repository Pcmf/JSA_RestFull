<?php
require_once 'passwordHash.php';
require_once 'db/DB.php';
/**
 * Description of Login
 *
 * @author pedro
 */
class Login {
    private $db;
    private $valido;
    private $token;
    
    public function __construct() {
        $this->db = new DB();
    }
    
    
    public function checkuser($username, $password) {
       if ($resp = $this->db->query("SELECT * FROM utilizadores WHERE username LIKE :user ", array(':user' =>$username))) {
            //verificar se a password e utilizador correspondem
            $this->valido = false;
            foreach ($resp AS $r) {
          //      if ($r['password'] == $password) {
                if (passwordHash::check_password($r['password'], $password)) {
                    $this->token = generateToken($r);
                    $this->db->query("UPDATE utilizadores SET token=:token WHERE id=:id ", array(':token'=> $this->token, ':id'=>$r['id']));
                    $this->valido = true;
                    break;    
                }
            }
            if($this->valido){
                return $this->token;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
}

            /**
            * Check token and return user ID or false
            */
           function generateToken($resp) {
               //Chave para a encriptação
               $key = 'klEApJG93';

               //Configuração do JWT
               $header = [
                   'alg' => 'HS256',
                   'typ' => 'JWT'
               ];

               $header = json_encode($header);
               $header = base64_encode($header);
               
               //Obter o nome do fornecedor
               
               //Dados 
               $payload = [
                   'iss' => 'JSA',
                   'id' => $resp['id'],
                   'nome' => $resp['nome'],
                   'username' => $resp['username'],
                   'tipo'=> $resp['tipo'],
               ];

               $payload = json_encode($payload);
               $payload = base64_encode($payload);

               //Signature

               $signature = hash_hmac('sha256', "$header.$payload", $key, true);
               $signature = base64_encode($signature);

               return "$header.$payload.$signature";
           }
