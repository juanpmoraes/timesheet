<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Verifica se o ID da hora foi fornecido na URL
if (!isset($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$hour_id = $_GET['id'];

// Consultar as informações da hora
$stmt = $conn->prepare(
    "SELECT id, date, entry_time, lunch_start, lunch_end, exit_time, description 
     FROM hours 
     WHERE user_id = ? AND id = ?"
);
$stmt->bind_param("ii", $user_id, $hour_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a hora foi encontrada
if ($result->num_rows == 0) {
    echo "Registro não encontrado!";
    exit;
}

$hour = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $date = $_POST['date'];
    $entry_time = $_POST['entry_time'];
    $lunch_start = $_POST['lunch_start'];
    $lunch_end = $_POST['lunch_end'];
    $exit_time = $_POST['exit_time'];
    $description = $_POST['description'];

    // Atualiza as informações no banco de dados
    $stmt = $conn->prepare(
        "UPDATE hours 
         SET date = ?, entry_time = ?, lunch_start = ?, lunch_end = ?, exit_time = ?, description = ? 
         WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ssssssii", $date, $entry_time, $lunch_start, $lunch_end, $exit_time, $description, $hour_id, $user_id);

    if ($stmt->execute()) {
        echo "Horas atualizadas com sucesso!";
        header("Location: view_hours.php");  // Redireciona para a página de visualização de horas
        exit;
    } else {
        echo "Erro ao atualizar as horas!";
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horas Lançadas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Hora Lançada</h1>
        <form method="POST" action="edit_hour.php?id=<?php echo $hour['id']; ?>">
            <label for="date">Data:</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($hour['date']); ?>" required><br>

            <label for="entry_time">Entrada:</label>
            <input type="time" name="entry_time" value="<?php echo htmlspecialchars($hour['entry_time']); ?>" required><br>

            <label for="lunch_start">Início Almoço:</label>
            <input type="time" name="lunch_start" value="<?php echo htmlspecialchars($hour['lunch_start']); ?>"><br>

            <label for="lunch_end">Fim Almoço:</label>
            <input type="time" name="lunch_end" value="<?php echo htmlspecialchars($hour['lunch_end']); ?>"><br>

            <label for="exit_time">Saída:</label>
            <input type="time" name="exit_time" value="<?php echo htmlspecialchars($hour['exit_time']); ?>" required><br>

            <label for="description">Descrição:</label>
            <textarea name="description"><?php echo htmlspecialchars($hour['description']); ?></textarea><br>

            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="view_hours.php" class="btn">Voltar à Visualização de Horas</a>
    </div>
</body>
</html>
