<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["airport_name"];
    $code = $_POST["airport_code"];
    $city = $_POST["city"];
    $country = $_POST["country"];

    try {
        $stmt = $pdo->prepare("INSERT INTO Airports (airport_name, airport_code, city, country) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $code, $city, $country]);
        $success = "Airport added successfully.";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Airport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-primary mt-3">Dashboard</a><br/><br/>
    <h2>Add New Airport</h2>

    <?php if (!empty($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Airport Name</label>
            <input type="text" name="airport_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Airport Code (ex: CDG)</label>
            <input type="text" name="airport_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>City</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Country</label>
            <input type="text" name="country" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Airport</button>
    </form>
</div>
</body>
</html>
