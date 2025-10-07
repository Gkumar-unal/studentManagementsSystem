<?php
include "config.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // hashing
    $role = $_POST['role'];
    $status = ($role == 'admin') ? 'active' : 'pending';

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        echo "<script>alert('Email already exists!');window.location='register.html';</script>";
        exit;
    }

    $sql = "INSERT INTO users (name, email, password, role, status) VALUES ('$name','$email','$password','$role','$status')";
    if($conn->query($sql)){
        echo "<script>alert('Registration successful! Please login.');window.location='login.html';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');window.location='register.html';</script>";
    }
}
?>
