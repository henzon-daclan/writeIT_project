<?php
require '../../db.php';

$email = $password = '';
$roleId = 1; // Admin role
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Email validation
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // Password validation
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    // Check for duplicate email
    if (empty($errors)) {
        $stmt = $connection->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        } else {
            // Insert new admin
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $connection->prepare("INSERT INTO users (email, password, role_id) VALUES (?, ?, ?)");
            $insert->bind_param("ssi", $email, $hashedPassword, $roleId);

            if ($insert->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>log in</a>.";
                $email = $password = ''; // Reset fields
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration - WriteIT</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 374px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
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

        .error-box,
        .success-box {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .error-box {
            background-color: #ffe6e6;
            color: #d8000c;
            border-left: 5px solid #d8000c;
        }

        .success-box {
            background-color: #e6ffed;
            color: #155724;
            border-left: 5px solid #28a745;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Register Admin</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <button type="submit">Register</button>
        <p style="text-align: center;">Already have an account? <a href="/auth/admin/login.php">Log In</a></p>
    </form>
</div>

</body>
</html>
