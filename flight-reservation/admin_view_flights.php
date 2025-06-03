<?php
session_start();
require_once 'includes/db.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Supprimer un vol
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Supprimer les réservations associées à ce vol avant de supprimer le vol
    $pdo->prepare("DELETE FROM Bookings WHERE flight_id = ?")->execute([$delete_id]);
    // Maintenant, supprimer le vol
    $stmt = $pdo->prepare("DELETE FROM Flights WHERE flight_id = ?");
    $stmt->execute([$delete_id]);
    header("Location: admin_view_flights.php?deleted=1");
    exit();
}

// Récupère la liste des vols
$stmt = $pdo->query("SELECT * FROM Flights");
$flights = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Flights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">View Flights</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Flight deleted successfully!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Flight ID</th>
                <th>Flight Number</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flights as $flight): ?>
                <tr>
                    <td><?= htmlspecialchars($flight['flight_id']) ?></td>
                    <td><?= htmlspecialchars($flight['flight_number']) ?></td>
                    <td><?= htmlspecialchars($flight['departure_time']) ?></td>
                    <td><?= htmlspecialchars($flight['arrival_time']) ?></td>
                    <td>
                        <a href="admin_edit_flight.php?id=<?= $flight['flight_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="admin_view_flights.php?delete_id=<?= $flight['flight_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this flight?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
