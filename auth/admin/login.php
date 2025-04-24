<?php
require '../../db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required.";
    }

    if (empty($errors)) {
    
        $stmt = $connection->prepare("SELECT user_id, email, password, role_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
       
            if (password_verify($password, $user['password'])) {
             
                if ($user['role_id'] == 1) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];

                    header('Location: ../../admin_dashboard.php');
                    exit;
                } else {
                    $errors[] = "Only Admins can log in here.";
                }
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No admin account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - WriteIT</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #fff;
            padding: 30px;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: 374px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 2rem;
        }

        button:hover {
            background-color: #218838;
        }

        .error-box {
            background-color: #ffe6e6;
            color: #d8000c;
            border-left: 4px solid #d8000c;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Admin Login</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
            <p style="text-align: center;">Don't have an account? <a href="/auth/admin/register.php">Register</a></p>
        </form>
    </div>
</body>
</html>
