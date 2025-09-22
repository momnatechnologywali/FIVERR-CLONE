<?php
// messages.php - Messaging system for orders.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    echo "No order specified.";
    exit;
}
 
// Check if user is part of the order
$stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND (buyer_id = ? OR seller_id = ?)");
$stmt_order->execute([$order_id, $_SESSION['user_id'], $_SESSION['user_id']]);
$order = $stmt_order->fetch();
if (!$order) {
    echo "Access denied.";
    exit;
}
 
// Fetch messages
$stmt_messages = $pdo->prepare("SELECT m.*, u.username FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.order_id = ? ORDER BY m.timestamp ASC");
$stmt_messages->execute([$order_id]);
$messages = $stmt_messages->fetchAll();
 
// Send message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $receiver_id = ($order['buyer_id'] == $_SESSION['user_id']) ? $order['seller_id'] : $order['buyer_id'];
 
    $stmt = $pdo->prepare("INSERT INTO messages (order_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $_SESSION['user_id'], $receiver_id, $message]);
    echo "<script>window.location.href = 'messages.php?order_id=$order_id';</script>";
}
 
// Mark as read
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE order_id = ? AND receiver_id = ?")->execute([$order_id, $_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages for Order #<?php echo $order_id; ?></title>
    <style>
        /* Internal CSS - Chat-like interface */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .chat { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .message { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .sent { background: #1dbf73; color: white; text-align: right; }
        .received { background: #ddd; text-align: left; }
        form { margin-top: 20px; }
        textarea { width: 100%; padding: 10px; }
        button { background: #1dbf73; color: white; border: none; padding: 10px; cursor: pointer; }
        @media (max-width: 768px) { .chat { padding: 10px; } }
    </style>
</head>
<body>
    <div class="chat">
        <h2>Chat for Order #<?php echo $order_id; ?></h2>
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo ($msg['sender_id'] == $_SESSION['user_id']) ? 'sent' : 'received'; ?>">
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                <small><?php echo $msg['timestamp']; ?></small>
            </div>
        <?php endforeach; ?>
        <form method="POST">
            <textarea name="message" placeholder="Type your message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
    <script>
        // Internal JS - Auto scroll to bottom
        const chat = document.querySelector('.chat');
        chat.scrollTop = chat.scrollHeight;
    </script>
</body>
</html>
