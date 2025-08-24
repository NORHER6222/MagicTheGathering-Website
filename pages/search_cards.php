<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Any MTG Card (Scryfall)</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php if (file_exists(__DIR__ . '/../includes/navbar.php')) { include __DIR__ . '/../includes/navbar.php'; } else { include __DIR__ . '/../includes/nav.php'; } ?>

<div id="scryfall-filters"></div>
<div id="scryfall-results"></div>
<script src="../assets/js/scryfall-filters.js"></script>
</body>
</html>
