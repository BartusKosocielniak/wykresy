<?php
header("Content-Type: application/json");

$dsn = 'mysql:dbname=mendela;host=127.0.0.1:3307';
$user = 'root';
$password = '';




try {
    $pdo = new PDO($dsn, $user, $password);
} catch (\PDOException $e) {
    echo json_encode(["message" => "Błąd połączenia z bazą: " . $e->getMessage()]);
    exit;
}

// 2. Odbieranie danych JSON
// $json = file_get_contents('php://input');
$data = json_decode(file_get_contents('php://input'), true);

$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $data['password'] ?? '';

// 3. Walidacja


//teraz naucz sie regex

$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/';
    
if (!$email || !preg_match($passwordRegex, $password)) {
    echo json_encode(["message" => "Błędny email lub za krótkie hasło (min. 8 znaków, w tym -conajmniej jeden mały znak
	- conajmniej jeden duży znak
	- conajmniej jedna cyfra)."]);
    exit;
}

// 4. Hashowanie hasła
// PASSWORD_DEFAULT automatycznie używa najsilniejszego aktualnie algorytmu (BCrypt)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // 5. Zapis do bazy (Prepared Statements chronią przed SQL Injection)
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    echo json_encode(["message" => "Konto zostało założone pomyślnie!"]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Kod błędu dla Duplicated Entry
        echo json_encode(["message" => "Ten adres email jest już zajęty."]);
    } else {
        echo json_encode(["message" => "Wystąpił błąd serwera."]);
    }
}

?>