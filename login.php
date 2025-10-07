<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // Using md5 to match your current DB

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check account status
        if ($user['status'] !== 'active') {
            echo "<script>alert('Account not active. Please wait for admin approval.');window.location='login.html';</script>";
            exit;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit;
        } elseif ($user['role'] === 'student') {
            header("Location: student_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid user role.');window.location='login.html';</script>";
        }

    } else {
        echo "<script>alert('Invalid Email or Password');window.location='login.html';</script>";
    }
}
?>
