<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if ($postId && $userId) {
        // Check if user already liked
        $stmt = $connection->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Unlike
            $stmt = $connection->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param('ii', $postId, $userId);
            $stmt->execute();
        } else {
            // Like
            $stmt = $connection->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $postId, $userId);
            $stmt->execute();
        }

        // Get updated like count
        $stmt = $connection->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $likeCount = $result->fetch_assoc()['like_count'];

        echo json_encode(['success' => true, 'likes' => $likeCount]);
        exit;
    }
}
echo json_encode(['error' => 'Invalid request']);
