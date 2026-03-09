<?php
require 'session_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Opcjonalnie: automatyczne wylogowanie po 30 minutach bezczynności
if (time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php?reason=timeout");
    exit;
}
$_SESSION['last_activity'] = time();

?>