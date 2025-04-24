<?php
session_start();
include 'header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;

}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_SESSION['user_id'];
    $firstName = $_SESSION['first_name'];
    $lastName = $_SESSION['last_name'];
    $userFullName = $firstName . ' ' . $lastName;
    $userEmail = $_SESSION['email'];
    $initial = strtoupper(substr($firstName, 0, 1));

    $stmt = $connection->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "User not found.";
        exit;
    }

    $user = $result->fetch_assoc();
    $userCreationDate = $user['created_at'] ?? null;
    $status = $userCreationDate ? "Active" : "Inactive";
} else {
    echo "Invalid request method.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        .profile-container {
            margin-top: 60px;
            display: flex;
            justify-content: center;
        }

        .profile-card {
            width: 360px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 30px 25px;
            text-align: center;
        }

        .avatar {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px auto;
            background-color: #007bff;
            color: white;
            font-size: 36px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .name {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }

        .email {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 10px 0;
        }

        .info-row p {
            margin: 0;
        }

        hr {
            margin: 20px 0;
            border: 0;
            height: 1px;
            background-color: #eee;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="avatar"><?php echo $initial; ?></div>
            <div class="name"><?php echo htmlspecialchars($userFullName); ?></div>
            <div class="email"><?php echo htmlspecialchars($userEmail); ?></div>
            <hr>
            <div class="info-row">
                <p>Member since:</p>
                <p><?php echo $userCreationDate ? htmlspecialchars(date('F j, Y', strtotime($userCreationDate))) : 'N/A'; ?></p>
            </div>
            <div class="info-row">
                <p>Status:</p>
                <p><?php echo htmlspecialchars($status); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
