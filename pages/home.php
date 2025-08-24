<?php
require '../includes/auth.php';
require '../includes/db.php';
$username = $_SESSION['username'];
$user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
?>
<!DOCTYPE html>
<html>
    <video autoplay loop muted playsinline class="back-video">
                <source src="../videos/backgroundvid.mp4" type="video/mp4">
                 Browser does not support video
            </video>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .box-section { margin:10px 0; padding:10px; background: black; border:1px solid #e8e8e8ff; color: white;}
        .entry { padding:6px; border-bottom:1px solid #a34b1bff; }
        .entry a { text-decoration:none; color:white; }
    </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div>
    <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>
</div>


<div class="">

    <h3>FRIENDS</h3>
        <div class="box-section">
        <?php
            $fRes = $conn->query("SELECT friend_username FROM friends WHERE user_id=$user_id ORDER BY friend_username LIMIT 5");
            while ($f = $fRes->fetch_assoc()):
            $name = htmlspecialchars($f['friend_username'], ENT_QUOTES);
        ?>
        <div class="entry"><a href="view_inventory.php?user=<?php echo urlencode($name); ?>"><?php echo $name; ?></a></div>
    <?php endwhile; ?>
    </div>
    <h3>INVENTORY</h3>
    <div class="box-section">

    <?php
    $iRes = $conn->query("SELECT card_name, quantity FROM inventory WHERE user_id=$user_id ORDER BY card_name LIMIT 5");
    while ($i = $iRes->fetch_assoc()):
    ?>
    <div class="entry"><?php echo htmlspecialchars($i['card_name']); ?> (x<?php echo intval($i['quantity']); ?>)</div>
    <?php endwhile; ?>
    </div>
</div>






<!-----------------SECTION 2 - SAMPLE ONLY----------------->




<div class="magicLogo">
    <img src="../img/magic.png" alt="">
</div>



<div>
    <h4 class="sec2">Your Gateway To All Things Magic.</h4>
    <p>We support all official set releases and also community driven decks.</p>
    <p>Check Our Popular Community Drive Decks Below.</p><br><br>
</div>



<section class="homeDecks">

        <div class="inline-block">
            <img src="../img/angelCom.png" alt="">
        </div>

        <div class="inline-block">    
            <img src="../img/deck2.png" alt="">
        </div>

        <div class="inline-block">    
            <img src="../img/deck3.png" alt="">
        </div>

        <div class="inline-block">
            <img src="../img/deck4.png" alt="">
        </div>

        
</section>
<button class="homeDecksBtn">Explore More Decks</button>
<section class="homeFriends">

        <h4 class="sec2">Find Creators, Collectors & Friends<h4>
            
        <p>Explore the community's top collectors or friends near you!</p><br><br>

        <div class="inline-block-friends">
            <img src="../img/favatars/01.png" alt="">
        </div>
        
        <div class="inline-block-friends">
            <img src="../img/favatars/02.png" alt="">
        </div>

        <div class="inline-block-friends">
            <img src="../img/favatars/03.png" alt="">
        </div>

        <div class="inline-block-friends">
            <img src="../img/favatars/1.png" alt="">
        </div>

        <div class="inline-block-friends">    
            <img src="../img/favatars/2.png" alt="">
        </div>

        <div class="inline-block-friends">    
            <img src="../img/favatars/3.png" alt="">
        </div>

        <div class="inline-block-friends">
            <img src="../img/favatars/4.png" alt="">
        </div>

        <div class="inline-block-friends">
            <img src="../img/favatars/5.png" alt="">
        </div>

        
</section>

        <button class="homeFriendsBtn">Find More Collectors</button>


<section class="homeSupport">
      
        <h4 class="sec2">Support Us<h4>
        <p>Support Our Pateron & Join Our Discord Server</p>

                <div class="inline-block-supportus">
                    <img src="../img/patreon.png" alt="">
                </div>
                
                <div class="inline-block-supportus">
                    <img src="../img/discord.png" alt="">
                </div>

</section>

</body>


<footer>
    <section class="footer">
       <h4 class="sec2"></h4>
       <p class="footnotes">Wizards of the Coast, Magic: The Gathering, and their logos are trademarks of Wizards of the Coast LLC in the United States and other countries. © 1993-2025 Wizards. All Rights Reserved.<br><br>MTG MANAGER is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC. MTG MANAGER may use the trademarks and other intellectual property of Wizards of the Coast LLC, which is permitted under Wizards' Fan Site Policy. MAGIC: THE GATHERING® is a trademark of Wizards of the Coast. For more information about Wizards of the Coast or any of Wizards' trademarks or other intellectual property, please visit their website at https://company.wizards.com/.<br><br>© 2025 MTG MANAGER·Terms of Service·Privacy Policy·Affiliate Disclosures·Version 2025.v01</p>
    </section>
</footer>
</html>