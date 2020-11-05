<?php
require_once 'db/DB.php';
include 'status.php';
include './ext/Agenda.php';
include './ext/Email.php';
include './ext/Turn.php';
/**
 * Description of Leads
 *
 * @author pedro
 */
class Leads {
    private $db;
    
    public function __construct() {
        $this->db = new DB();
    }
    
    /**
     * 
     * @param type $user
     * @return type
     */
    public function getAll($user) {
        return $this->db->query("SELECT * FROM leads WHERE user=:user ", array(':user'=>$user));
    }
    /**
     * 
     * @param type $user
     * @param type $lead
     * @return type
     */
    public function getOne($user, $lead) {
        return $this->db->query("SELECT * FROM leads WHERE lead=:lead", array(':lead'=>$lead));
    }  
    
    /**
     * 
     * @param int $user
     * @return array
     */
    public function getNew($user) {
        // Verificar se existe alguma lead com status=2  para este user. Se existir seleciona
        $result = $this->db->query("SELECT * FROM leads WHERE status=2 AND user=:user ORDER BY dataentrada LIMIT 1", array(':user'=>$user));
        if($result){
            return $result[0];
        //Não existe ativa    
        } else {  
            if(new Turn($user) ==='N'){
                $result = $this->db->query("SELECT * FROM leads WHERE status=1 ORDER BY dataentrada LIMIT 1");
                if($result){
                    $this->db->query("UPDATE leads SET status=2, datastatus=NOW(), user=:user WHERE lead=:lead", array(':user'=>$user, ':lead'=>$result[0]['lead']));
                     return $result;
                } else {
                    $this->getNew($user);   
                }
            } else {
                //verificar agendados
                if($lead =(new Agenda ())->getAgenda($user)){
                    return $this->db->query("SELECT * FROM leads WHERE lead=:lead ",array(':lead'=>$lead));
                } else {
                    $this->getNew($user);
                }
            }
        }
        
    }
    /**
     * 
     * @param type $user
     * @return type
     */
    public function getCount($user) {
        $resp = array();
        
        // Contar os novos e atribuidos 
        $result = $this->db->query("SELECT count(*) AS novas FROM leads WHERE status=1 OR (status=2 AND user=:user) ", array(':user'=>$user));
        $resp['novas'] = 0;
        if($result){
            $resp['novas'] = $result[0]['novas'];
        }    
        
        // Contar os agendadas com data anterior
        $result = (new Agenda())->getCountAtivos($user);
        $resp['agendaativas'] = 0;
        if($result){
            $resp['agendaativas'] = $result[0]['qty'];
        }   
        
        // Contar os agendadas
        $result = $this->db->query("SELECT count(*) AS agendadas FROM leads WHERE status IN(3,4)  AND user=:user ", array(':user'=>$user));
        $resp['agendadas'] = 0;
        if($result){
            $resp['agendadas'] = $result[0]['agendadas'];
        }
        
        // Contar os Aguardam documentos
        $result = $this->db->query("SELECT count(*) AS agdocs FROM leads WHERE status=5  AND user=:user ", array(':user'=>$user));
        $resp['agdocs'] = 0;
        if($result){
            $resp['agdocs'] = $result[0]['agdocs'];
        }  
        
        // Contar os Aguardam pagamento
        $result = $this->db->query("SELECT count(*) AS agpag FROM leads WHERE status=6  AND user=:user ", array(':user'=>$user));
        $resp['agpag'] = 0;
        if($result){
            $resp['agpag'] = $result[0]['agpag'];
        }  
        
        // Contar as Anuladas
        $result = $this->db->query("SELECT count(*) AS anul FROM leads WHERE status IN(9,10,11) AND user=:user ", array(':user'=>$user));
        $resp['anul'] = 0;
        if($result){
            $resp['anul'] = $result[0]['anul'];
        }    
        
        // Lista de leads com processos em andamento
        $resp['lista'] = [];
        $result = $this->db->query("SELECT L.*, S.status AS situacao, S.descricao FROM leads L"
                . " INNER JOIN statuslead S ON S.id=L.status "
                . " WHERE L.status IN(7,8) AND L.user=:user", array(':user'=>$user));
        if($result){
            $resp['lista'] = $result;
        }  
        
        return $resp;
    }
    /**
     * Regista os dados do cliente na lead
     * @param type $user
     * @param type $obj
     */
    public function registaLead($user, $obj) {
        $dprofissionais = $obj->dprofissionais;
        $dhabitacao = $obj->dhabitacao;
        $d2proponente = $obj->d2proponente;
        $result = $this->db->query("UPDATE leads SET nome=:nome, email=:email, telefone=:telefone, nif=:nif, idade=:idade, estadocivil=:estadocivil, filhos=:filhos,"
                . " irs=:irs, dhabitacao=:dhabitacao, dprofissionais=:dprofissionais, =:d2proponente, status=2, datastatus=NOW(), user=:user ",
                array(':nome'=>$obj->nome, ':email'=>$obj->email, ':telefone'=>$obj->telefone, ':nif'=>$obj->nif, ':idade'=>$obj->idade, ':estadocivil'=>$obj->estadocivil, 
                    ':filhos'=>$obj->filhos, ':irs'=>$obj->irs, ':dhabitacao'=>$dhabitacao, ':dprofissionais'=>$dprofissionais, ':d2proponente'=>$d2proponente, ':user'=>$user));
        $mapaDiv = $obj->mapaDiv;
    }
    /**
     * 
     * @param type $user
     * @param type $status
     * @param type $lead
     * @return type
     */
    public function rejectLead( $lead) {
        (new Agenda())->limparAgendamentos($lead);
        return $this->upStatus($lead, 9);
    }
    
