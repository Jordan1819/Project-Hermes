<?php

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config.php';

// Validate user
if(empty($_SESSION['user_id'])) {
    echo json_encode(['ok'=>false, 'error'=>'Not authenticated']);
    exit;
}

// Read JSON body
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Accept 'note' or 'text' for backwards compatibility
$note = trim( (string) ($input['note'] ?? $input['text'] ?? '') );

// Basic validation
if ($note == '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Note empty']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Prepare and execute insert
$stmt = $db->prepare("INSERT INTO notes (user_id, note_text) VALUES (?, ?)");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB connection failed: ' . $db->error]);
    exit;
}
$stmt->bind_param('is', $user_id, $note);

if ($stmt->execute()) {
    $insertId = $stmt->insert_id;
    $username = $_SESSION['username'] ?? '';
    echo json_encode([
        'ok' => true,
        'note_id' => $insertId,
        'username' => $username ?? ''
    ]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Insert failed: ' . $stmt->error]);
}
$stmt->close();
$db->close();