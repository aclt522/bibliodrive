<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['profil']) || $_SESSION['profil'] !== 'admin') {
    header('Location: index.php');
    exit();
}
