<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('lib/fpdf186/fpdf.php');

class PDF extends FPDF
{

    public $params;

   function __construct($params) 
   { 
      parent::__construct(); 
      $this->params = $params;
   } 
// Cabecera de página
function Header()
{
    $this->Image('assets/images/logo.png',150,8,33);
    // Arial bold 10
    $this->SetFont('Arial','B',10);
    // Movernos a la derecha
    //$this->Cell(80);

    //http://localhost/equioral_genpdf/?params={%22patient%22:{%22id%22:%2223%22,%22horse%22:%22SULTAN%20DE%20VILLA%20JULIANA%22,%22horse_farm%22:%22AMAZONA%22,%22owner_name%22:%22CRIADERO%20AMAZONA%22,%22owner_phone%22:%223215207463%22,%22status%22:%22active%22,%22createdAt%22:%222024-03-22T14:18:17.204Z%22},%22history%22:{%22id%22:%2223%22,%22patient_id%22:%2223%22,%22first_observation%22:%22Presenta%20aristas%20en%20premolares%20y%20molares,%20incisivos%20con%20desgaste%20por%20habito,%20ausencia%20del%20102%22,%22status%22:%22active%22,%22treatment%22:%22Se%20realiza%20reducci%C3%B3n%20de%20aristas%20en%20premolares%20y%20molares,%20balance%20oclusal,%20reducci%C3%B3n%20de%20canino%20304,%20ajuste%20de%20incisivos,%20se%20recomienda%20Rx%20para%20observar%20presencia%20de%20incisivo%20102,%20se%20recomienda%20tratamiento%20g%C3%A1strico%20%22,%22photos%22:[{%22src%22:%22https://equioral.s3.amazonaws.com/be3039e8-7269-468e-a508-b5e31afa450a%22},{%22src%22:%22https://equioral.s3.amazonaws.com/9efe12fd-c74e-404f-bf00-44fb268836e2%22},{%22src%22:%22https://equioral.s3.amazonaws.com/4105ba09-5156-4b13-b7c3-eebc6b1e1c90%22},{%22src%22:%22https://equioral.s3.amazonaws.com/b47a4029-00c3-4bcc-a79c-31c95e4688d0%22},{%22src%22:%22https://equioral.s3.amazonaws.com/9224247f-224b-4415-b534-e5a8cb0fecd1%22},{%22src%22:%22https://equioral.s3.amazonaws.com/ac9ace71-1747-4da0-9333-93bf06cb5c3c%22}],%22createdAt%22:%222024-03-22T14:40:40.203Z%22,%22share_id%22:%22bab0508b0794%22,%22share_options%22:{%22share_time%22:1711233865382,%22share_expiration%22:1,%22share_password%22:%22%22,%22share_enabled%22:%22enabled%22,%22share_url%22:%22http://localhost:3000/share-history/bab0508b0794%22}}}
    $this->Cell(0,5,utf8_decode('ID Historia: '.$this->params->history->id),0,1);
    $this->Ln(1);// Salto de línea
    $this->Cell(0,5,utf8_decode('Criadero:'.$this->params->patient->horse_farm),0,1);
    $this->Ln(1);// Salto de línea
    $this->Cell(0,5,utf8_decode('Dueño: '.$this->params->patient->owner_name),0,1);
    $this->Ln(1);// Salto de línea
    $this->Cell(0,5,utf8_decode('Caballo: '.$this->params->patient->horse),0,1);
    $this->Ln(1);// Salto de línea
    $this->Cell(0,5,utf8_decode('Teléfono: '.$this->params->patient->owner_phone),0,1);

    $this->Ln(10);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

try{

$params = json_decode($_GET['params']);

// Creación del objeto de la clase heredada
$pdf = new PDF($params);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,10,utf8_decode('Observación Inicial'),0,1);
$pdf->SetFont('Times','',10);
$pdf->Multicell(0,5,utf8_decode($params->history->first_observation),0,1);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,10,utf8_decode('Tratamiento'),0,1);
$pdf->SetFont('Times','',10);
$pdf->Multicell(0,5,utf8_decode($params->history->treatment),0,1);
$x=0;$y=0;$total=4/*count($params->history->photos)*/;
for($i=0; $i < $total; $i++ ){
    $x = ($i % 3 === 0) ? 0 : $x;
    $y = ($i % 3 === 0) ? $y+1 : $y;
    $col = 10 + (40 * $x++);
    $row = 100 + (50 * $y);
    copy($params->history->photos[$i]->src, '/tmp/localimage'.$i.'.jpg');
    $pdf->Image('/tmp/localimage'.$i.'.jpg',$col,$row,33);
    sleep(1);
}

// copy('https://equioral.s3.amazonaws.com/be3039e8-7269-468e-a508-b5e31afa450a', '/tmp/localimage.jpg');
// $pdf->Image('/tmp/localimage.jpg',10,150,33);

// $pdf->Image('/tmp/localimage.jpg',50,150,33);

$pdf->Output();
}catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>