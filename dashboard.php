<?php
session_start();
include 'db.php';

// Check if user is NOT logged in
if (!isset($_SESSION['user_id'])) {
    // Display login and register options for logged-out users
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Welcome to Our Blog</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5 text-center">
      <h2>Welcome to the Blog!</h2>
      <p>Please login or register to access the dashboard and create posts.</p>
      <div class="mt-4">
        <a href="login.php" class="btn btn-primary btn-lg me-3">Login</a>
        <a href="register.php" class="btn btn-success btn-lg">Register</a>
      </div>
    </div>
    </body>
    </html>
    <?php
    exit; // Stop execution here, don't show the dashboard content
}

// --- REST OF YOUR ORIGINAL DASHBOARD.PHP CODE GOES HERE ---
// (This part will only execute if the user IS logged in)

// Get search & pagination input
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

// Join with users table to get post owner's username
$stmt = $pdo->prepare("
    SELECT posts.*, users.username
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    WHERE posts.title LIKE :search
    ORDER BY posts.created_at DESC
    LIMIT :offset, :limit
");
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Count total for pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ?");
$totalStmt->execute(["%$search%"]);
$totalPosts = $totalStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
  <a href="logout.php" class="btn btn-danger float-end">Logout</a>

  <form class="d-flex mb-4" role="search">
    <input class="form-control me-2" type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search posts...">
  </form>

  <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="create_post.php" class="btn btn-success mb-3">+ Add Post</a>
  <?php endif; ?>

  <ul class="list-group">
    <?php foreach ($posts as $post): ?>
      <li class="list-group-item">
        <h5><?= htmlspecialchars($post['title']) ?></h5>
        <p class="mb-1 text-muted">
          By <?= htmlspecialchars($post['username'] ?? 'Unknown') ?> •
          <?= date("d M Y, h:i A", strtotime($post['created_at'])) ?>
        </p>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <?php if ($_SESSION['role'] === 'admin'): ?>
          <div class="mt-2">
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Delete this post?');" class="btn btn-sm btn-danger">Delete</a>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <nav class="mt-4">
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

</div>
</body>
</html>