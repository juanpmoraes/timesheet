<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require 'config.php';

$is_admin = $_SESSION['is_admin'];

if ($is_admin) {
    // Exibir página do admin
    include 'admin_dashboard.php';
} else {
    // Exibir página do usuário
    include 'user_dashboard.php';
}
?>
