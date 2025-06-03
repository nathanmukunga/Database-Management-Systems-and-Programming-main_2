<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

// Retrieve the list of airports for selects
$airports = $pdo->query("SELECT airport_id, airport_name, city FROM Airports")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher un vol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2>🔍 Search for a flight</h2>
    <form method="GET" action="search_flights.php" class="row g-3">
        <div class="col-md-4">
            <label for="origin" class="form-label">Departure airport</label>
            <select name="origin" id="origin" class="form-select" required>
                <option value="">Choose...</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport['airport_id'] ?>">
                        <?= htmlspecialchars($airport['airport_name']) ?> (<?= $airport['city'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="destination" class="form-label">Arrival airport</label>
            <select name="destination" id="destination" class="form-select" required>
                <option value="">Choose...</option>
                <?php foreach ($airports as $airport): ?>
                    <option value="<?= $airport['airport_id'] ?>">
                        <?= htmlspecialchars($airport['airport_name']) ?> (<?= $airport['city'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Departure date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">research</button>
        </div>
    </form>

    <?php
    // Search result
    if (isset($_GET['origin'], $_GET['destination'], $_GET['date'])) {
        $origin = $_GET['origin'];
        $destination = $_GET['destination'];
        $date = $_GET['date'];

        $stmt = $pdo->prepare("
            SELECT * FROM Flights 
            WHERE origin_airport_id = ? AND destination_airport_id = ?
            AND DATE(departure_time) = ?
        ");
        $stmt->execute([$origin, $destination, $date]);
        $flights = $stmt->fetchAll();

        echo "<hr><h4>Search results :</h4>";
        if (count($flights) === 0) {
            echo "<p>No flights found.</p>";
        } else {
            echo '<table class="table table-bordered">';
            echo '<thead><tr>
                    <th>Flight number</th>
                    <th>Departue</th>
                    <th>Arrival</th>
                    <th>Price</th>
                    <th>Action</th>
                  </tr></thead><tbody>';
            foreach ($flights as $flight) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($flight['flight_number']) . '</td>';
                echo '<td>' . htmlspecialchars($flight['departure_time']) . '</td>';
                echo '<td>' . htmlspecialchars($flight['arrival_time']) . '</td>';
                echo '<td>' . htmlspecialchars($flight['price']) . ' $</td>';
                echo '<td><a href="book_flight.php?flight_id=' . $flight['flight_id'] . '" class="btn btn-success">book</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
    }
    ?>
</div>
</body>
</html>
