<?php
$dsn = 'mysql:dbname=mendela;host=127.0.0.1:3307';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd połączenia: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Kluczowa poprawka: upewnij się, że klucze istnieją (zmieniłem currentEditedPoint na id poniżej w JS)
    if (isset($input['temperature']) && isset($input['dayNumber'])) {
        updateTemperature($input, $dbh);
    } else if (isset($input['illness'])&& isset($input['dayNumber']) && $input['illness']==true) {
        updateIllness($input, $dbh);
    } else if (isset($input['noMeasurement']) && isset($input['dayNumber']) && $input['noMeasurement']==true) {
        updateNoMeasurement($input, $dbh);
    } else {
        echo json_encode(['success' => false, 'error' => 'Brakujące dane (id lub temperatura)']);
        exit;
    }
}
function updateIllness($input, $dbh){
            $check = $dbh->prepare("SELECT day_number FROM temperatures WHERE day_number = :dayNumber AND id=:id"); // Zakładam tabelę temperatures
            $check->execute(['dayNumber' => $input['dayNumber'], 'id' => 1]);

            if ($check->rowCount() > 0) {
                // UPDATE
                $sth = $dbh->prepare("UPDATE temperatures SET temperature = :temperature WHERE day_number = :dayNumber AND id = :id");
            } else {
                // INSERT
                $sth = $dbh->prepare("INSERT INTO temperatures (day_number, id, temperature) VALUES (:dayNumber, :id, :temperature)");
            }

            $sth->bindValue(':id', 1, PDO::PARAM_INT);
            $sth->bindValue(':temperature', -1, PDO::PARAM_STR);
            $sth->bindValue(':dayNumber', $input['dayNumber'], PDO::PARAM_INT);
            
            if ($sth->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Błąd zapisu w bazie']);
            }    
            exit;
}

function updateNoMeasurement($input, $dbh){
            $sth = $dbh->prepare("DELETE FROM temperatures WHERE day_number = :dayNumber AND id=:id");
            if ($sth->execute(['dayNumber' => $input['dayNumber'], 'id' => 1])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Błąd zapisu w bazie']);
            }    
}

function updateTemperature($input, $dbh) {
 $temp = (float)$input['temperature'];
        
        if ($temp >= 36 && $temp <= 37.2) {
            // Sprawdzamy czy istnieje (używamy konsekwentnie $dbh)
            $check = $dbh->prepare("SELECT day_number FROM temperatures WHERE day_number = :dayNumber AND id=:id"); // Zakładam tabelę temperatures
            $check->execute(['dayNumber' => $input['dayNumber'], 'id' => 1]);

            if ($check->rowCount() > 0) {
                // UPDATE
                $sth = $dbh->prepare("UPDATE temperatures SET temperature = :temperature WHERE day_number = :dayNumber AND id = :id");
            } else {
                // INSERT
                $sth = $dbh->prepare("INSERT INTO temperatures (day_number, id, temperature) VALUES (:dayNumber, :id, :temperature)");
            }

            $sth->bindValue(':id', 1, PDO::PARAM_INT);
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
}
