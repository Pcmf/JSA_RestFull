<?php
require_once 'fpdf.php';
require_once 'db/DB.php';
/**
 * Description of classProcuracao
 *
 * @author pedro
 */
class classProcuracao {
    private $db;
    
    public function __construct($lead) {
        $this->db = new DB();
        //obter os dados da lead
        $result = $this->db->query("SELECT L.*, U.nome AS gnome, U.telefone AS gtelefone, U.email AS gemail "
                . " FROM leads L"
                . " INNER JOIN utilizadores U ON U.id=L.user"
                . " WHERE L.id=:lead ",
                array(':lead'=>$lead));
        
        if($result){
            //Criar pdf
            $this->criarPDF($result[0]);
        }
        
    }
    
    
    private function criarPDF($param) {
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->AddPage()
                ;
        //Criar cabeçalho
        $pdf->SetFont('Times','B',12);
        $pdf->MultiCell(0,12,utf8_decode('PROCURAÇÃO'),0,'C');
        $pdf->Ln(1);
        $pdf->SetFont('Times','',10);
        $pdf->MultiCell(0,8,utf8_decode($param['nome']));

        
        $pdf->Ln(2);
        $pdf->MultiCell(0, 6, "	Lorem ipsum mauris mi cras tellus convallis nostra, ut suscipit commodo pretium molestie lorem adipiscing cursus,"
                . " quisque scelerisque lectus aliquam libero ultricies. aenean purus nulla sodales ipsum ante vulputate himenaeos, ut ultricies aptent aliquet vitae pulvinar,"
                . " quisque fermentum aenean vehicula ac viverra. dapibus malesuada massa senectus aenean laoreet faucibus, phasellus tellus platea ligula congue."
                . " dolor primis sagittis cursus eleifend hac varius leo metus laoreet faucibus consectetur, rhoncus tincidunt massa condimentum malesuada felis pretium odio"
                . " in lacus felis, sem nunc leo donec commodo taciti nulla nullam fringilla vestibulum. faucibus vehicula maecenas neque aliquet mauris volutpat, magna"
                . " sem nam torquent vestibulum ac quisque, posuere ultrices orci sollicitudin congue. ", 0);
        
        // Obter numero de linha
        $linha = $this->db->query("SELECT count(*)+1 FROM procuracoes WHERE lead=:lead", array(':lead'=>$param['id']));
        // Guardar o documento
        $doc = 'C:\xampp/htdocs/JSA_RestFul/doc/proc_'.$param['id'].'_'.$linha[0][0].'.pdf';
        $pdf->Output($doc,'F');
        
        // Guardar com base64
        $fx64 = base64_encode(file_get_contents($doc));
        // inserir na BD
        
        $this->db->query("INSERT INTO procuracoes(lead, linha, nomefx, fx64) VALUES(:lead, :linha, :nomefx, :fx64) ",
                array(':lead'=>$param['id'], ':linha'=>$linha[0][0], ':nomefx'=>'proc_'.$param['id'].'_'.$linha[0][0], ':fx64'=>$fx64));
        
    }
}
