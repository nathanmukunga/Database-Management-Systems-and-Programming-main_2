<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'employee') {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$success = false;

// Récupère les aéroports pour les menus déroulants
$airports = $pdo->query("SELECT airport_id, airport_name FROM Airports")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $flight_number = $_POST["flight_number"];
    $origin_id = $_POST["origin_airport_id"];
    $destination_id = $_POST["destination_airport_id"];
    $departure = $_POST["departure_time"];
    $arrival = $_POST["arrival_time"];

    $stmt = $pdo->prepare("INSERT INTO Flights (flight_number, origin_airport_id, destination_airport_id, departure_time, arrival_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$flight_number, $origin_id, $destination_id, $departure, $arrival]);
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Flight - Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">Add New Flight</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">✈️ Flight added successfully!</div>
    <?php endif; ?>

    <form method="POST" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label class="form-label">Flight Number</label>
            <input type="text" name="flight_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Origin Airport</label>
            <select name="origin_airport_id" class="form-select" required>
                <option value="">Select origin</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport["airport_id"] ?>"><?= htmlspecialchars($airport["airport_name"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Destination Airport</label>
            <select name="destination_airport_id" class="form-select" required>
                <option value="">Select destination</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport["airport_id"] ?>"><?= htmlspecialchars($airport["airport_name"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Departure Time</label>
            <input type="datetime-local" name="departure_time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Arrival Time</label>
            <input type="datetime-local" name="arrival_time" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Flight</button>
    </form>
</div>
</body>
</html>
