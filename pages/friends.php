<?php
require '../includes/auth.php';
require '../includes/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Friends</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .box-section {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 6px;
            max-height: 300px;
            overflow-y: auto;
        }
        .friend-entry, .user-entry {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            background: #fff;
            border: 1px solid #ddd;
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

<h2>Your Friends</h2>
<input type="text" id="friendSearch" placeholder="Search friends...">
<div class="box-section" id="friends-results">
    <!-- Current friends loaded via AJAX -->
</div>

<h2>Add a Friend</h2>
<input type="text" id="userSearch" placeholder="Search users to add...">
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
</html>
