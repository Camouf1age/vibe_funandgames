<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$file = __DIR__ . '/scores.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $initials = strtoupper(preg_replace('/[^A-Za-z]/', '', $data['initials'] ?? ''));
    $initials = substr($initials, 0, 3) ?: 'AAA';
    $score = intval($data['score'] ?? 0);

    if ($score <= 0) {
        echo json_encode([]);
        exit;
    }

    $scores = [];
    if (file_exists($file)) {
        $scores = json_decode(file_get_contents($file), true) ?: [];
    }

    $scores[] = ['initials' => $initials, 'score' => $score];
    usort($scores, fn($a, $b) => $b['score'] - $a['score']);
    $scores = array_slice($scores, 0, 10);

    file_put_contents($file, json_encode($scores));
    echo json_encode($scores);
} else {
    if (file_exists($file)) {
        echo file_get_contents($file);
    } else {
        echo json_encode([]);
    }
}
