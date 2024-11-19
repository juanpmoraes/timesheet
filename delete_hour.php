<?php
session_start();
require 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o parâmetro de ID foi passado
if (isset($_GET['id'])) {
    $hour_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Verificar se a hora lançada pertence ao usuário atual
    $stmt = $conn->prepare("SELECT user_id FROM hours WHERE id = ?");
    $stmt->bind_param("i", $hour_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Se o usuário logado for o mesmo que lançou a hora, excluir
        if ($row['user_id'] == $user_id) {
            // Deletar a hora lançada
            $stmt = $conn->prepare("DELETE FROM hours WHERE id = ?");
            $stmt->bind_param("i", $hour_id);

            if ($stmt->execute()) {
                echo "Registro de hora excluído com sucesso!";
                header("Location: view_hours.php"); // Redirecionar de volta para a página de visualização
                exit;
            } else {
                echo "Erro ao excluir o registro de hora.";
            }
        } else {
            echo "Você não tem permissão para excluir este registro.";
        }
    } else {
        echo "Registro de hora não encontrado.";
    }

    $stmt->close();
} else {
    echo "ID do registro de hora não fornecido!";
}
?>
