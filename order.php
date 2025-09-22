<?php
// order.php - Place and manage orders.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$gig_id = $_GET['gig_id'] ?? 0;
if ($gig_id) {
    // Place order
    $stmt_gig = $pdo->prepare("SELECT * FROM gigs WHERE id = ?");
    $stmt_gig->execute([$gig_id]);
    $gig = $stmt_gig->fetch();
 
    if ($gig) {
        $buyer_id = $_SESSION['user_id'];
        $seller_id = $gig['user_id'];
        $price = $gig['price'];
 
        $stmt = $pdo->prepare("INSERT INTO orders (gig_id, buyer_id, seller_id, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$gig_id, $buyer_id, $seller_id, $price]);
        $order_id = $pdo->lastInsertId();
        echo "<script>window.location.href = 'orders.php';</script>";
    }
} else {
    // View orders? Redirect to orders.php
    echo "<script>window.location.href = 'orders.php';</script>";
}
?>
