<?php
require('./fpdf186/fpdf.php');


iconv_set_encoding("internal_encoding", "UTF-8");
iconv_set_encoding("output_encoding", "UTF-8");

// Cria uma classe PDF que estende a classe FPDF do pacote fpdf186
class PDF extends FPDF {

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        // Decode the text as UTF-8
        $txt = utf8_decode($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }


    function Header() {
        // Cabeçalho do PDF
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(60);
        $this->Cell(60, 10, 'Relatório de Médicos', 1, 1, 'C');
    }

    function Footer() {
        // Rodapé do PDF
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Página '. $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($title) {
        // Título do capítulo
        $this->SetFont('Arial', 'BU', 12);
        $this->Cell(0, 6, $title, 0, 1);
    }

    function ChapterBody($data) {
        // Corpo do capítulo (dados dos médicos)
        foreach ($data as $row) {
           
            // largura 0 , altura 5 / borda 1 , nova linha 1
            $this->SetFont('Arial', 'BU', 12);
            $this->Cell(0, 5, 'ID: ' . $row['id'], 1, 1);
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 5, 'NOME: ' . $row['nome'], 1, 1);
            $this->Cell(0, 5,'ENDEREÇO: '. $row['endereco'], 1, 1);
            $this->Ln(10); // Espaço entre os registros
        }
    }
}

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "laravel";

// Cria uma conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi estabelecida com sucesso
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta SQL para recuperar os dados da tabela "clientes"
$sql = "SELECT id, nome, endereco FROM clientes";

// Executa a consulta
$result = $conn->query($sql);

// Verifica se a consulta retornou resultados
if ($result->num_rows > 0) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }


    // Cria uma instância da classe PDF
    $pdf = new PDF();

    // Adiciona uma página ao PDF
    $pdf->AddPage();

    // Adiciona uma imagem ao PDF (logotipo)
    $pdf->Image('logo_grupo_setev.png', 5, 5, 40, 0);

    // Adiciona espaço vertical
    $pdf->Ln(15);

    // Adiciona o título do capítulo
    $pdf->ChapterTitle('Dados dos Médicos');
    
     // Adiciona espaço vertical
     $pdf->Ln(15);

    // Adiciona o corpo do capítulo com os dados dos médicos
    $pdf->ChapterBody($data);

    // Gera o PDF e o salva como 'hello.pdf'
    $pdf->Output('hello.pdf', 'F');
} else {
    echo "Nenhum registro encontrado na tabela 'clientes'.";
}

// Fecha a conexão com o banco de dados
$conn->close();
