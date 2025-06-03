<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Flight ID Verification
if (!isset($_GET["flight_id"])) {
    die("Flight not specified.");
}

$flight_id = $_GET["flight_id"];

// Get flight information
$stmt = $pdo->prepare("SELECT * FROM Flights WHERE flight_id = ?");
$stmt->execute([$flight_id]);
$flight = $stmt->fetch();

if (!$flight) {
    die("Flight not found.");
}

// Check that the user has a passenger profile
$stmt = $pdo->prepare("SELECT passenger_id FROM Passengers WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$passenger = $stmt->fetch();

if (!$passenger) {
    header("Location: complete_profile.php");
    exit();
}

// If the user confirms, he is redirected to payment.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Location: payment.php?flight_id=" . $flight_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver un vol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>✈️ Reservation confirmation</h2>
    <div class="card mt-3">
        <div class="card-body">
            <p><strong>Flight number :</strong> <?= htmlspecialchars($flight['flight_number']) ?></p>
            <p><strong>Departure :</strong> <?= htmlspecialchars($flight['departure_time']) ?></p>
            <p><strong>Arrival :</strong> <?= htmlspecialchars($flight['arrival_time']) ?></p>
            <p><strong>Price :</strong> <?= htmlspecialchars($flight['price']) ?> $</p>

            <form method="POST">
                <button type="submit" class="btn btn-primary">Continue to pay</button>
                <a href="search_flights.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>