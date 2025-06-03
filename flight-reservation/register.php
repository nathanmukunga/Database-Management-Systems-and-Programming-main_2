<?php
    require_once 'includes/db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $email = $_POST["email"];
        $phone = $_POST["phone_number"];
        $role = $_POST["role"];

        $stmt = $pdo->prepare("INSERT INTO Users (username, password, role, email, phone_number) 
                               VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $password, $role, $email, $phone]);
            header("Location: login.php?success=1");
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Create an account</h2>
    <form action="" method="post">
        <div class="mb-3">
            <label>User name</label>
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
            <label>Phone number</label>
            <input type="text" name="phone_number" class="form-control">
        </div>
        <!--HIDEEN INPUT-->
        <input type="hidden" name="role" value="customer">
        <button type="submit" class="btn btn-primary">Register</button>
        <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>
</body>
</html>
