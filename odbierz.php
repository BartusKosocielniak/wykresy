<?php
header('Content-Type: application/json; charset=utf-8'); // odpowiedź JSON

// Odbiór danych z POST
$imie = isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : '';
$haslo = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

try {
    // Dane do połączenia
    $dsn = 'mysql:host=sql103.infinityfree.com;dbname=if0_39956528_char;charset=utf8';
    $user = 'if0_39956528';
    $password = 'W8tdnN9uOeu';

    // Połączenie z bazą
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // rzucaj wyjątki przy błędach
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Przygotowanie zapytania
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");

    // Wykonanie zapytania z danymi
    $stmt->execute([
        ':email' => $imie,
        ':password' => $haslo
    ]);

    // Odpowiedź JSON
    echo json_encode([
        'status' => 'ok',
        'wiadomosc' => "✅ Użytkownik $imie został dodany!"
    ]);

} catch (PDOException $e) {
    // Obsługa błędu połączenia lub zapytania
    echo json_encode([
        'status' => 'error',
        'wiadomosc' => '❌ Błąd bazy danych: ' . $e->getMessage()
    ]);
}
?>
