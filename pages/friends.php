<?php
require '../includes/auth.php';
require '../includes/db.php';
?>
<!DOCTYPE html>
<html>
     <video autoplay loop muted playsinline class="back-video">
                <source src="../videos/friendspage.mp4" type="video/mp4">
                 Browser does not support video
     </video>
<head>
    <title>Friends</title>
    <link rel="stylesheet" href="../css/friends.css">
    <style>
        .box-section {
            
            color: white;
            margin: auto;
            padding: 10px;
            background: transparent;
            max-height: 300px;
            overflow-y: auto;
            width: 25%;
        }
        .friend-entry, .user-entry {
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 10px;
            background: black;
            opacity: 90%;
            border: 2.5px ridge #eb5a1cff;
            border-radius: 4px;
            margin-bottom: 5px;
        }
    </style>
    <script>
        function loadFriends() {
            fetch('get_friends_list.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('friends-results').innerHTML = html;
                    filterFriends();
                });
        }
        function removeFriend(username) {
            fetch('remove_friend.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'friend=' + encodeURIComponent(username)
            }).then(() => loadFriends());
        }
        function filterFriends() {
            const filter = document.getElementById('friendSearch').value.toLowerCase();
            const entries = document.querySelectorAll('.friend-entry');
            let count = 0;
            entries.forEach(e => {
                const txt = e.querySelector('span').innerText.toLowerCase();
                if (txt.includes(filter) && count < 10) {
                    e.style.display = 'flex';
                    count++;
                } else {
                    e.style.display = 'none';
                }
            });
        }
        function addFriend(username) {
            fetch('process_add_friend.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'friend_username=' + encodeURIComponent(username)
            }).then(() => {
                loadFriends();
                filterUsers();
            });
        }
        function filterUsers() {
            const filter = document.getElementById('userSearch').value.toLowerCase();
            const entries = document.querySelectorAll('.user-entry');
            let count = 0;
            entries.forEach(e => {
                const txt = e.querySelector('span').innerText.toLowerCase();
                if (txt.includes(filter) && count < 10) {
                    e.style.display = 'flex';
                    count++;
                } else {
                    e.style.display = 'none';
                }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            loadFriends();
            document.getElementById('friendSearch').addEventListener('input', filterFriends);
            document.getElementById('userSearch').addEventListener('input', filterUsers);
        });
    </script>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div>
    <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>
</div>

<h2>Your Friends</h2>
<input type="text" id="friendSearch" placeholder="Search Friends...">
<div class="box-section" id="friends-results">
    
</div>

<h2>Add A Friend</h2>
<input type="text" id="userSearch" placeholder="Search Users To Add...">
<div class="box-section" id="users-results">
<?php
$current = $_SESSION['username'];
$users = $conn->query("SELECT username FROM users WHERE username != '{$current}' ORDER BY username");
$count = 0;
while ($row = $users->fetch_assoc()):
    $u = htmlspecialchars($row['username'], ENT_QUOTES);
    $style = $count < 10 ? '' : 'display:none;';
?>
    <div class="user-entry" style="<?php echo $style; ?>">
        <span><?php echo $u; ?></span>
        <button onclick="addFriend('<?php echo addslashes($u); ?>')">Add</button>
    </div>
<?php
    $count++;
endwhile;
?>
</div>
</body>

    <footer>
        <section class="footer">
        <h4 class="sec2"></h4>
             <p class="footnotes">Wizards of the Coast, Magic: The Gathering, and their logos are trademarks of Wizards of the Coast LLC in the United States and other countries. © 1993-2025 Wizards. All Rights Reserved.<br><br>MTG MANAGER is not affiliated with, endorsed, sponsored, or specifically approved by Wizards of the Coast LLC. MTG MANAGER may use the trademarks and other intellectual property of Wizards of the Coast LLC, which is permitted under Wizards' Fan Site Policy. MAGIC: THE GATHERING® is a trademark of Wizards of the Coast. For more information about Wizards of the Coast or any of Wizards' trademarks or other intellectual property, please visit their website at https://company.wizards.com/.<br><br>© 2025 MTG MANAGER·Terms of Service·Privacy Policy·Affiliate Disclosures·Version 2025.v01</p>
        </section>
    </footer>

</html>
