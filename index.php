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

    if(!isset($_GET['params'])) exit;

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
$x=0;$y=0;$total=count($params->history->photos);
$photosPerRow = isset($params->photosPerRow) ? $params->photosPerRow : 3;
for($i=0; $i < $total; $i++ ){
    $x = ($i % $photosPerRow === 0) ? 0 : $x;
    $y = ($i % $photosPerRow === 0) ? $y+1 : $y;
    $col = 10 + (40 * $x++);
    $row = 70 + (50 * $y);
    copy($params->history->photos[$i]->src, '/tmp/localimage'.$i.'.jpg');
    $pdf->Image('/tmp/localimage'.$i.'.jpg',$col,$row,33);
    usleep(500000);
}

$pdf->Output('I', 'historia');
}catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>