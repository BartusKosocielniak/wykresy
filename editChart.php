<?php
$dsn = 'mysql:dbname=mendela;host=127.0.0.1:3307';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'error' => 'Błąd połączenia: ' . $e->getMessage()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Kluczowa poprawka: upewnij się, że klucze istnieją (zmieniłem currentEditedPoint na id poniżej w JS)
    if (isset($input['temperature']) && isset($input['dayNumber'])) {
        $temp = (float)$input['temperature'];
        
        if ($temp >= 36 && $temp <= 37.2) {
            // Sprawdzamy czy istnieje (używamy konsekwentnie $dbh)
            $check = $dbh->prepare("SELECT day_number FROM temperatures WHERE day_number = :dayNumber AND user_id=:user_id"); // Zakładam tabelę temperatures
            $check->execute(['dayNumber' => $input['dayNumber'], 'user_id' => 1]);

            if ($check->rowCount() > 0) {
                // UPDATE
                $sth = $dbh->prepare("UPDATE temperatures SET temperature = :temperature WHERE day_number = :dayNumber AND user_id = :user_id");
            } else {
                // INSERT
                $sth = $dbh->prepare("INSERT INTO temperatures (day_number, user_id, temperature) VALUES (:dayNumber, :user_id, :temperature)");
            }

            $sth->bindValue(':user_id', 1, PDO::PARAM_INT);
            $sth->bindValue(':temperature', $temp, PDO::PARAM_STR);
            $sth->bindValue(':dayNumber', $input['dayNumber'], PDO::PARAM_INT);
            
            if ($sth->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Błąd zapisu w bazie']);
            }
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Temperatura poza zakresem (36-37.2)']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Brakujące dane (id lub temperatura)']);
        exit;
    }
    
}