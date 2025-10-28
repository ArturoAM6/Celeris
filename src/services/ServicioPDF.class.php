<?php

class ServicioPDF {
    // Metodo publico para generar un PDF del Turno
    public static function generarTurnoPdf(int $caja, string $departamento, int $turno, ?string $nombre=""): void {
        $pdf = new tFPDF('P', 'mm', array(50,55));
		$pdf->SetMargins(2,2,2);
		$pdf->SetAutoPageBreak(true, 1);
        $pdf->AddPage();
        $pdf->addFont('DejaVu', '', 'DejaVuSansCondensed.ttf',true);
        $pdf->Image(__DIR__ . '/../../public/img/logo_celeris.png', 20, 2, 10);
        $pdf->Ln(12);
        $pdf->setFont('DejaVu', '', 12);
		$pdf->Cell(0, 3, 'CELERIS', 0, 1, 'C');
		$pdf->Ln();
		$pdf->setFont('DejaVu', '', 7);

        if (!$nombre) {
            $pdf->Cell(0, 3, 'Nombre: N/A', 0, 1, 'L');
        } else {
            $pdf->Cell(0, 3, 'Nombre: ' . $nombre, 0, 1, 'L');
        }
        
        $pdf->Cell(0, 3, 'Departamento: ' . $departamento, 0, 1, 'L');
        $pdf->Cell(0, 3, 'Caja: ' . $caja, 0, 1, 'L');
		$pdf->Ln();
		$pdf->setFont('DejaVu', '', 8);
        $pdf->Cell(0, 5, 'Numero de Turno: ' . $turno, 1, 1, 'C');
		$pdf->Ln();
		$pdf->setFont('DejaVu', '', 7);
        $pdf->Cell(0, 3, 'Torreón, Coahuila', 0, 1, 'L');
        $pdf->Cell(0, 3, date('d-m-Y'), 0, 1, 'L');
        $pdf->Cell(0, 3, date('H:i:s'), 0, 1, 'L');
        $pdf->Cell(0, 3, 'Servicios Celeris', 0, 1, 'L');
		
		$pdf->Output('I', 'ticket.pdf');
    }

    public static function generarTurnoPdfString(int $caja, string $departamento, int $turno, ?string $nombre=""): string {
        $pdf = new FPDF('P', 'mm', array(50,55));
        $pdf->SetMargins(2,2,2);
        $pdf->SetAutoPageBreak(true, 1);
        $pdf->AddPage();
        $pdf->addFont('DejaVu', '', 'DejaVuSansCondensed.ttf',true);
        $pdf->Image(__DIR__ . '/../../public/img/logo_celeris.png', 20, 2, 10);
        $pdf->Ln(12);
        $pdf->setFont('DejaVu', '', 12);
        $pdf->Cell(0, 3, 'CELERIS', 0, 1, 'C');
        $pdf->Ln();
        $pdf->setFont('DejaVu', '', 7);

        if (!$nombre) {
            $pdf->Cell(0, 3, 'Nombre: N/A', 0, 1, 'L');
        } else {
            $pdf->Cell(0, 3, 'Nombre: ' . $nombre, 0, 1, 'L');
        }
        
        $pdf->Cell(0, 3, 'Departamento: ' . $departamento, 0, 1, 'L');
        $pdf->Cell(0, 3, 'Caja: ' . $caja, 0, 1, 'L');
        $pdf->Ln();
        $pdf->setFont('DejaVu', '', 8);
        $pdf->Cell(0, 5, 'Numero de Turno: ' . $turno, 1, 1, 'C');
        $pdf->Ln();
        $pdf->setFont('DejaVu', '', 7);
        $pdf->Cell(0, 3, 'Torreón, Coahuila', 0, 1, 'L');
        $pdf->Cell(0, 3, date('d-m-Y'), 0, 1, 'L');
        $pdf->Cell(0, 3, date('H:i:s'), 0, 1, 'L');
        $pdf->Cell(0, 3, 'Servicios Celeris', 0, 1, 'L');
        
       return $pdf->Output('S');
    }
   
}