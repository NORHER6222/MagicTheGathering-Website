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

// verify ownership
$ownDeck = $conn->prepare("SELECT id FROM decks WHERE id=? AND user_id=?");
$ownDeck->bind_param("ii",$deck_id,$uid);
$ownDeck->execute();
if(!$ownDeck->get_result()->fetch_assoc()){ http_response_code(403); echo "forbidden"; exit; }

$ownInv = $conn->prepare("SELECT i.id, i.quantity FROM inventory i WHERE i.id=? AND i.user_id=?");
$ownInv->bind_param("ii",$inventory_id,$uid);
$ownInv->execute();
$invRow = $ownInv->get_result()->fetch_assoc();
if(!$invRow){ http_response_code(403); echo "forbidden"; exit; }

$available = (int)$invRow['quantity'];
if($available < $qty){ http_response_code(400); echo "insufficient_inventory"; exit; }

$conn->begin_transaction();

try {
    // upsert into deck_cards
    $sel = $conn->prepare("SELECT id, quantity FROM deck_cards WHERE deck_id=? AND inventory_id=? FOR UPDATE");
    $sel->bind_param("ii",$deck_id,$inventory_id);
    $sel->execute();
    $r = $sel->get_result()->fetch_assoc();
    if($r){
        $newq = (int)$r['quantity'] + $qty;
        $upd = $conn->prepare("UPDATE deck_cards SET quantity=? WHERE id=?");
        $upd->bind_param("ii",$newq,$r['id']);
        $upd->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO deck_cards (deck_id, inventory_id, quantity) VALUES (?,?,?)");
        $ins->bind_param("iii",$deck_id,$inventory_id,$qty);
        $ins->execute();
    }

    // deduct from inventory
    $updInv = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ? AND user_id = ?");
    $updInv->bind_param("iii",$qty,$inventory_id,$uid);
    $updInv->execute();

    $conn->commit();
    echo "ok";
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "error";
}
