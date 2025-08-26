<?php
require '../includes/auth.php';
?>
<!DOCTYPE html>
<html>
    
    <video autoplay loop muted playsinline class="back-video">
                <source src="../videos/friendspage.mp4" type="video/mp4">
                 Browser does not support video
     </video>

<head>
    <title>Help</title>
    
</head>
<body>
<?php include '../includes/nav.php'; ?>

<link rel="stylesheet" href="../css/help.css">


    <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>


<h1>Help & Documentation</h1>
<p class="welcome">Welcome to the Magic: The Gathering Collection Manager. Here’s how to use the features:</p>
<div id="featuresbox">
<ul>
    <li><strong>Home:</strong> Quick dashboard showing your first 5 friends and first 5 cards.</li>
    <li><strong>Inventory:</strong> Add new cards with quantities, view your collection in a scrollable list, filter by name, and remove entries.</li>
    <li><strong>Friends:</strong> Search and add friends by username, view your friends list in a scrollable pane, filter names, and remove friends.</li>
    <li><strong>View Friend Inventory:</strong> Click on any friend’s name to see their card collection on a dedicated page.</li>
    <li><strong>Help:</strong> You’re here! This page provides guidance on using the app.</li>
    <li><strong>Logout:</strong> Securely end your session.</li>
<li><strong>Build a Deck:</strong> Create decks from your inventory and assign cards to them using the dropdowns on the Inventory page. View a deck to see its contents.</li>
    <li><strong>Add Any MTG Card:</strong> Use the Scryfall search box on the Inventory page to search the entire MTG library and add cards directly to your collection.</li>
    <li><strong>Your Profile:</strong> Pick a profile icon on the Profile page; it will appear across the app.</li>
</ul>
</div>
</body>

<footer>
    <section class="footer">
       <h4 class="sec2"></h4>
       <p class="footnotes">Wizards of the Coast, Magic: The Gathering, and their logos are trademarks of Wizards of the Coast LLC in the United States and other countries. © 1993-2025 Wizards. All Rights Reserved.<br><br>MTG MANAGER is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC. MTG MANAGER may use the trademarks and other intellectual property of Wizards of the Coast LLC, which is permitted under Wizards' Fan Site Policy. MAGIC: THE GATHERING® is a trademark of Wizards of the Coast. For more information about Wizards of the Coast or any of Wizards' trademarks or other intellectual property, please visit their website at https://company.wizards.com/.<br><br>© 2025 MTG MANAGER·Terms of Service·Privacy Policy·Affiliate Disclosures·Version 2025.v01</p>
    </section>
</footer>

</html>
