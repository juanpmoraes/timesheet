<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $entry_time = $_POST['entry_time'];
    $lunch_start = $_POST['lunch_start'];
    $lunch_end = $_POST['lunch_end'];
    $exit_time = $_POST['exit_time'];
    $description = $_POST['description'];

    $stmt = $conn->prepare(
        "INSERT INTO hours (user_id, date, entry_time, lunch_start, lunch_end, exit_time, description) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issssss", $user_id, $date, $entry_time, $lunch_start, $lunch_end, $exit_time, $description);
    
    if ($stmt->execute()) {
        echo "Horas registradas com sucesso!";
    } else {
        echo "Erro ao registrar horas.";
    }
    $stmt->close();
    header("Location: view_hours.php");
}
?>
