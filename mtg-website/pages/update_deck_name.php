<?php
require '../includes/auth.php';
require_once '../includes/db.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }

$deck_id = intval($_POST['deck_id'] ?? 0);
$new_name = trim($_POST['name'] ?? '');
if($deck_id<=0 || $new_name===''){ http_response_code(400); echo "invalid"; exit; }

$user = $_SESSION['username'] ?? null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

$ownDeck = $conn->prepare("SELECT id FROM decks WHERE id=? AND user_id=?");
$ownDeck->bind_param("ii",$deck_id,$uid);
$ownDeck->execute();
if(!$ownDeck->get_result()->fetch_assoc()){ http_response_code(403); echo "forbidden"; exit; }

$upd = $conn->prepare("UPDATE decks SET name=? WHERE id=?");
$upd->bind_param("si",$new_name,$deck_id);
$upd->execute();
echo "ok";
?>