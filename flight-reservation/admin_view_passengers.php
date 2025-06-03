<?php
session_start();
require_once 'includes/db.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Supprimer un passager
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM Passengers WHERE passenger_id = ?");
    $stmt->execute([$delete_id]);
    header("Location: admin_view_passengers.php?deleted=1");
    exit();
}

// Récupère la liste des passagers
$stmt = $pdo->query("SELECT * FROM Passengers");
$passengers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Passengers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">View Passengers</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Passenger deleted successfully!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Passenger ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($passengers as $passenger): ?>
                <tr>
                    <td><?= htmlspecialchars($passenger['passenger_id']) ?></td>
                    <td><?= htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']) ?></td>
                    <td><?= htmlspecialchars($passenger['email']) ?></td>
                    <td><?= htmlspecialchars($passenger['phone_number']) ?></td>
                    <td>
                        <a href="edit_passenger.php?id=<?= $passenger['passenger_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="admin_view_passengers.php?delete_id=<?= $passenger['passenger_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this passenger?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
