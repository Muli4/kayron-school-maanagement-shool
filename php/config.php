<?php
session_start();
include 'db.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hash the entered password using SHA-256 (must match how it was stored)
    $hashed_password = hash('sha256', $password);

    // Prepare statement to fetch hashed password
    $stmt = $conn->prepare("SELECT password FROM administration WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Bind the result to a variable
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Compare hashed passwords (entered vs stored)
        if ($hashed_password === $db_password) {
            $_SESSION['username'] = $username;
            $_SESSION['message'] = "";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "<div class='error-message'>Incorrect Password!</div>";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['message'] ="<div class='error-message'>Username not found!</div>";
        header("Location: ../index.php");
        exit();
    }

    // Close statement and connection
    $stmt->close();
}
$conn->close();
?>
