<?php
echo "<h1>Bem-vindo ao Dashboard</h1>";
?>
<link rel="stylesheet" href="style.css">
<form action="allocate_hours.php" method="POST">
    <h3>Alocar Horários</h3>
    <label for="date">Data:</label>
    <input type="date" name="date" required>
    
    <label for="entry_time">Horário de Entrada:</label>
    <input type="time" name="entry_time" required>
    
    <label for="lunch_start">Início do Almoço:</label>
    <input type="time" name="lunch_start" required>
    
    <label for="lunch_end">Fim do Almoço:</label>
    <input type="time" name="lunch_end" required>
    
    <label for="exit_time">Horário de Saída:</label>
    <input type="time" name="exit_time" required>
    
    <label for="description">Descrição:</label>
    <textarea name="description"></textarea>
    
    <button type="submit">Salvar</button>
</form>

<button><a href="view_hours.php">Ver Horas</a></button>
<br>
<!-- Botão de Logout -->
<a href="logout.php" class="btn-logout">Sair</a>

