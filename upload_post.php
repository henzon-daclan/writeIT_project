<?php
session_start();

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$content = trim($_POST['content'] ?? '');
$imagePath = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $targetFile;
    }
}

$stmt = $connection->prepare("INSERT INTO posts (user_id, post_content, post_image) VALUES (?, ?, ?)");
$stmt->bind_param('iss', $userId, $content, $imagePath);
$stmt->execute();
$stmt->close();

header('Location: index.php');
exit;
?>