<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["flight_id"])) {
    die("Vol non spécifié.");
}

$flight_id = $_GET["flight_id"];

// Obtenir infos du vol
$stmt = $pdo->prepare("SELECT * FROM Flights WHERE flight_id = ?");
$stmt->execute([$flight_id]);
$flight = $stmt->fetch();

if (!$flight) {
    die("Vol introuvable.");
}

// Obtenir le passager
$stmt = $pdo->prepare("SELECT passenger_id FROM Passengers WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$passenger = $stmt->fetch();

if (!$passenger) {
    header("Location: complete_profile.php");
    exit();
}

// Paiement et réservation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $method = $_POST["payment_method"];
    $amount = $flight["price"];
    $airport_id = $flight["origin_airport_id"];

    // 1. Créer la réservation
    $stmt = $pdo->prepare("INSERT INTO Bookings (passenger_id, flight_id, airport_id, total_amount) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$passenger["passenger_id"], $flight_id, $airport_id, $amount]);
    $booking_id = $pdo->lastInsertId();

    // 2. Enregistrer le paiement
    $stmt = $pdo->prepare("INSERT INTO Payments (booking_id, payment_method, amount, payment_status) 
                           VALUES (?, ?, ?, 'completed')");
    $stmt->execute([$booking_id, $method, $amount]);

    header("Location: booking_success.php?booking_id=" . $booking_id);
    exit();
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Paiement du vol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Payment for the flight <?= htmlspecialchars($flight['flight_number']) ?></h3>
    <p><strong>Amount :</strong> <?= htmlspecialchars($flight['price']) ?> $</p>

    <form method="POST">
        <div class="mb-3">
            <label>Payment method :</label>
            <select name="payment_method" class="form-control" required>
                <option value="Credit card">Credit card</option>
                <option value="PayPal">PayPal</option>
                <option value="Cash">Cash</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Pay and reserve</button>
    </form>
</div>
</body>
</html>
