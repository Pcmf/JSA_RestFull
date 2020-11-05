<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Turn
 *
 * @author pedro
 */
class Turn {
    private $db;
    private $turn;
    private $user;
    
    public function __construct($user) {
        $this->user = $user;
        $this->db = new DB();
        $this->turn = $this->db->query("SELECT tipo FROM gertcontrol WHERE user=:user" , array(':user'=>$user));
        $this->setTurn($turn);
        return $this->turn;
    }

   function setTurn($turn) {
       $turn=='N' ? $turn = 'A' : $turn='N';
        $this->db->query("UPDATE getcontrol SET tipo=:turn WHERE user=:user ", array(':user'=> $this->user, ':turn'=>$turn));
    }


}
