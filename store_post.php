<?php
session_start();
include 'db.php';

// Only admin can store posts
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if ($title && $content) {
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $title, $content]);
}

header("Location: dashboard.php");
exit;
?>
