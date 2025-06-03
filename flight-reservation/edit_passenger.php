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
    header("Location: admin_view_passengers.php");
    exit();
}

$passenger_id = $_GET['id'];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $stmt = $pdo->prepare("UPDATE Passengers SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE passenger_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone_number, $passenger_id]);

    header("Location: admin_view_passengers.php?updated=1");
    exit();
}

// Récupère les infos du passager
$stmt = $pdo->prepare("SELECT * FROM Passengers WHERE passenger_id = ?");
$stmt->execute([$passenger_id]);
$passenger = $stmt->fetch();

if (!$passenger) {
    echo "Passenger not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Passenger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="admin_view_passengers.php" class="btn btn-primary mt-3">view passengers</a><br/><br/>
    <h2>Edit Passenger</h2>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required value="<?= htmlspecialchars($passenger['first_name']) ?>">
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required value="<?= htmlspecialchars($passenger['last_name']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($passenger['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" required value="<?= htmlspecialchars($passenger['phone_number']) ?>">
        </div>

        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="admin_view_passengers.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
