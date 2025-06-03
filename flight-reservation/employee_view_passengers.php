<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'employee') {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';

$stmt = $pdo->query("SELECT * FROM Passengers ORDER BY passenger_id DESC");
$passengers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Passengers - Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-primary mt-3">dashboard</a><br/><br/>
    <h2 class="mb-4">Passengers List</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Passport No</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($passengers as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p["passenger_id"]) ?></td>
                    <td><?= htmlspecialchars($p["first_name"] . ' ' . $p["last_name"]) ?></td>
                    <td><?= htmlspecialchars($p["passport_number"]) ?></td>
                    <td><?= htmlspecialchars($p["phone_number"]) ?></td>
                    <td><?= htmlspecialchars($p["email"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
