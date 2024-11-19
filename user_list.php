<?php
session_start();
require 'config.php';

// Verificar se o usuário é um administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Consultar todos os usuários do banco de dados
$stmt = $conn->prepare("SELECT id, username, email, is_admin FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Lista de Usuários</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome de Usuário</th>
                        <th>E-mail</th>
                        <th>Administrador</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['is_admin'] == 1 ? 'Sim' : 'Não'; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>">Editar</a> |
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Deseja realmente excluir este usuário?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Não há usuários cadastrados.</p>
        <?php endif; ?>
        <a href="add_user.php" class="btn">Adicionar Usuário</a>
        <a href="logout.php" class="btn-logout">Sair</a>
    </div>
</body>
</html>

<?php
$stmt->close();
?>
