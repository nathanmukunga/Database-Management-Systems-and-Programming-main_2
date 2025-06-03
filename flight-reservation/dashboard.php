<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Récupérer les infos de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT username FROM Users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">✈️ Flight System</a>
        <div class="d-flex">
            <span class="text-white me-3">Welcome, <?= htmlspecialchars($user['username']) ?> (<?= ucfirst($role) ?>)</span>
            <a href="logout.php" class="btn btn-outline-light">Disconnect</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Dashboard - <?= ucfirst($role) ?></h2>

    <?php if ($role === 'admin'): ?>
        <div class="alert alert-info text-center">👨‍✈️ You are logged in as administrator.</div>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="admin_add_airport.php" class="btn btn-primary w-100">➕ Add Airport</a>
            </div>
            <div class="col-md-4">
                <a href="admin_add_flight.php" class="btn btn-primary w-100">✈️ Add Flight</a>
            </div>
            <div class="col-md-4">
                <a href="admin_view_flights.php" class="btn btn-secondary w-100">📋 View Flights</a>
            </div>
            <div class="col-md-4">
                <a href="admin_manage_reservations.php" class="btn btn-warning w-100">🗂️ Manage Reservations</a>
            </div>
            <div class="col-md-4">
                <a href="admin_view_users.php" class="btn btn-info w-100">👥 View Users</a>
            </div>
            <div class="col-md-4">
                <a href="admin_view_passengers.php" class="btn btn-dark w-100">🧍‍♂️ View Passengers</a>
            </div>
        </div>

    <?php elseif ($role === 'employee'): ?>
        <div class="alert alert-warning text-center">🛠️ You are logged in as employee.</div>
        <div class="row g-3">
            <div class="col-md-6">
                <a href="employee_add_airport.php" class="btn btn-primary w-100">➕ Add Airport</a>
            </div>
            <div class="col-md-6">
                <a href="employee_add_flight.php" class="btn btn-primary w-100">✈️ Add Flight</a>
            </div>
            <div class="col-md-6">
                <a href="employee_view_flights.php" class="btn btn-secondary w-100">✈️ View Flights</a>
            </div>
            <div class="col-md-6">
                <a href="employee_view_passengers.php" class="btn btn-info w-100">🧍‍♂️ View Passengers</a>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-success text-center">🧳 You are logged in as a customer.</div>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="search_flights.php" class="btn btn-primary w-100">🔍 Search Flights</a>
            </div>
            <div class="col-md-4">
                <a href="my_reservations.php" class="btn btn-secondary w-100">📅 My Reservations</a>
            </div>
            <div class="col-md-4">
                <a href="#" class="btn btn-outline-dark w-100">✏️ Edit My Profile</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