    public function naoAtende($user, $lead) {
        //Faz o agendamento
        (new Agenda())->agendaAutomatico($user, $lead);
        //atualiza o status
        $this->upStatus($lead, 3);
        //envia email (?)
    }
    
    /**
     * 
     * @param type $user
     * @return type
     */
    public function getAllAgenda($user) {
        return $this->db->query("SELECT L.lead, L.nome, L.telefone, L.email, A.dataagenda, A.horaagenda, S.status, s.descricao "
                . " FROM leads L "
                . " INNER JOIN agenda A ON A.lead=L.lead "
                . " INNER JOIN statuslead S ON S.id=L.status "
                . " WHERE L.user=:user AND L.status IN(3,4)  AND A.ativo=1 "
                . " ORDER BY dataagenda, horaagenda, lead", array(':user'=>$user));
    }
    /**
     * 
     * @param type $user
     * @param type $lead
     * @return type
     */
    public function getOneAgenda($user, $lead) {
        return $this->db->query("SELECT L.lead, L.telefone, L.email, A.dataagenda, A.horaagenda "
                . " FROM leads L "
                . " INNER JOIN agenda A ON A.lead=L.lead "
                . " WHERE L.user=:user AND L.lead=:lead AND L.status= 4 AND A.ativo=1 "
                . " ORDER BY dataagenda, horaagenda, lead", array(':user'=>$user, ':lead'=>$lead));
    }
    
        /**
     * @category Aguardam documentação
     * @param type $user
     * @return type
     */
    public function getAllAg($user, $sts) {
        return $this->db->query("SELECT L.lead, L.nome, L.telefone, L.email, A.dataagenda, DATEDIFF( NOW(), A.dataagenda) AS atraso  "
                . " FROM leads L "
                . " INNER JOIN agenda A ON A.lead=L.lead "
                . " WHERE L.user=:user AND L.status=:sts "
                . " ORDER BY dataagenda, horaagenda, lead", array(':user'=>$user, ':sts'=>$sts));
    }
    /**
     * @category Aguardam documentação
     * @param type $user
     * @param type $lead
     * @return type
     */
    public function getOneAg($user, $lead, $sts) {
        return $this->db->query("SELECT L.lead, L.telefone, L.email, A.dataagenda "
                . " FROM leads L "
                . " INNER JOIN agenda A ON A.lead=L.lead"
                . " WHERE L.user=:user AND L.lead=:lead AND L.status=:sts "
                . " ORDER BY dataagenda, horaagenda, lead", array(':user'=>$user, ':lead'=>$lead, ':sts'=>$sts));
    }


    public function agendaLead($param) {
        //limpar agendamentos anteriores
        //agendar a lead
        //atualizar o status da lead
    }
    
    
    private function  upStatus($lead, $status) {
        $this->db->query("UPDATE leads SET status=:status, datastatus=NOW() WHERE lead=:lead", array(':lead'=>$lead, ':status'=>$status));
    }
}
