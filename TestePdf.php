<?php

require('./fpdf186/fpdf.php');

class PDF extends FPDF {
    

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        // Decode the text as UTF-8
        $txt = utf8_decode($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    // Cabeçalho
    function Header() 
    {
        global $title;

        $this->Image('jurassic.png',15,5,30); // cria a imagem
        $this->SetFont('Arial', 'U', 16); // define a fonte e o tamanho e coloca com sublinhado 'U'
        $w = $this->GetStringWidth($title) + 6;
        $this->SetX((210 - $w) / 2); // Centralize o título na página A4 (210 mm de largura)
        // Cores das bordas, fundo e texto
        $this->SetDrawColor(220, 50, 50); 
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(220,50,50); 
        // Largura da borda (1 mm)
        $this->SetLineWidth(1);
        // Título
        $this->Cell($w,9,$title,1,1,'C',true);
        // Salto de linha
        $this->Ln(10);
    }

    // Corpo do PDF
    function ChapterTitle($num, $label) 
    {
        $this->SetFont('Arial','B',12);
        // Adicione o título corretamente
        $this->SetFillColor(200,220,255);
        $this->Ln(8);
        // Title
        $this->Cell(0,6,"Capítulo $num : $label",0,1,'L',true);
        // Line break
        $this->Ln(8);

    }

    function ChapterBody($file) 
    {
        $txt = file_get_contents($file);
        // Times 12
        $this->SetFont('Times','',12);
        // Imprimimos o texto justificado
        $this->MultiCell(0,5,$txt);
        // Salto de linha
        $this->Ln();
        // Citação em itálico
        $this->SetFont('','I');
        $this->Cell(0,5,'(fim do trecho)');
    }

    // Rodapé
    function Footer() 
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function PrintChapter($num, $title, $file)
    {
        $this->AddPage();
        $this->ChapterTitle($num,$title);
        $this->ChapterBody($file);
    }

}

$pdf = new PDF();
$title = "Historia de um Dinossauro";
$pdf->SetTitle($title);
$pdf->SetAuthor('ChatGPT');
$pdf->PrintChapter(1,'Dinossauro Brasil', "historiaDino.txt");
$pdf->PrintChapter(2,'Dinossauro Alemão', "historiaDois.txt");

$pdf->Output('hello.pdf', 'F');




