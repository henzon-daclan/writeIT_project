<?php
require '../db.php';

$firstName = $lastName = $email = $password = '';
$roleId = 2; 
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($firstName)) {
        $errors[] = 'First name is required.';
    }
    if (empty($lastName)) {
        $errors[] = 'Last name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    $stmt = $connection->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email already registered.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $roleId);

        if ($stmt->execute()) {
            header('Location: login.php'); 
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - WriteIT</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #fff;
            width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .card label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .card input {
            width: 374px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
         
        }

        .card button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 2rem;
        }

        .card button:hover {
            background-color: #0056b3;
        }

        .errors {
            background-color: #ffe6e6;
            color: #b30000;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Register</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($firstName) ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($lastName) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
            <p style="text-align: center;">Already have an account? <a href="/auth/login.php">Log In</a></p>
        </form>
    </div>
</body>
</html>
