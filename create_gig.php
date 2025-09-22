<?php
// create_gig.php - Create new gig.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
// Fetch categories
$stmt_categories = $pdo->prepare("SELECT * FROM categories");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
 
    // Handle images (simple, comma-separated paths)
    $images = '';
    if (isset($_FILES['images'])) {
        $uploaded = [];
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $target = 'uploads/' . basename($name);
                move_uploaded_file($_FILES['images']['tmp_name'][$key], $target);
                $uploaded[] = $target;
            }
        }
        $images = implode(',', $uploaded);
    }
 
    $stmt = $pdo->prepare("INSERT INTO gigs (user_id, title, description, price, category_id, images) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $price, $category_id, $images]);
    echo "<script>window.location.href = 'index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gig</title>
    <style>
        /* Internal CSS - Form styling */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        form { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, textarea, select { display: block; width: 100%; margin: 10px 0; padding: 10px; }
        button { background: #1dbf73; color: white; border: none; padding: 10px; cursor: pointer; }
        @media (max-width: 768px) { form { padding: 10px; } }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Create New Gig</h2>
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="images[]" multiple>
        <button type="submit">Create Gig</button>
    </form>
    <script>
        // Internal JS
        console.log('Create gig page');
    </script>
</body>
</html>
