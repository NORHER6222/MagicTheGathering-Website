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
if (!$user_id) { echo json_encode([]); exit; }

$sql = 'SELECT d.id, d.name, COALESCE(SUM(dc.quantity),0) as count
        FROM decks d
        LEFT JOIN deck_cards dc ON dc.deck_id = d.id
        WHERE d.user_id = ?
        GROUP BY d.id, d.name
        ORDER BY d.name ASC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) { $rows[] = $r; }
$stmt->close();

echo json_encode($rows);
