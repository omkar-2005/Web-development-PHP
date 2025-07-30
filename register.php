<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Register</h2>
  <form method="post">
    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
    <select name="role" class="form-control mb-2" required>
      <option value="editor">Editor</option>
      <option value="admin">Admin</option>
    </select>
    <button class="btn btn-success">Register</button>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = htmlspecialchars(trim($_POST['username']));
      $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
      $role = $_POST['role'];

      try {
          // Check for existing username
          $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
          $checkStmt->execute([$username]);
          if ($checkStmt->fetchColumn() > 0) {
              echo "<div class='alert alert-warning mt-3'>Username already exists. Try another.</div>";
          } else {
              $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
              $stmt->execute([$username, $password, $role]);
              echo "<div class='alert alert-success mt-3'>Registration successful. <a href='login.php'>Login here</a></div>";
          }
      } catch (PDOException $e) {
          echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
      }
  }
  ?>
</div>
</body>
</html>
