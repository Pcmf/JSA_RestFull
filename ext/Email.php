<?php
//require_once '../db/DB.php';
require_once 'emailConfig.php';
require_once 'class.phpmailer.php';
require_once 'class.smtp.php';
require_once 'class.regSentEmail.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author pedro
 */
class Email {
    private $db;
    private $user;
    private $emailOrigem;
    private $emailDestino;
    private $assunto;
    private $msg;
    private $anexos;
    
    
    
    public function __construct($user, $emailOrigem, $emailDestino, $assunto, $msg, $anexos) {
         $this->db = new DB();
         $this->user = $user;
         $this->emailOrigem = $emailOrigem;
         $this->emailDestino = $emailDestino;
         $this->assunto = $assunto;
         $this->msg = $msg;
         $this->anexos = $anexos;
         
         $footer = "Texto que se repete em todos os emails, no final da pagina.";
         
         $this->sendEmail();
    }
    
    
    
    private function sendEmail(){
           //Enviar o email

            $mail = new PHPMailer();

         //   $mail->SMTPDebug=4;
            $mail->isSMTP();
            $mail->Host = EHOST;
            $mail->Port = EPORT;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = $this->emailOrigem;
            $mail->Password = EPASS;
            $mail->setFrom($this->emailOrigem, utf8_decode(ENOME));
            $mail->addAddress($this->emailDestino);
            //

            $mail->Subject = utf8_decode($this->assunto);
            $mail->isHTML(TRUE);
            $mail->Body = utf8_decode($this->msg);
            if(isset($this->anexos) && sizeof($this->anexos)>0){
                foreach ($this->anexos as $anexo) {
                    $mail->addAttachment($anexo->path, $anexo->nome);
                }
            }
             //
            //LOG
            $log = new regSentemail($this->user, $this->emailDestino, $this->assunto);
            if(!$mail->send()){
                echo 'Erro no envio! Contactar suporte.\n\ Mailer error: '.$mail->ErrorInfo;
                $log->registErro($mail->ErrorInfo);
            } else { 
                //atualizar o status da LEAD para indicar que foi enviado email (?)
                //TO DO
                echo 'Mensagem enviada com sucesso.';
                $log->registOk();
            }    
    }
}
