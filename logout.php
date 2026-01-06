<?php
session_start();
session_destroy();

// Redirection après déconnexion
header('Location: index.php'); // ou login.php si tu préfères
exit;