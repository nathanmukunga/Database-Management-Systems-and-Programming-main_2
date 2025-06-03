<?php
    session_start();
    require_once 'includes/db.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Check if the passenger profile already exists
    $stmt = $pdo->prepare("SELECT * FROM Passengers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $passenger = $stmt->fetch();

    if ($passenger) {
        header("Location: dashboard.php");
        exit();
    }

    // Form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $passport_number = $_POST["passport_number"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];

        $stmt = $pdo->prepare("INSERT INTO Passengers (user_id, first_name, last_name, passport_number, phone_number, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $first_name, $last_name, $passport_number, $phone, $email]);

        header("Location: book_flight.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Complete the profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Complete your passenger profile ✈️</h2>
    <form method="POST">
        <div class="mb-3">
            <label>First name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Passport number</label>
            <input type="text" name="passport_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone number</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
</body>
</html>
