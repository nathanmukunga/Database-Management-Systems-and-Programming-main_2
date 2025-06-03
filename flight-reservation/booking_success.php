<?php
    session_start();
    require_once 'includes/db.php';

    if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
        header("Location: login.php");
        exit();
    }

    if (!isset($_GET["booking_id"])) {
        die("Booking not specified.");
    }

    $booking_id = $_GET["booking_id"];

    // Retrieve booking info
    $stmt = $pdo->prepare("SELECT b.*, f.flight_number 
                           FROM Bookings b
                           JOIN Flights f ON b.flight_id = f.flight_id
                           WHERE b.booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die("Booking not found.");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 text-center">
    <h1 class="text-success">✅ Booking Confirmed!</h1>
    <p class="lead">Thank you for your payment.</p>
    <p><strong>Booking Number:</strong> <?= htmlspecialchars($booking_id) ?></p>
    <p><strong>Flight Number:</strong> <?= htmlspecialchars($booking['flight_number']) ?></p>
    <a href="dashboard.php" class="btn btn-primary mt-3">Dashboard</a>
</div>
</body>
</html>
