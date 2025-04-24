<?php
include 'header.php';
include 'db.php';

$posts = [];

$query = "
SELECT p.*, u.first_name,
       (SELECT COUNT(*) FROM likes WHERE likes.post_id = p.post_id) as like_count
FROM posts p
JOIN users u ON u.user_id = p.user_id
ORDER BY p.post_id DESC
";

$result = $connection->query($query);

while ($row = $result->fetch_assoc()) {
    $postId = $row['post_id'];

    $comments = [];
    $commentQuery = "
    SELECT c.comment_text, u.first_name FROM comments c JOIN users u ON u.user_id = c.user_id
    WHERE c.post_id = $postId
    ORDER BY c.comment_id DESC
    ";
    
    $commentResult = $connection->query($commentQuery);
    
    while ($commentRow = $commentResult->fetch_assoc()) {
        $comments[] = [
            'first_name' => $commentRow['first_name'],
            'initial' => !empty($commentRow['first_name']) 
    ? strtoupper($commentRow['first_name'][0]) 
    : '',
            'comment' => $commentRow['comment_text'],
        ];
    }

    $posts[] = [
        'post_id' => $row['post_id'],
        'user_first_name' => $row['first_name'],
        'content' => $row['post_content'],
        'image' => $row['post_image'],
        'likes' => $row['like_count'],
        'comments' => $comments,
    ];
}

$isAuthenticated = isset($_SESSION['user_id']);
$avatarInitial = $isAuthenticated ? strtoupper($_SESSION['first_name'][0]) : '?';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WriteIT - Feed</title>
    <link rel="stylesheet" href="styles/menu_style.css">
    
</head>
<body>

<div class="main-container">
    <div class="all-post-card-container">

    <div class="card" onclick="handlePostClick()">
    <div class="post-header">
        <div class="avatar"><?= $avatarInitial ?></div>
        <input class="input-field" type="text" placeholder="What's on your mind?" readonly>
    </div>
</div>

