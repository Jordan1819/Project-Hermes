<?php

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config.php';

// Validate user
if(empty($_SESSION['user_id'])) {
    echo json_encode(['ok'=>false, 'error'=>'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$note = $input['note'] ?? '';

// Check for empty note submission
if($note === '') {
    echo json_encode(['ok'=>false, 'error'=>'Note empty']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$stmt = $db->prepare("INSERT INTO notes (user_id, note_text) VALUES (?, ?)");
$stmt->bind_param('is', $user_id, $note);

if($stmt->execute()) {
    echo json_encode(['ok'=>true, 'note_id'=>$stmt->insert_id]);
} else {
    echo json_encode(['ok'=>false, 'error'=>'Insert failed: '.$db->error]);
}
$stmt->close();
$db->close();