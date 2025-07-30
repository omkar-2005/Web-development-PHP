<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "Invalid post ID.";
    exit;
}

// Use prepared statement
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Post</h2>
  <form method="POST" action="update_post.php">
    <input type="hidden" name="id" value="<?= $post['id'] ?>">
    <input type="text" name="title" class="form-control mb-2" value="<?= htmlspecialchars($post['title']) ?>" required>
    <textarea name="content" class="form-control mb-2" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
    <button class="btn btn-primary">Update</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
