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

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
$q = trim((string) ($input['q'] ?? ''));

if ($q === '') {
    json_exit(['ok' => true, 'notes' => [], 'total' => 0]);
}

// split query into words & drop short tokens
$parts = preg_split('/\s+/', $q);
$terms = [];
foreach ($parts as $p) {
    $p = trim($p);
    if ($p === '' || mb_strlen($p) < 2) continue; // Ignore single character tokens
    $terms[] = $p;
}

if (count($terms) === 0) {
    json_exit(['ok' => false, 'error' => 'Search must have a minimum of two or more letters'], 400);
}

// build WHERE with LIKE clauses
$whereParts = [];
$params = [];
$types = '';
foreach ($terms as $t) {
    $whereParts[] = "LOWER(n.note_text) LIKE ?";
    $params[] = '%' . mb_strtolower($t) . '%';
    $types .= 's';
}
// finish constructing where clause
$whereSQL = '(' . implode(' OR ', $whereParts). ')';

$limit = 100;
// construct sql query - return 100 results max
$selectSQL = "SELECT n.id, n.note_text, n.created_at, u.username
              FROM notes n
              JOIN users u ON n.user_id = u.id
              WHERE $whereSQL
              ORDER BY n.created_at DESC
              LIMIT ?";

// prepare db & execute query
$stmt = $db->prepare($selectSQL);
if ($stmt === false) {
    json_exit(['ok' => false, 'error' => 'DB prepare failed: ' . $db->error], 500);
}

$params[] = $limit;
$types .= 'i';

// build array of references for call_user_func_array
$bind_names = [];
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_names[] = &$params[$i];
}

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

json_exit(['ok' => true, 'notes' => $rows, 'total' => $total]);