<?php if (empty($posts)): ?>
        <div class="card" style="text-align: center; margin-top: 2rem; width: 500px">
            <h3>No posts yet</h3>
            <p>Be the first to share something!</p>
        </div>
    <?php endif; ?>

    <?php foreach ($posts as $index => $post): ?>
    <div class="card" id="post-<?= $index ?>">
        <div class="post-header" style="margin-bottom: 2rem;">
            <div class="avatar" onclick="handleAuthClick()"><?= strtoupper($post['user_first_name'][0]) ?></div>
            <div class="post-name"><?= htmlspecialchars($post['user_first_name']) ?></div>
        </div>
        <div class="post-content"><?= htmlspecialchars($post['content']) ?></div>
        <?php if (!empty($post['image'])): ?>
            <img class="post-image" src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image">
        <?php endif; ?>
        <div class="post-actions" style="margin-top: 2rem;">
            <div class="icon" onclick="handleLike(<?= $post['post_id'] ?>)">
                <span id="like-icon-<?= $index ?>">👍</span>
                <span id="like-count-<?= $post['post_id'] ?>"><?= $post['likes'] ?: '' ?></span>
            </div>
            <div class="icon" onclick="toggleComments(<?= $index ?>)">
                <span id="comment-icon-<?= $index ?>">💬</span>
                <span id="comment-count-<?= $index ?>"><?= count($post['comments']) ?: '' ?></span>
            </div>
        </div>

        <div id="comments-section-<?= $index ?>" style="display: none; margin-top: 10px;">
            <div id="comment-list-<?= $index ?>">
                    <?php foreach ($post['comments'] as $comment): ?>
            <div class="comment" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
                <div class="avatar"><?= $comment['initial'] ?></div>
                <div>
                    <div style="font-weight: bold;"><?= htmlspecialchars($comment['first_name']) ?></div>
                    <div><?= htmlspecialchars($comment['comment']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
            </div>
            <div style="margin-top: 10px; display: flex;">
                <input type="text" id="comment-input-<?= $index ?>" class="input-field" placeholder="Write a comment..." style="flex-grow: 1;">
                <button onclick="postComment(<?= $post['post_id'] ?>, <?= $index ?>)">Post</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<div class="side-post-cards">
<div class="card" style="margin-left: 2rem; width:350px; height:150px;">
<h4 style="height: 0;">Trending topics</h4>
<div style="display: flex; flex-direction:column;">
    <div style="margin-bottom:5px; display: flex; justify-content: space-between; height: 20px;">
        <h4>#Finals</h4>
        <h5 style="color: grey; padding-top: 0; padding-bottom: 0;">1.2k posts</h5>
    </div>
    <div style="margin-bottom: 5px;">
    <div style=" display: flex; justify-content: space-between; height: 20px;">
        <h4>#1stYearBullying</h4>
        <h5 style="color: grey;">11.2k posts</h5>
    </div>
    </div>
    <div style="margin-bottom: 5px;">
    <div style=" display: flex; justify-content: space-between; height: 20px;">
        <h4>#4thYearStudentSuicide</h4>
        <h5 style="color: grey;">31.5k posts</h5>
    </div>
    </div>
</div>
</div>

    <div class="card" style="margin-left: 2rem; width:350px; height:150px;">
        <h4>Popular Club</h4>
            <div style="display: flex; flex-direction:column;"></div>
                <div style="margin-bottom: 10px;">👨‍💻 r/ProgrammingClub</div>
                <div style="margin-bottom: 10px;">🎵 r/MusicClub</div>
                <div style="margin-bottom: 10px;">📷 r/PhotographyClub</div>

            </div>
    </div>
</div>
</div>

<div class="modal" id="postModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 style="text-align: center;">Create a Post</h3>
        <form action="upload_post.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="content" placeholder="Write something..." required>
            
            <label for="file-upload" class="custom-file-upload" id="file-upload-label">
    <i class="fas fa-cloud-upload-alt"></i><br>
    <span id="file-name">Click or drag to upload an image</span>
</label>
<input id="file-upload" type="file" name="image" accept="image/*">
            
            <button type="submit">Post</button>
        </form>
    </div>
</div>

<script>
        const fileInput = document.getElementById('file-upload');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = "Click or drag to upload an image";
        }
    });

    const isAuthenticated = <?= json_encode($isAuthenticated) ?>;
    const likedPosts = {}; 

    function handlePostClick() {
        if (!isAuthenticated) {
            window.location.href = "/auth/login.php";
            return;
        }
        const modal = document.getElementById("postModal");
        modal.style.display = "flex";

    }

    function closeModal() {
        const modal = document.getElementById('postModal');
        modal.style.display = 'none';
    }

    function handleAuthClick() {
        if (!isAuthenticated) {
            window.location.href = "/auth/login.php";
        }
    }

    function handleLike(postId) {
    if (!isAuthenticated) return handleAuthClick();

    fetch('like_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const likeCount = document.getElementById(`like-count-${postId}`);
            likeCount.textContent = data.likes > 0 ? data.likes : '';
        } else {
            console.error(data.error);
        }
    })
    .catch(err => console.error(err));
    }

    function toggleComments(index) {
        if (!isAuthenticated) return handleAuthClick();

        const section = document.getElementById(`comments-section-${index}`);
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }

    function postComment(postId, index) {
    const input = document.getElementById(`comment-input-${index}`);
    const list = document.getElementById(`comment-list-${index}`);
    const count = document.getElementById(`comment-count-${index}`);
    const text = input.value.trim();

    if (!text) return;

    fetch('comment_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&comment_text=${encodeURIComponent(text)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const commentEl = document.createElement('div');
            commentEl.className = 'comment';
            commentEl.textContent = data.comment;
            list.appendChild(commentEl);

            input.value = '';
            count.textContent = parseInt(count.textContent || 0) + 1;
        } else {
            alert('Failed to post comment: ' + data.error);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error posting comment');
    });
}
</script>


</body>
</html>
