<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Variáveis para as datas do filtro
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Construir a consulta com base nos filtros de data
$query = "SELECT id, date, entry_time, lunch_start, lunch_end, exit_time, description 
          FROM hours 
          WHERE user_id = ?";

// Adicionar filtro de data, se necessário
if ($start_date && $end_date) {
    $query .= " AND date BETWEEN ? AND ?";
}

$query .= " ORDER BY date DESC";

// Preparar e executar a consulta
$stmt = $conn->prepare($query);
if ($start_date && $end_date) {
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);  // Para filtrar por data
} else {
    $stmt->bind_param("i", $user_id);  // Sem filtro de data
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Horas Lançadas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Minhas Horas Lançadas</h1>

        <!-- Formulário de filtro por data -->
        <form method="POST" action="view_hours.php">
            <label for="start_date">Data Início:</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>"><br>

            <label for="end_date">Data Fim:</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>"><br>

            <button type="submit">Filtrar</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Entrada</th>
                        <th>Início Almoço</th>
                        <th>Fim Almoço</th>
                        <th>Saída</th>
                        <th>Descrição</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['entry_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['lunch_start']); ?></td>
                            <td><?php echo htmlspecialchars($row['lunch_end']); ?></td>
                            <td><?php echo htmlspecialchars($row['exit_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="edit_hour.php?id=<?php echo $row['id']; ?>">Editar</a> | 
                                <a href="delete_hour.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Deseja realmente excluir este registro?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Você ainda não lançou horas.</p>
        <?php endif; ?>

        <form action="export_hours.php" method="POST">
            <a href="user_dashboard.php" class="btn">Adicionar Horas</a>
            <button type="submit">Exportar para Excel</button>
        </form>
    </div>
    <br>
    <!-- Botão de Logout -->
<a href="logout.php" class="btn-logout">Sair</a>

</body>
</html>

<?php $stmt->close(); ?>
