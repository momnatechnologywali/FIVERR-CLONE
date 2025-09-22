<?php
// index.php - Homepage showcasing featured and trending gigs.
session_start();
include 'db.php';
 
// Fetch featured gigs (e.g., top rated or recent active gigs)
$stmt = $pdo->prepare("SELECT g.*, u.username FROM gigs g JOIN users u ON g.user_id = u.id WHERE g.status = 'active' ORDER BY g.created_at DESC LIMIT 10");
$stmt->execute();
$gigs = $stmt->fetchAll();
 
// Fetch categories
$stmt_categories = $pdo->prepare("SELECT * FROM categories");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiverr Clone - Homepage</title>
    <style>
        /* Internal CSS - Beautiful, real-looking design with modern styles */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        header { background-color: #1dbf73; color: white; padding: 20px; text-align: center; }
        nav { background-color: #fff; padding: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        nav a { margin: 0 15px; text-decoration: none; color: #1dbf73; font-weight: bold; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .gig-card { display: flex; flex-wrap: wrap; justify-content: space-around; }
        .gig { width: 30%; margin: 10px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .gig:hover { transform: scale(1.05); }
        .gig img { max-width: 100%; border-radius: 4px; }
        .categories { display: flex; flex-wrap: wrap; justify-content: center; }
        .category { margin: 10px; padding: 10px 20px; background: #1dbf73; color: white; border-radius: 20px; text-decoration: none; }
        footer { text-align: center; padding: 10px; background: #1dbf73; color: white; }
        @media (max-width: 768px) { .gig { width: 100%; } }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Fiverr Clone</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Welcome, <?php echo $_SESSION['username']; ?>! <a href="profile.php" style="color: white;">Profile</a> | <a href="logout.php" style="color: white;">Logout</a></p>
        <?php else: ?>
            <a href="login.php" style="color: white;">Login</a> | <a href="signup.php" style="color: white;">Signup</a>
        <?php endif; ?>
    </header>
    <nav>
        <a href="index.php">Home</a>
        <a href="search.php">Search</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_gig.php">Create Gig</a>
            <a href="orders.php">Orders</a>
            <a href="messages.php">Messages</a>
        <?php endif; ?>
    </nav>
    <div class="container">
        <h2>Featured Gigs</h2>
        <div class="gig-card">
            <?php foreach ($gigs as $gig): ?>
                <div class="gig">
                    <h3><?php echo htmlspecialchars($gig['title']); ?></h3>
                    <p>By: <?php echo htmlspecialchars($gig['username']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
                    <p>Price: $<?php echo $gig['price']; ?></p>
                    <a href="gig.php?id=<?php echo $gig['id']; ?>">View Gig</a>
                </div>
            <?php endforeach; ?>
        </div>
        <h2>Categories</h2>
        <div class="categories">
            <?php foreach ($categories as $cat): ?>
                <a href="search.php?category=<?php echo $cat['id']; ?>" class="category"><?php echo htmlspecialchars($cat['name']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <footer>&copy; 2025 Fiverr Clone</footer>
    <script>
        // Internal JS - For any basic interactions, e.g., confirmations
        console.log('Homepage loaded');
    </script>
</body>
</html>
