<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

$success = $error = "";

// Récupérer les aéroports pour les menus déroulants
$stmt = $pdo->query("SELECT airport_id, airport_name, city, country FROM Airports ORDER BY city");
$airports = $stmt->fetchAll();

// Ajouter un vol
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $flight_number = $_POST["flight_number"];
    $origin = $_POST["origin_airport_id"];
    $destination = $_POST["destination_airport_id"];
    $departure = $_POST["departure_time"];
    $arrival = $_POST["arrival_time"];
    $price = $_POST["price"];

    // Vérifier que l'aéroport d'origine et de destination sont différents
    if ($origin === $destination) {
        $error = "Origin and destination airports must be different.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Flights (flight_number, origin_airport_id, destination_airport_id, departure_time, arrival_time, price) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$flight_number, $origin, $destination, $departure, $arrival, $price]);
            $success = "Flight added successfully.";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Flight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">Dashboard</a><br/><br/>
    <h2>Add New Flight</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Flight Number</label>
            <input type="text" name="flight_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Origin Airport</label>
            <select name="origin_airport_id" class="form-select" required>
                <option value="">Select origin</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport["airport_id"] ?>">
                        <?= htmlspecialchars($airport["airport_name"] . " - " . $airport["city"] . ", " . $airport["country"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Destination Airport</label>
            <select name="destination_airport_id" class="form-select" required>
                <option value="">Select destination</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport["airport_id"] ?>">
                        <?= htmlspecialchars($airport["airport_name"] . " - " . $airport["city"] . ", " . $airport["country"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Departure Time</label>
            <input type="datetime-local" name="departure_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Arrival Time</label>
            <input type="datetime-local" name="arrival_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Price ($)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Flight</button>
    </form>
</div>
</body>
</html>
