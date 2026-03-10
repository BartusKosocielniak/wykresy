<?php
require 'session_config.php';

$hash = '$2y$12$4Umg0rCJwMswRw/l.SwHvuQV01coP0eWmGzd61QH2RvAOMANUBGC.';

// ... (tutaj Twoja logika sprawdzania hasła w bazie danych)
//todo
//dorob zeby byl POST oraz dodaj pierwszy mechanizm sesji do mod.php dalej to zrob jak było na kartce o ile jest :))))
try {
    // 5. Zapis do bazy (Prepared Statements chronią przed SQL Injection)
    $stmt = $pdo->prepare("SELECT email, password FROM users WHERE email LIKE ? AND password LIKE ?");
    $stmt->execute([$email, password_verify($password, PASSWORD_DEFAULT)]);

    echo json_encode(["message" => "Konto zostało założone pomyślnie!"]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Kod błędu dla Duplicated Entry
        echo json_encode(["message" => "Ten adres email jest już zajęty."]);
    } else {
        echo json_encode(["message" => "Wystąpił błąd serwera."]);
    }
}


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