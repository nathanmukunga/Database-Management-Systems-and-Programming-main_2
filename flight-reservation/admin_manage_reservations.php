<?php
session_start();
require_once 'includes/db.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Gestion du changement de statut
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"], $_POST["status"])) {
    $stmt = $pdo->prepare("UPDATE Bookings SET booking_status = ? WHERE booking_id = ?");
    $stmt->execute([$_POST["status"], $_POST["booking_id"]]);
    header("Location: admin_manage_reservations.php?success=1");
    exit();
}

// Récupère les réservations avec jointures
$stmt = $pdo->query("
    SELECT 
        b.booking_id, b.total_amount, b.booking_status, 
        f.flight_number, f.departure_time, f.arrival_time,
        a1.airport_name AS origin_name, a2.airport_name AS destination_name,
        CONCAT(p.first_name, ' ', p.last_name) AS full_name
    FROM Bookings b
    JOIN Flights f ON b.flight_id = f.flight_id
    JOIN Airports a1 ON f.origin_airport_id = a1.airport_id
    JOIN Airports a2 ON f.destination_airport_id = a2.airport_id
    JOIN Passengers p ON b.passenger_id = p.passenger_id
    ORDER BY b.booking_id DESC
");

$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">Dashboard</a><br/><br/>
    <h2 class="mb-4">Manage Reservations</h2>

    <?php if (isset($_GET["success"])): ?>
        <div class="alert alert-success">Status updated successfully!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Passenger</th>
                <th>Flight No</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Amount ($)</th>
                <th>Status</th>
                <th>Change</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res["booking_id"]) ?></td>
                    <td><?= htmlspecialchars($res["full_name"]) ?></td>
                    <td><?= htmlspecialchars($res["flight_number"]) ?></td>
                    <td><?= htmlspecialchars($res["origin_name"]) ?> <br><small><?= $res["departure_time"] ?></small></td>
                    <td><?= htmlspecialchars($res["destination_name"]) ?> <br><small><?= $res["arrival_time"] ?></small></td>
                    <td><?= htmlspecialchars($res["total_amount"]) ?></td>
                    <td><?= htmlspecialchars($res["booking_status"] ?? 'Pending') ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?= $res["booking_id"] ?>">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option <?= ($res["booking_status"] === "Pending" ? "selected" : "") ?>>Pending</option>
                                <option <?= ($res["booking_status"] === "Confirmed" ? "selected" : "") ?>>Confirmed</option>
                                <option <?= ($res["booking_status"] === "Cancelled" ? "selected" : "") ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
