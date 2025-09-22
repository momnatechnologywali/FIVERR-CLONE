<?php
// gig.php - View single gig.
include 'db.php';
session_start();
 
$gig_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT g.*, u.username FROM gigs g JOIN users u ON g.user_id = u.id WHERE g.id = ? AND g.status = 'active'");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch();
if (!$gig) {
    echo "Gig not found.";
    exit;
}
 
// Delete gig if owner
if (isset($_POST['delete']) && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $gig['user_id']) {
    $stmt = $pdo->prepare("UPDATE gigs SET status = 'deleted' WHERE id = ?");
    $stmt->execute([$gig_id]);
    echo "<script>window.location.href = 'index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gig['title']); ?></title>
    <style>
        /* Internal CSS - Detailed gig view */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .gig-detail { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        img { max-width: 100%; margin: 10px 0; }
        button { background: #1dbf73; color: white; border: none; padding: 10px; cursor: pointer; }
        .delete-btn { background: red; }
        @media (max-width: 768px) { .gig-detail { padding: 10px; } }
    </style>
</head>
<body>
    <div class="gig-detail">
        <h2><?php echo htmlspecialchars($gig['title']); ?></h2>
        <p>By: <?php echo htmlspecialchars($gig['username']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
        <p>Price: $<?php echo $gig['price']; ?></p>
        <?php if ($gig['images']): ?>
            <?php foreach (explode(',', $gig['images']) as $img): ?>
                <img src="<?php echo trim($img); ?>" alt="Gig Image">
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $gig['user_id']): ?>
            <a href="order.php?gig_id=<?php echo $gig_id; ?>"><button>Place Order</button></a>
        <?php elseif (isset($_SESSION['user_id'])): ?>
            <a href="edit_gig.php?id=<?php echo $gig_id; ?>"><button>Edit Gig</button></a>
            <form method="POST" style="display:inline;">
                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Delete this gig?');">Delete Gig</button>
            </form>
        <?php endif; ?>
    </div>
    <script>
        // Internal JS
        console.log('Gig view loaded');
    </script>
</body>
</html>
