<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WriteIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #333;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        nav{
            padding-left: 2rem;
            padding-right: 2rem;
        }

        nav a {
            color: white;
            margin-left: 3rem;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <a href="/index.php" class="brand">WriteIT</a>
    <nav>
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2): ?>
        <!-- <span>Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span> -->
        <a href="/index.php">Home</a>
            <a href="/profile.php">Profile</a>
            <a href="/logout.php">Logout</a>
       
        <?php else: ?>
            <a href="/auth/login.php">Login</a>
            <a href="/auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
