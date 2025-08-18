<?php
require '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

$user = $_SESSION['username'] ?? null;
if(!$user){ echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s",$user);
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['id'];

// Fetch base inventory
$items = [];
$q = $conn->prepare("SELECT id, card_name, quantity FROM inventory WHERE user_id=? ORDER BY card_name");
$q->bind_param("i",$uid);
$q->execute();
$res = $q->get_result();
while($row = $res->fetch_assoc()){
    $row['in_decks'] = 0;
    $row['breakdown'] = []; // [{deck, qty}]
    $items[$row['id']] = $row;
}

// Aggregate deck quantities
if(count($items)>0){
    $ids = implode(',', array_map('intval', array_keys($items)));
    $sql = "SELECT dc.inventory_id, d.name as deck_name, dc.quantity
            FROM deck_cards dc
            JOIN decks d ON d.id=dc.deck_id
            WHERE dc.inventory_id IN ($ids)";
    $r = $conn->query($sql);
    while($r && $row = $r->fetch_assoc()){
        $iid = (int)$row['inventory_id'];
        $qty = (int)$row['quantity'];
        if(isset($items[$iid])){
            $items[$iid]['in_decks'] += $qty;
            $items[$iid]['breakdown'][] = ['deck'=>$row['deck_name'], 'qty'=>$qty];
        }
    }
}

// Compute on-hand totals
$out = [];
foreach($items as $it){
    $it['on_hand_total'] = (int)$it['quantity'] + (int)$it['in_decks'];
    $out[] = $it;
}

echo json_encode($out);
?>