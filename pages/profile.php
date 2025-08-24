<?php
require '../includes/auth.php';
require_once '../includes/db.php';

$user = $_SESSION['username'] ?? null;
if (!$user) { header('Location: /mtg-website/index.php'); exit; }

$stmt = $conn->prepare("SELECT id, username, COALESCE(avatar,'') as avatar FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$res = $stmt->get_result();
$me = $res->fetch_assoc();
$uid = (int)$me['id'];
$currentAvatar = $me['avatar'] ?? '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['avatar'])) {
    $avatar = $_POST['avatar'];
    $stmt2 = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt2->bind_param("si", $avatar, $uid);
    $stmt2->execute();
    $currentAvatar = $avatar;
    $msg = "Profile updated!";
}
?>
<!DOCTYPE html>
<html>

     <link rel="stylesheet" href="../css/profile.css">
<head>
    <title>Your Profile</title>
    
    <style>
        .avatar-scroll { display:flex; gap:50px; overflow-x:auto; padding:10px; border:5px solid; background-color: black; #ee6217ff; border-radius:10px; scroll-snap-type:x mandatory; }
        .avatar-scroll::-webkit-scrollbar { height:10px; }
        .avatar-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
        .avatar-item { scroll-snap-align:center; display:flex; align-items:center; justify-content:center; border:2px solid transparent; border-radius:50%; padding:4px; }
        .avatar-item input { display:none; }
        .avatar-item img { width:72px; height:72px; border-radius:50%; display:block; }
        .avatar-item.selected { border-color:#3b82f6; }
        .controls { margin-top:12px; }
    </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div>
    <a href="home.php"><img class="logo" src="../img/mtgmanager.png" alt=""></a>
</div>

<h3>Your Profile</h3>
<p>Signed In As <strong><?= htmlspecialchars($me['username']) ?></strong></p>
<?php if (!empty($msg)): ?><p style="color:green;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <h3>Choose An Avatar</h3>
<form method="post">
    
    <div class="avatar-scroll" id="avatarScroll">
        <?php
        $avatars = glob(__DIR__ . '/../img/avatars/*.{svg,png}', GLOB_BRACE);
        sort($avatars);
        foreach ($avatars as $file) {
            $rel = '/mtg-website/img/avatars/' . basename($file);
            $sel = ($rel === $currentAvatar) ? 'selected' : '';
            echo '<label class="avatar-item '.$sel.'">';
            echo '<input type="radio" name="avatar" value="'.$rel.'" '.(($rel===$currentAvatar)?'checked':'').'>';
            echo '<img src="'.$rel.'" alt="avatar">';
            echo '</label>';
        }
        ?>
         
    </div>
        
        <div>
            <button type="submit">Save</button>
        </div>
    
</form>

        
    

<h3>Change Password</h3>

<div id="chpassword">
      
            <form action="chpassword.php" method="POST">
                <div class="pass-ch">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="pass-ch">
                    <label for="c-password">Current Password</label>
                    <input type="password" id="c-password" name="c-password" required>
                </div>

                <div class="pass-ch">
                    <label for="n-password">New Password</label>
                    <input type="password" id="n-password" name="n-password" required>
                </div>

                <div class="pass-ch">
                    <label for="conf-password">Confirm New Password</label>
                    <input type="password" id="conf-password" name="conf-password" required>
                </div>
                <button type="submit" id="ChangePass">Change Password</button>
            </form>  
             
</div>

<script>
document.getElementById('avatarScroll').addEventListener('click', function(e){
  const label = e.target.closest('.avatar-item');
  if(!label) return;
  this.querySelectorAll('.avatar-item').forEach(el => el.classList.remove('selected'));
  label.classList.add('selected');
  const input = label.querySelector('input[type=radio]');
  if (input) input.checked = true;
});
</script>

</body>
</html>
