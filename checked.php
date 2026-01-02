<?php
// Plik do zapisu i odczytu sprawdzonych imion
$file = __DIR__ . '/checked.txt'; // jawna ścieżka w tym samym folderze

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');

    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Empty name']);
        exit;
    }

    // Wczytaj istniejące
    $lines = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    // Dodaj jeśli nie ma
    if (!in_array($name, $lines)) {
        $result = file_put_contents($file, $name . PHP_EOL, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Cannot write to file - check permissions']);
            exit;
        }
        $lines[] = $name;
    }

    echo json_encode(['success' => true, 'names' => array_values(array_unique($lines))]);

} else { // GET
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        echo json_encode(array_values(array_unique($lines)));
    } else {
        echo json_encode([]);
    }
}
?>