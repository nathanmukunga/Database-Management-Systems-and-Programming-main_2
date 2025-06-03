<?php
session_start();
require_once 'includes/db.php';

// Access restriction
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Get passenger ID linked to current user
$stmt = $pdo->prepare("SELECT passenger_id FROM Passengers WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$passenger = $stmt->fetch();

if (!$passenger) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>My Reservations</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
        <h2 class="mb-4">My Reservations</h2>
        <div class="alert alert-info">You have no reservations yet. Once you book a flight, your reservations will appear here.</div>
        <a href="search_flights.php" class="btn btn-primary mt-3">Search Flights</a>
    </div>
    </body>
    </html>';
    exit();
}


$passenger_id = $passenger["passenger_id"];

// Fetch all bookings
$stmt = $pdo->prepare("
    SELECT b.booking_id, b.total_amount, f.flight_number, f.departure_time, f.arrival_time,
           f.origin_airport_id, f.destination_airport_id, p.payment_status
    FROM Bookings b
    JOIN Flights f ON b.flight_id = f.flight_id
    JOIN Payments p ON b.booking_id = p.booking_id
    WHERE b.passenger_id = ?
    ORDER BY f.departure_time DESC
");
$stmt->execute([$passenger_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">My Reservations</h2>

    <?php if (count($reservations) === 0): ?>
        <div class="alert alert-info">You have no reservations yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Booking ID</th>
                    <th>Flight</th>
                    <th>From → To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Total Paid</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= htmlspecialchars($res["booking_id"]) ?></td>
                        <td><?= htmlspecialchars($res["flight_number"]) ?></td>
                        <td><?= htmlspecialchars($res["origin_airport_id"]) ?> → <?= htmlspecialchars($res["destination_airport_id"]) ?></td>
                        <td><?= htmlspecialchars($res["departure_time"]) ?></td>
                        <td><?= htmlspecialchars($res["arrival_time"]) ?></td>
                        <td>$<?= htmlspecialchars($res["total_amount"]) ?></td>
                        <td>
                            <span class="badge <?= $res["payment_status"] === 'completed' ? 'bg-success' : 'bg-warning' ?>">
                                <?= htmlspecialchars(ucfirst($res["payment_status"])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
