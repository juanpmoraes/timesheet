<?php
require 'config.php';
require 'vendor/autoload.php'; // Inclua o autoload do PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;  // Certifique-se de incluir o namespace de Bordas

session_start();

$user_id = $_SESSION['user_id'];

// Consultar o nome do usuário
$stmt_name = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt_name->bind_param("i", $user_id);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$username = $result_name->fetch_assoc()['username'];
$stmt_name->close();

// Consultar o valor de ganho por hora (isso pode ser ajustado para refletir o valor no banco de dados)
$stmt_rate = $conn->prepare("SELECT hourly_rate FROM users WHERE id = ?");
$stmt_rate->bind_param("i", $user_id);
$stmt_rate->execute();
$result_rate = $stmt_rate->get_result();
$hourly_rate = $result_rate->fetch_assoc()['hourly_rate']; // Supondo que o valor de hourly_rate esteja na tabela users
$stmt_rate->close();

// Variáveis para os filtros de data
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Construir a consulta com base nos filtros de data
$query = "SELECT date, entry_time, lunch_start, lunch_end, exit_time, description 
          FROM hours WHERE user_id = ?";

// Adicionar filtro de data, se necessário
if ($start_date && $end_date) {
    $query .= " AND date BETWEEN ? AND ?";
}

$query .= " ORDER BY date";

// Preparar e executar a consulta
$stmt = $conn->prepare($query);
if ($start_date && $end_date) {
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);  // Para filtrar por data
} else {
    $stmt->bind_param("i", $user_id);  // Sem filtro de data
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Criar a planilha
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Definir cabeçalhos
    $sheet->setCellValue('A1', 'Relatório de Horas Trabalhadas - Petla DBA');
    $sheet->mergeCells('A1:H1'); // Mescla as células para o título
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    
    // Cabeçalhos das colunas
    $sheet->setCellValue('A2', 'Data');
    $sheet->setCellValue('B2', 'Entrada');
    $sheet->setCellValue('C2', 'Início do Almoço');
    $sheet->setCellValue('D2', 'Fim do Almoço');
    $sheet->setCellValue('E2', 'Saída');
    $sheet->setCellValue('F2', 'Descrição');
    $sheet->setCellValue('G2', 'Horas Trabalhadas');
    $sheet->setCellValue('H2', 'Valor Total');
    $sheet->setCellValue('I2', 'Valor por Hora');

    // Estilizar cabeçalhos
    $sheet->getStyle('A2:I2')->getFont()->setBold(true);
    $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:I2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Adicionar dados e cálculos
    $row = 3; // Começa na linha 3
    $total_hours = 0;
    while ($data = $result->fetch_assoc()) {
        // Calcular as horas trabalhadas
        $entry_time = new DateTime($data['entry_time']);
        $exit_time = new DateTime($data['exit_time']);
        $lunch_start = new DateTime($data['lunch_start']);
        $lunch_end = new DateTime($data['lunch_end']);

        // Subtrair o intervalo de almoço da diferença entre a entrada e saída
        $worked_hours = $entry_time->diff($exit_time);
        $lunch_break = $lunch_start->diff($lunch_end);

        // Converter a diferença para horas decimais
        $worked_hours_decimal = $worked_hours->h + ($worked_hours->i / 60);
        $lunch_break_decimal = $lunch_break->h + ($lunch_break->i / 60);

        // Horas trabalhadas após subtrair o intervalo de almoço
        $total_worked = $worked_hours_decimal - $lunch_break_decimal;
        $total_hours += $total_worked;

        // Definir valores na planilha
        $sheet->setCellValue('A' . $row, $data['date']);
        $sheet->setCellValue('B' . $row, $data['entry_time']);
        $sheet->setCellValue('C' . $row, $data['lunch_start']);
        $sheet->setCellValue('D' . $row, $data['lunch_end']);
        $sheet->setCellValue('E' . $row, $data['exit_time']);
        $sheet->setCellValue('F' . $row, $data['description']);
        $sheet->setCellValue('G' . $row, $total_worked); // Horas Trabalhadas
        $sheet->setCellValue('H' . $row, $total_worked * $hourly_rate); // Valor Total

        // Fórmula para calcular o valor por hora
        $sheet->setCellValue('I' . $row, "=H$row / G$row"); // Fórmula corrigida
        
        $row++;
    }

    // Adicionar total geral
    $sheet->setCellValue('G' . $row, $total_hours); // Total de horas
    $sheet->setCellValue('H' . $row, $total_hours * $hourly_rate); // Valor total

    // Exibir o nome do usuário
    $sheet->setCellValue('A' . ($row + 2), 'Funcionário: ' . $username);
    $sheet->mergeCells('A' . ($row + 2) . ':I' . ($row + 2));
    $sheet->getStyle('A' . ($row + 2))->getFont()->setBold(true);
    
    // Ajustar largura das colunas
    foreach (range('A', 'I') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Definir o nome do arquivo
    $filename = 'Horas_' . date('Y-m-d') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Salvar o arquivo
    $writer->save($filename);

    // Enviar o arquivo para download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    readfile($filename);

    // Excluir o arquivo temporário
    unlink($filename);
} else {
    echo "Nenhum registro encontrado.";
}

$stmt->close();
?>
