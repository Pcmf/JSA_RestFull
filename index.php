<?php

require_once './db/configs.php';
require_once './db/DB.php';
require_once './class/Login.php';
require_once './class/Users.php';
require_once './class/Leads.php';
require_once './class/ListaDocs.php';
require_once './pdfs/classProcuracao.php';


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");


/*
 * * POSTS
 * *
 */
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $postBody = file_get_contents("php://input");
    $postBody = json_decode($postBody);

    if ($_GET['url'] == "login") {
        $ob = new Login();
        $resp = $ob->checkuser($postBody->username, $postBody->password);
        if ($resp) {
            echo json_encode($resp);
            http_response_code(200);
        } else {
            echo null;
            http_response_code(200);
        }
        // Procuração            
    } elseif ($_GET['url'] == "procuracao") {
        $resp = new classProcuracao($postBody->lead);
        if ($resp) {
            echo json_encode($resp);
            http_response_code(200);
        } else {
            echo null;
            http_response_code(200);
        }
     
    } elseif ($_GET['url'] == "listadocs") {
        $ob = new ListaDocs();
            echo json_encode($ob->setDoc($postBody));
            http_response_code(200);
            
    } elseif ($_GET['url'] == "users") {
        echo json_encode($postBody);
        $ob = new Users();
        echo json_encode($ob->setUser($postBody));
        http_response_code(200);
    }
    
        /**
         * GETS
         */
    } elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
        if ($_GET['url'] == 'leads') {
            $ob = new Leads();
            if (isset($_GET['id'])) {
                echo json_encode($ob->getOne($_GET['user'], $_GET['id']));
            } else {
                echo json_encode($ob->getAll($_GET['user']));
            }
            http_response_code(200);
            
         // Get new lead   
        }   elseif ($_GET['url'] == 'getlead') {
                $ob = new Leads();
                echo json_encode($ob->getNew($_GET['user']));
                http_response_code(200);
                
         //utilizadores       
        }   elseif ($_GET['url'] == 'users') {
                $ob = new Users();
                if(isset($_GET['user'])){
                    echo json_encode($ob->getOne($_GET['user']));    
                } else {
                    echo json_encode($ob->getAll());
                }
                http_response_code(200);
                
          //Dados do dashboard      
        }   elseif ($_GET['url'] == 'getcount') {
                $ob = new Leads();
                echo json_encode($ob->getCount($_GET['user']));
                http_response_code(200);
        
         //lista do documentos necessários
        }  elseif ($_GET['url'] == 'listadocs') {
                $ob = new ListaDocs();
                echo json_encode($ob->getAll());
                http_response_code(200);
        // Agendados        
        } elseif ($_GET['url'] == 'agenda') {
                $ob = new Leads();
                if(isset($_GET['lead'])){
                    echo json_encode($ob->getOneAgenda($_GET['user'], $_GET['lead']));
                } else {
                    echo json_encode($ob->getAllAgenda($_GET['user']));
                }
                http_response_code(200);
         //Aguardam Documentos       
        }  elseif ($_GET['url'] == 'agdocs') {
                $ob = new Leads();
                if(isset($_GET['lead'])){
                    echo json_encode($ob->getOneAg($_GET['user'], $_GET['lead'], 5));
                } else {
                    echo json_encode($ob->getAllAg($_GET['user'], 5));
                }
                http_response_code(200);
          //Aguardam pagamentos      
        }   elseif ($_GET['url'] == 'agpag') {
                $ob = new Leads();
                if(isset($_GET['lead'])){
                    echo json_encode($ob->getOneAg($_GET['user'], $_GET['lead'], 6));
                } else {
                    echo json_encode($ob->getAllAg($_GET['user'], 6));
                }
                http_response_code(200);
                
        }  else {
            http_response_code(206);
        }
        /**
         * PUTS
         */
    } elseif ($_SERVER['REQUEST_METHOD'] == "PUT") {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        if ($_GET['url'] == 'upleads') {
            $ob = new Leads();
            $resp = $ob->upLead($_GET['lead'], $postBody);
            if ($resp != "Erro") {
                echo json_encode($resp);
                http_response_code(200);
            } else {
                echo $resp;
                http_response_code(204);
            }
            
        } elseif ($_GET['url'] == "listadocs") {
            $ob = new ListaDocs();
            echo json_encode($ob->update($_GET['id'], $postBody));
            http_response_code(200);
            
        }  elseif($_GET['url'] == "users"){
            $ob = new Users();
            echo json_encode($ob->update($_GET['user'], $postBody));
            http_response_code(200);
            
            //Rejeitar
        }  elseif($_GET['url'] == "rej"){
            $ob = new Leads();
            echo json_encode($ob->rejectLead($_GET['lead']));
            http_response_code(200);
            
            //Não atende Agendamento automatico
        }  elseif($_GET['url'] == "nat"){
            $ob = new Leads();
            echo json_encode($ob->naoAtende($_GET['user'], $_GET['lead']));
            http_response_code(200);
            
            //Agendamento manual
        }  elseif($_GET['url'] == "agm"){
            $ob = new Leads();
            echo json_encode($ob->update($_GET['user'], $postBody));
            http_response_code(200);
        }
        /**
         * DELETES
         */
    } elseif ($_SERVER['REQUEST_METHOD'] == "DELETE") {

        if ($_GET['url'] == 'docs') {
            $ob = new Docs();
            echo json_encode($ob->delete($_GET['lead'], $_GET['tipodoc']));
            http_response_code(200);
            
        } elseif ($_GET['url'] == "listadocs") {
            $ob = new ListaDocs();
            echo json_encode($ob->delete($_GET['id']));
            http_response_code(200);
        }

//FIM
    } else {
        http_response_code(405);
    }