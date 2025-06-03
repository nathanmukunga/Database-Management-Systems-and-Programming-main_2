<?php
session_start();
require_once 'includes/db.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Gestion de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
    $stmt->execute([$_POST['delete_user_id']]);
    header("Location: admin_view_users.php?deleted=1");
    exit();
}

// Statistiques
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM Users GROUP BY role");
$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Utilisateurs
$stmt = $pdo->query("SELECT user_id, username, email, role, created_at FROM Users ORDER BY user_id DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-box {
            padding: 20px;
            border-radius: 8px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }
        .admin { background-color: #343a40; }
        .customer { background-color: #0d6efd; }
        .employee { background-color: #198754; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">Dashboard</a><br/><br/>
    <h2 class="mb-4">Users Overview</h2>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert alert-warning">User successfully deleted.</div>
    <?php endif; ?>

    <div class="row text-white">
        <div class="col-md-4">
            <div class="stat-box admin">
                <h4>Admins</h4>
                <p class="fs-3"><?= $stats['admin'] ?? 0 ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box customer">
                <h4>Customers</h4>
                <p class="fs-3"><?= $stats['customer'] ?? 0 ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box employee">
                <h4>Employees</h4>
                <p class="fs-3"><?= $stats['employee'] ?? 0 ?></p>
            </div>
        </div>
    </div>

    <h3 class="mt-5 mb-3">All Users</h3>

    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['username'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                    <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <a href="admin_edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="delete_user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
