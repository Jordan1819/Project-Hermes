<?php

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config.php';

// small helper to return JSON and exit - default 200 ok
function json_exit($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

// Auth: require logged-in user
if (empty($_SESSION['user_id'])) {
    json_exit(['ok' => false, 'error' => 'Not authenticated'], 401);
}

// Read and parse request body
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
$q = trim((string) ($input['q'] ?? ''));

if ($q === '') {
    json_exit(['ok' => true, 'notes' => [], 'total' => 0]);
}

// split query into words(terms) by spaces, drop short terms, and store in terms[]
$parts = preg_split('/\s+/', $q);
$terms = [];
foreach ($parts as $p) {
    $p = trim($p);
    if ($p === '' || mb_strlen($p) < 2) continue; // Ignore single character tokens
    $terms[] = $p;
}

if (count($terms) === 0) {
    json_exit(['ok' => false, 'error' => 'Search must have two or more letters'], 400);
}

// collect individual LIKE expressions (one per search term)
$whereParts = [];
// collect values that will be bound to ? placeholders
$params = [];
// strong to describe parameter types 'sss'
$types = '';
foreach ($terms as $t) {
    $whereParts[] = "LOWER(n.note_text) LIKE ?";
    $params[] = '%' . mb_strtolower($t) . '%';
    $types .= 's';
}
// Combine all whereParts with OR
$whereSQL = '(' . implode(' OR ', $whereParts). ')';

$limit = 100;
// construct sql query - return 100 results max
$selectSQL = "SELECT n.id, n.note_text, n.created_at, u.username
              FROM notes n
              JOIN users u ON n.user_id = u.id
              WHERE $whereSQL
              ORDER BY n.created_at DESC
              LIMIT ?";

// prepare db statement
$stmt = $db->prepare($selectSQL);
if ($stmt === false) {
    json_exit(['ok' => false, 'error' => 'DB prepare failed: ' . $db->error], 500);
}

// append limit to params
$params[] = $limit;
// append 'i' to types because limit is an integer
$types .= 'i';
// build array of references for call_user_func_array
$bind_names = [];
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    // call_user_func_array passes array elements as values unless they're references
    // and bind_param() expects variables passed by reference
    $bind_names[] = &$params[$i];
}
// bind parameters dynamically
call_user_func_array([$stmt, 'bind_param'], $bind_names);

if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    json_exit(['ok' => false, 'error' => 'DB execute failed: ' . $err], 500);
}

$res = $stmt->get_result();
if ($res === false) {
    $err = $stmt->error;
    $stmt->close();
    json_exit(['ok' => false, 'error' => 'Getting results failed: ' . $err], 500);
}

$rows = $res->fetch_all(MYSQLI_ASSOC);
$total = count($rows);
$stmt->close();
// Return results set in JSON payload
json_exit(['ok' => true, 'notes' => $rows, 'total' => $total]);