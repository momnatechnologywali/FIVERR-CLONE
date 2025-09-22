<?php
// orders.php - List and manage orders for user.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
 
// Fetch orders as buyer or seller
$stmt = $pdo->prepare("SELECT o.*, g.title, ub.username as buyer_name, us.username as seller_name 
                       FROM orders o 
                       JOIN gigs g ON o.gig_id = g.id 
                       JOIN users ub ON o.buyer_id = ub.id 
                       JOIN users us ON o.seller_id = us.id 
                       WHERE o.buyer_id = ? OR o.seller_id = ?");
$stmt->execute([$user_id, $user_id]);
$orders = $stmt->fetchAll();
 
// Update status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
 
    // Check if user is seller for this order
    $stmt_check = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND seller_id = ?");
    $stmt_check->execute([$order_id, $user_id]);
    if ($stmt_check->fetch()) {
        $stmt_update = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt_update->execute([$new_status, $order_id]);
    }
    echo "<script>window.location.href = 'orders.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        /* Internal CSS - Orders list */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        button { background: #1dbf73; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        @media (max-width: 768px) { table { font-size: 12px; } }
    </style>
</head>
<body>
    <h2>Your Orders</h2>
    <table>
        <tr><th>Gig Title</th><th>Buyer</th><th>Seller</th><th>Price</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['title']); ?></td>
                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                <td>$<?php echo $order['price']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td>
                    <?php if ($user_id == $order['seller_id'] && in_array($order['status'], ['pending', 'in_progress'])): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status">
                                <option value="accepted">Accept</option>
                                <option value="rejected">Reject</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Complete</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    <?php endif; ?>
                    <a href="messages.php?order_id=<?php echo $order['id']; ?>">Message</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
        // Internal JS
        console.log('Orders page loaded');
    </script>
</body>
</html>
