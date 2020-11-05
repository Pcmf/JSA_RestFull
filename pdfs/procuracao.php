<?php
/* 
 * Criação de PDF para procuração
 */

require_once 'fpdf.php';
require_once '../db/DB.php';

$lead = file_get_contents("php://input");


