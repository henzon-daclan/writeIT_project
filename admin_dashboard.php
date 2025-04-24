<?php
session_start();
require 'db.php';
include 'admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../login.php');
    exit;
}

$query = "
    SELECT 
        users.user_id,
        users.first_name,
        users.last_name, 
        users.email, 
        COUNT(posts.post_id) AS post_count
    FROM users
    LEFT JOIN posts ON posts.user_id = users.user_id
    WHERE users.role_id = 2
    GROUP BY users.user_id, users.email
    ORDER BY users.user_id ASC
";

$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WriteIT</title>
    <style>

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Admin Dashboard</h1>

        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Number of Posts</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['first_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['post_count']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
