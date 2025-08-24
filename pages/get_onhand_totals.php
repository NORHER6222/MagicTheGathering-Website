<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$username = $_SESSION['username'];
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$sql = 'SELECT i.id AS inventory_id,
               i.quantity + COALESCE(SUM(dc.quantity),0) AS total
        FROM inventory i
        LEFT JOIN deck_cards dc ON dc.inventory_id = i.id
        WHERE i.user_id = ?
        GROUP BY i.id, i.quantity';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r; }
$stmt->close();
echo json_encode($out);
