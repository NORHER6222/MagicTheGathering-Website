<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';
?>
<!doctype html>
<html>
  <link rel="stylesheet" href="../css/searchcard.css">

    

<head>
<meta charset="utf-8">
<title>Add Any MTG Card (Scryfall)</title>
</head>
<body>
<?php if (file_exists(__DIR__ . '/../includes/navbar.php')) { include __DIR__ . '/../includes/navbar.php'; } else { include __DIR__ . '/../includes/nav.php'; } ?>

    <div>
        <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>
    </div>

<div id="scryfall-filters"></div>
<div id="scryfall-results"></div>
<script src="../assets/js/scryfall-filters.js"></script>
</body>
</html>
