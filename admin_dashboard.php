<?php
echo "<h1>Bem-vindo, Administrador</h1>";
?>
<!-- Formulário para adicionar usuário -->
<link rel="stylesheet" href="style.css">
<form method="POST" action="add_user.php">
    <label for="username">Nome de Usuário:</label>
    <input type="text" name="username" required><br>

    <label for="email">E-mail:</label>
    <input type="email" name="email" required><br>

    <label for="password">Senha:</label>
    <input type="password" name="password" required><br>

    <label for="hourly_rate">Valor por Hora:</label>
    <input type="number" name="hourly_rate" step="0.01" required><br>

    <label for="is_admin">Administrador:</label>
    <input type="checkbox" name="is_admin"><br>

    <input type="submit" value="Adicionar Usuário">
    <h1></h1>
    <a href="user_list.php">Lista de Usuários</a>
</form>
<br>
<!-- Botão de Logout -->
<a href="logout.php" class="btn-logout">Sair</a>


