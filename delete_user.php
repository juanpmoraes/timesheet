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

    // Verificar se o usuário a ser excluído não é o próprio administrador logado
    if ($user_id == $_SESSION['user_id']) {
        echo "Você não pode excluir a si mesmo!";
        exit;
    }

    // Iniciar a transação para garantir a integridade referencial
    $conn->begin_transaction();

    try {
        // Excluir as horas relacionadas ao usuário
        $stmt_hours = $conn->prepare("DELETE FROM hours WHERE user_id = ?");
        $stmt_hours->bind_param("i", $user_id);
        $stmt_hours->execute();

        // Excluir o usuário
        $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();

        // Se ambas as operações forem bem-sucedidas, confirmar a transação
        $conn->commit();
        
        echo "Usuário excluído com sucesso!";
    } catch (Exception $e) {
        // Se algo falhar, desfazer a transação
        $conn->rollback();
        echo "Erro ao excluir o usuário: " . $e->getMessage();
    }

    $stmt_hours->close();
    $stmt_user->close();
} else {
    echo "ID do usuário não fornecido!";
}

// Redirecionar para a lista de usuários
header("Location: user_list.php");
exit;
?>
