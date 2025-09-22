<?php
// edit_gig.php - Edit existing gig.
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$gig_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM gigs WHERE id = ? AND user_id = ?");
$stmt->execute([$gig_id, $_SESSION['user_id']]);
$gig = $stmt->fetch();
if (!$gig) {
    echo "Gig not found or not yours.";
    exit;
}
 
// Fetch categories
$stmt_categories = $pdo->prepare("SELECT * FROM categories");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
 
    // Handle new images, append to existing
    $images = $gig['images'];
    if (isset($_FILES['images'])) {
        $uploaded = explode(',', $images);
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $target = 'uploads/' . basename($name);
                move_uploaded_file($_FILES['images']['tmp_name'][$key], $target);
                $uploaded[] = $target;
            }
        }
        $images = implode(',', $uploaded);
    }
 
    $stmt = $pdo->prepare("UPDATE gigs SET title = ?, description = ?, price = ?, category_id = ?, images = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $category_id, $images, $gig_id]);
    echo "<script>window.location.href = 'gig.php?id=$gig_id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gig</title>
    <style>
        /* Internal CSS - Similar to create */
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        form { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, textarea, select { display: block; width: 100%; margin: 10px 0; padding: 10px; }
        button { background: #1dbf73; color: white; border: none; padding: 10px; cursor: pointer; }
        @media (max-width: 768px) { form { padding: 10px; } }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Edit Gig</h2>
        <input type="text" name="title" value="<?php echo htmlspecialchars($gig['title']); ?>" required>
        <textarea name="description" required><?php echo htmlspecialchars($gig['description']); ?></textarea>
        <input type="number" name="price" value="<?php echo $gig['price']; ?>" step="0.01" required>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $gig['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="images[]" multiple>
        <p>Current Images: <?php echo $gig['images']; ?></p>
        <button type="submit">Update Gig</button>
    </form>
    <script>
        // Internal JS
        console.log('Edit gig page');
    </script>
</body>
</html>
