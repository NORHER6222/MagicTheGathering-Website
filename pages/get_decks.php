<?php
require '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');
$user = $_SESSION['username'] ?? null;
if(!$user){ http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

$decks = [];
$q = $conn->prepare("SELECT id, name, created_at FROM decks WHERE user_id=? ORDER BY created_at DESC");
$q->bind_param("i",$uid);
$q->execute();
$res = $q->get_result();
while($row=$res->fetch_assoc()) $decks[]=$row;
echo json_encode($decks);