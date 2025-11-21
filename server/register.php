<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

// Basic input validation
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

// Username length check
if (strlen($username) > 50) {
    echo json_encode(['success' => false, 'message' => 'Username exceeds maximum length of 50 characters.']);
    exit;
}

// Hash password
$pwHash = password_hash($password, PASSWORD_DEFAULT);

// Insert user into 'users' table
$stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->bind_param('ss', $username, $pwHash);
if ($stmt->execute()) {
    $userId = $stmt->insert_id;
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    echo json_encode(['ok'=>true, 'user_id'=>$userId, 'username'=>$username]);
} else {
    if ($db->errno === 1062) {
        echo json_encode(['ok'=>false, 'error'=>'Username already exists.']);
    } else {
        echo json_encode(['ok'=>false, 'error'=>'Insert failed: ' . $db->error]);
    }
}
$stmt->close();
$db->close();

