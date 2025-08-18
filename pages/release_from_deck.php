<?php
require '../includes/auth.php';
require_once '../includes/db.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }

$deck_id = intval($_POST['deck_id'] ?? 0);
$inventory_id = intval($_POST['inventory_id'] ?? 0);
$qty = max(1, intval($_POST['quantity'] ?? 1));

$user = $_SESSION['username'] ?? null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

// ensure deck belongs to user
$ownDeck = $conn->prepare("SELECT id FROM decks WHERE id=? AND user_id=?");
$ownDeck->bind_param("ii",$deck_id,$uid);
$ownDeck->execute();
if(!$ownDeck->get_result()->fetch_assoc()){ http_response_code(403); echo "forbidden"; exit; }

// ensure inventory belongs to user
$ownInv = $conn->prepare("SELECT id FROM inventory WHERE id=? AND user_id=?");
$ownInv->bind_param("ii",$inventory_id,$uid);
$ownInv->execute();
if(!$ownInv->get_result()->fetch_assoc()){ http_response_code(403); echo "forbidden"; exit; }

$conn->begin_transaction();

try {
    // check deck_cards quantity
    $sel = $conn->prepare("SELECT id, quantity FROM deck_cards WHERE deck_id=? AND inventory_id=? FOR UPDATE");
    $sel->bind_param("ii",$deck_id,$inventory_id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    if(!$row || (int)$row['quantity'] < $qty){
        $conn->rollback();
        http_response_code(400);
        echo "insufficient_deck_quantity";
        exit;
    }

    $remaining = (int)$row['quantity'] - $qty;
    if($remaining > 0){
        $upd = $conn->prepare("UPDATE deck_cards SET quantity=? WHERE id=?");
        $upd->bind_param("ii",$remaining,$row['id']);
        $upd->execute();
    } else {
        $del = $conn->prepare("DELETE FROM deck_cards WHERE id=?");
        $del->bind_param("i",$row['id']);
        $del->execute();
    }

    // add back to inventory
    $updInv = $conn->prepare("UPDATE inventory SET quantity = quantity + ? WHERE id = ? AND user_id = ?");
    $updInv->bind_param("iii",$qty,$inventory_id,$uid);
    $updInv->execute();

    $conn->commit();
    header('Location: view_deck.php?id=' . $deck_id); exit;
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "error";
}
