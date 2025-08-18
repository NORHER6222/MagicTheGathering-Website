<?php
require '../includes/auth.php';
require_once '../includes/db.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: inventory.php'); exit; }
$name = trim($_POST['name'] ?? '');
if ($name===''){ header('Location: inventory.php'); exit; }

$user = $_SESSION['username'] ?? null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

$q = $conn->prepare("INSERT INTO decks (user_id, name) VALUES (?,?)");
$q->bind_param("is",$uid,$name);
$q->execute();
header('Location: inventory.php');