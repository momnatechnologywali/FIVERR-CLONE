<?php
// profile.php - Profile management.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
 
// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    // Handle profile pic upload (simple, assuming uploads folder)
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target = 'uploads/' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);
        $profile_pic = $target;
        $stmt = $pdo->prepare("UPDATE users SET bio = ?, profile_pic = ? WHERE id = ?");
        $stmt->execute([$bio, $profile_pic, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->execute([$bio, $_SESSION['user_id']]);
    }
    echo "<script>window.location.href = 'profile.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Internal CSS - Profile page styling */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .profile { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        img { max-width: 150px; border-radius: 50%; }
        form { margin-top: 20px; }
        input, textarea { display: block; width: 100%; margin: 10px 0; padding: 10px; }
        button { background: #1dbf73; color: white; border: none; padding: 10px; cursor: pointer; }
        @media (max-width: 768px) { .profile { padding: 10px; } }
    </style>
</head>
<body>
    <div class="profile">
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>
        <img src="<?php echo $user['profile_pic']; ?>" alt="Profile Pic">
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Bio: <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="bio" placeholder="Update Bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            <input type="file" name="profile_pic">
            <button type="submit">Update Profile</button>
        </form>
    </div>
    <script>
        // Internal JS
        console.log('Profile loaded');
    </script>
</body>
</html>
