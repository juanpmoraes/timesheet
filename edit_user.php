<?php
session_start();
require 'config.php';

// Verificar se o usuário é um administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.html");
    exit;
}

// Verificar se o parâmetro de ID foi passado
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Consultar os dados do usuário
    $stmt = $conn->prepare("SELECT id, username, email, is_admin, password_hash, hourly_rate FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o usuário existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuário não encontrado!";
        exit;
    }

    $stmt->close();
} else {
    echo "ID do usuário não fornecido!";
    exit;
}

// Verificar se o formulário foi enviado para atualizar o usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];      // Nome de usuário
    $email = $_POST['email'];            // E-mail
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;  // Se for admin, 1; caso contrário, 0
    $password = $_POST['password'];      // Nova senha (se fornecida)
    $hourly_rate = $_POST['hourly_rate']; // Valor por hora

    // Se a senha foi fornecida, a hash será atualizada
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Atualizar o usuário com a nova senha e o valor por hora
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password_hash = ?, is_admin = ?, hourly_rate = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $username, $email, $hashed_password, $is_admin, $hourly_rate, $user_id);
    } else {
        // Caso a senha não tenha sido fornecida, atualizamos apenas o nome de usuário, email, status de administrador e valor por hora
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ?, hourly_rate = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $username, $email, $is_admin, $hourly_rate, $user_id);
    }

    if ($stmt->execute()) {
        echo "Usuário atualizado com sucesso!";
        header("Location: user_list.php"); // Redireciona para a lista de usuários
        exit;
    } else {
        echo "Erro ao atualizar o usuário!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Usuário</h1>
        <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
            <label for="username">Nome de Usuário:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

            <label for="email">E-mail:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

            <label for="hourly_rate">Valor por Hora:</label>
            <input type="number" name="hourly_rate" value="<?php echo $user['hourly_rate']; ?>" step="0.01" required><br>

            <label for="is_admin">Administrador:</label>
            <input type="checkbox" name="is_admin" <?php echo $user['is_admin'] == 1 ? 'checked' : ''; ?>><br>

            <label for="password">Nova Senha (deixe em branco para não alterar):</label>
            <input type="password" name="password"><br>

            <input type="submit" value="Atualizar Usuário">
        </form>
        <br>
        <a href="user_list.php" class="btn">Voltar para a lista de usuários</a>
        <a href="logout.php" class="btn-logout">Sair</a>
    </div>
</body>
</html>
