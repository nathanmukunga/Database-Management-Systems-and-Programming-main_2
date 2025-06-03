<?php
session_start();
require_once 'includes/db.php';

// 🔐 Security: Only admin can access this page
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied. This page is for administrators only.");
}

// Handle user creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = $_POST["email"];
    $phone = $_POST["phone_number"];
    $role = $_POST["role"]; // admin or employee

    $stmt = $pdo->prepare("INSERT INTO Users (username, password, role, email, phone_number) 
                           VALUES (?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $role, $email, $phone]);
        $message = "User created successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Create a New User (Admin or Employee)</h2>

    <?php if (isset($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

    <form action="" method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="employee">Employee</option>
                <option value="admin">Administrator</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create User</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
