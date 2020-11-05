<?php
//require_once '../db/DB.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Agenda
 *
 * @author pedro
 */
class Agenda {
    private $db;
    private $dataAtual;
    private $diaSemanaAtual;
    /**
     * 
     */
    public function __construct() {
        $this->db = new DB();
        $this->dataAtual = date('Y-m-d');
        $this->diaSemanaAtual = date('w', strtotime(date('Y-m-d')));
    }
    
    public function getCountAtivos($user) {
        return $this->db->query("SELECT count(*) AS qty FROM agenda WHERE dataagenda<=DATE(NOW()) AND horaagenda <= HOUR(NOW()) AND ativo=1 AND user=:user "
                , array(':user'=>$user));        
    }
    
    public function getAgenda($user) {
        return $this->db->query("SELECT lead FROM agenda WHERE dataagenda<=DATE(NOW()) AND horaagenda <= HOUR(NOW()) AND ativo=1 AND user=:user"
                . " ORDER BY dataagenda, horaagenda LIMIT 1", array(':user'=>$user));
    }
    
    /**
     * Registar Agendamento
     * @param type $user
     * @param type $data
     * @param type $hora
     * @param type $lead
     * @return type
     */
    public function agendaManual($user, $data, $hora, $lead) {
        //limpar agendamentos anteriores
        $this->limparAgendamentos($lead);
        
        //Registar novo agendamento
        return $this->db->query("INSERT INTO agenda(lead, dataagenda, horaagenda, ativo, user) VALUES(:lead, :dataagenda, :horaagenda, 1, :user)", 
                array(':lead'=>$lead, ':dataagenda'=>$data, ':horaagenda'=>$hora , ':user'=>$user));
        
    }
    
    /**
     * Limpar todos os agendamentos ativos para uma lead
     * @param type $lead
     */
    public function limparAgendamentos($lead) {
        $this->db->query("UPDATE agenda SET ativo=0 WHERE ativo=1 AND lead=:lead", array(':lead'=>$lead));
    }
    
    public function agendaAutomatico($user, $lead) {
             //limpar agendamentos anteriores
            $this->limparAgendamentos($lead);
            //Faz o agendamento para o periodo diferente do atual
            
            
    }
}
