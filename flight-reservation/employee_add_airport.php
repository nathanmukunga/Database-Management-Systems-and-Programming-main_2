<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'employee') {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["airport_name"];
    $city = $_POST["city"];
    $country = $_POST["country"];

    $stmt = $pdo->prepare("INSERT INTO Airports (airport_name, city, country) VALUES (?, ?, ?)");
    $stmt->execute([$name, $city, $country]);
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Airport - Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">Add New Airport</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">✅ Airport added successfully!</div>
    <?php endif; ?>

    <form method="POST" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label for="airport_name" class="form-label">Airport Name</label>
            <input type="text" class="form-control" name="airport_name" required>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" name="city" required>
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" class="form-control" name="country" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Airport</button>
    </form>
</div>
</body>
</html>
