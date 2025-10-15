<?php

class ServicioTurnos {
	private $file;

	public function __construct() {
		$this->file = './turno_' . date('Ymd_His') . '.pdf';
	}

    public function getFile() {
        return $this->file;
    }

    // Metodo publico para generar un PDF del Turno
    public function generarTurnoPdf(?string $nombre="", int $caja, string $departamento, int $turno): void {
        $pdf = new FPDF('P', 'mm', array(50,55));
		$pdf->SetMargins(2,2,2);
		$pdf->SetAutoPageBreak(true, 1);
        $pdf->AddPage();
        $pdf->setFont('Arial', 'B', 12);
		$pdf->Cell(0, 3, 'CELERIS', 0, 1, 'C');
		$pdf->Ln();
		$pdf->setFont('Arial', '', 7);

        if (!$nombre) {
            $pdf->Cell(0, 3, 'Nombre: N/A', 0, 1, 'L');
        } else {
            $pdf->Cell(0, 3, 'Nombre: ' . $nombre, 0, 1, 'L');
        }
        
        $pdf->Cell(0, 3, 'Departamento: ' . $departamento, 0, 1, 'L');
        $pdf->Cell(0, 3, 'Caja: ' . $caja, 0, 1, 'L');
		$pdf->Ln();
		$pdf->setFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, 'Numero de Turno: ' . $turno, 1, 1, 'C');
		$pdf->Ln();
		$pdf->setFont('Arial', '', 7);
        $pdf->Cell(0, 3, 'Torreón, Coahuila', 0, 1, 'L');
        $pdf->Cell(0, 3, date('d-m-Y'), 0, 1, 'L');
        $pdf->Cell(0, 3, date('H:i:s'), 0, 1, 'L');
        $pdf->Cell(0, 3, 'Servicios Celeris', 0, 1, 'L');
		
		$pdf->Output('F', $this->file);
    }
}