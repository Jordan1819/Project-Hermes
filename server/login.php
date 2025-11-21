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


// Query the db
$stmt = $db->prepare("SELECT id, password_hash FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($id, $pwHash);
// If query successful - verify password
if ($stmt->fetch()) {
    if (password_verify($password, $pwHash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        echo json_encode(['ok'=>true, 'user_id'=>$id, 'username'=>$username]);
    } else {
        echo json_encode(['ok'=>false, 'error'=>'Invalid username or password']);
    }
}
$stmt->close();
$db->close();