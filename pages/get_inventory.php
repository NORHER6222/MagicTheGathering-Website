<?php
session_start();
require '../includes/db.php';
if(!isset($_SESSION['username'])){ http_response_code(401); exit; }
$username = $_SESSION['username'];
$user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
$res = $conn->query("SELECT id, card_name, quantity FROM inventory WHERE user_id=$user_id ORDER BY card_name");
while($row = $res->fetch_assoc()){
    echo '<div class="card-entry"><span>'.htmlspecialchars($row['card_name']).' (x'.$row['quantity'].')</span><button onclick="removeCard('.$row['id'].')">Remove</button></div>';
}
?>