<?php
session_start();
include "config.php"; // Your DB connection file

// Check login
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch student details
$sql = "SELECT * FROM students WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<h3>No student record found for this account.</h3>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background-color: #f5f6fa;
  font-family: 'Poppins', sans-serif;
}
.dashboard {
  max-width: 900px;
  margin: 50px auto;
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
h2 {
  color: #007bff;
  margin-bottom: 20px;
}
.profile-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}
.logout-btn {
  float: right;
}
</style>
</head>
<body>

<div class="container dashboard">
  <div class="d-flex justify-content-between align-items-center">
    <h2>Welcome, <?php echo $student['firstname'] . " " . $student['lastname']; ?> 👋</h2>
    <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
  </div>
  <hr>
  <h4>Student Profile</h4>
  <div class="profile-info mt-3">
    <p><strong>Full Name:</strong> <?php echo $student['firstname']." ".$student['lastname']; ?></p>
    <p><strong>Age:</strong> <?php echo $student['age']; ?></p>
    <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
    <p><strong>Phone Number:</strong> <?php echo $student['phonenumber']; ?></p>
    <p><strong>Gender:</strong> <?php echo $student['gender']; ?></p>
    <p><strong>Languages:</strong> <?php echo $student['languages']; ?></p>
    <p><strong>Degree:</strong> <?php echo $student['degree']; ?></p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
