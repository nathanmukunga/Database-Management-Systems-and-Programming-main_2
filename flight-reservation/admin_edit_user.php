<?php
session_start();
require_once 'includes/db.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Vérifie qu'on a bien l'ID
if (!isset($_GET['id'])) {
    header("Location: admin_view_users.php");
    exit();
}

$user_id = $_GET['id'];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE Users SET username = ?, email = ?, role = ? WHERE user_id = ?");
    $stmt->execute([$username, $email, $role, $user_id]);

    header("Location: admin_view_users.php?updated=1");
    exit();
}

// Récupère les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur introuvable.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="admin_view_users.php" class="btn btn-primary mt-3">view users</a><br/><br/>
    <h2>Edit User</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Full Name</label>
            <input type="text" name="username" id="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-select" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="admin_view_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
