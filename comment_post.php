<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $userId = $_SESSION['user_id'];
    $commentText = trim($_POST['comment_text'] ?? '');

    if (!$postId || $commentText === '') {
        echo json_encode(['success' => false, 'error' => 'Missing fields']);
        exit;
    }

    $stmt = $connection->prepare("INSERT INTO comments (user_id, post_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $postId, $commentText);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'comment' => $commentText]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>
