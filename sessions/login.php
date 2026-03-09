<?php
require 'session_config.php';

// ... (tutaj Twoja logika sprawdzania hasła w bazie danych)

if ($password_is_correct) {
    // Zapobiegamy przejęciu sesji przez odświeżenie ID po zalogowaniu
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['last_activity'] = time();
    
    header("Location: dashboard.php");
    exit;
}

?>