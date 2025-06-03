<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'employee') {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$stmt = $pdo->query("
    SELECT f.flight_id, f.flight_number, f.departure_time, f.arrival_time,
           a1.airport_name AS origin, a2.airport_name AS destination
    FROM Flights f
    JOIN Airports a1 ON f.origin_airport_id = a1.airport_id
    JOIN Airports a2 ON f.destination_airport_id = a2.airport_id
    ORDER BY f.departure_time DESC
");
$flights = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Flights - Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">Flights Overview</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Flight No</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Departure</th>
                <th>Arrival</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flights as $flight): ?>
                <tr>
                    <td><?= htmlspecialchars($flight["flight_number"]) ?></td>
                    <td><?= htmlspecialchars($flight["origin"]) ?></td>
                    <td><?= htmlspecialchars($flight["destination"]) ?></td>
                    <td><?= htmlspecialchars($flight["departure_time"]) ?></td>
                    <td><?= htmlspecialchars($flight["arrival_time"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
