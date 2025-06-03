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
    header("Location: admin_view_flights.php");
    exit();
}

$flight_id = $_GET['id'];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_number = $_POST['flight_number'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $origin_airport_id = $_POST['origin_airport_id'];
    $destination_airport_id = $_POST['destination_airport_id'];

    $stmt = $pdo->prepare("UPDATE Flights SET flight_number = ?, departure_time = ?, arrival_time = ?, origin_airport_id = ?, destination_airport_id = ? WHERE flight_id = ?");
    $stmt->execute([$flight_number, $departure_time, $arrival_time, $origin_airport_id, $destination_airport_id, $flight_id]);

    header("Location: admin_view_flights.php?updated=1");
    exit();
}

// Récupère les infos du vol
$stmt = $pdo->prepare("SELECT * FROM Flights WHERE flight_id = ?");
$stmt->execute([$flight_id]);
$flight = $stmt->fetch();

if (!$flight) {
    echo "Flight not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Flight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="admin_view_flights.php" class="btn btn-primary mt-3">View flights</a><br/><br/>
    <h2>Edit Flight</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="flight_number" class="form-label">Flight Number</label>
            <input type="text" name="flight_number" id="flight_number" class="form-control" required value="<?= htmlspecialchars($flight['flight_number']) ?>">
        </div>

        <div class="mb-3">
            <label for="departure_time" class="form-label">Departure Time</label>
            <input type="text" name="departure_time" id="departure_time" class="form-control" required value="<?= htmlspecialchars($flight['departure_time']) ?>">
        </div>

        <div class="mb-3">
            <label for="arrival_time" class="form-label">Arrival Time</label>
            <input type="text" name="arrival_time" id ="arrival_time" class="form-control" required value="<?= htmlspecialchars($flight['arrival_time']) ?>">
        </div>
        <div class="mb-3">
            <label for="origin_airport_id" class="form-label">Origin Airport ID</label>
            <input type="number" name="origin_airport_id" id="origin_airport_id" class="form-control" required value="<?= htmlspecialchars($flight['origin_airport_id']) ?>">
        </div>

        <div class="mb-3">
            <label for="destination_airport_id" class="form-label">Destination Airport ID</label>
            <input type="number" name="destination_airport_id" id="destination_airport_id" class="form-control" required value="<?= htmlspecialchars($flight['destination_airport_id']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Flight</button>
    </form>

