<?php
session_start();
include "config.php";

// Check if admin is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.html");
    exit;
}

// Handle activate/deactivate/delete actions
if(isset($_GET['action'], $_GET['id'])){
    $id = (int)$_GET['id'];
    if($_GET['action'] == 'activate'){
        $conn->query("UPDATE users SET status='active' WHERE id=$id");
    } elseif($_GET['action'] == 'deactivate'){
        $conn->query("UPDATE users SET status='pending' WHERE id=$id");
    } elseif($_GET['action'] == 'delete'){
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch counts for cards
$totalStudents = $conn->query("SELECT * FROM users WHERE role='student'")->num_rows;
$activeUsers = $conn->query("SELECT * FROM users WHERE status='active'")->num_rows;
$pendingRequests = $conn->query("SELECT * FROM users WHERE status='pending'")->num_rows;

// Fetch all students
$result = $conn->query("SELECT * FROM users WHERE role='student' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .sidebar { height: 100vh; background: #343a40; color: white; padding-top: 20px; }
    .sidebar a { color: white; display: block; padding: 10px 15px; text-decoration: none; }
    .sidebar a:hover { background: #495057; }
    .content { padding: 20px; }
    table th, table td { vertical-align: middle !important; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 sidebar">
      <h3 class="text-center">Admin</h3>
      <a href="#">Dashboard</a>
      <a href="student_add.html">Add Student </a>
      <a href="#manageStudents">Manage Students</a>
      <a href="#">Settings</a>
      <a href="logout.php" class="text-danger">Logout</a>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 col-lg-10 content">
      <h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>

      <!-- Cards -->
      <div class="row my-4">
        <div class="col-md-4">
          <div class="card text-bg-primary mb-3">
            <div class="card-body">
              <h5 class="card-title">Total Students</h5>
              <p class="card-text fs-4"><?php echo $totalStudents; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-bg-success mb-3">
            <div class="card-body">
              <h5 class="card-title">Active Users</h5>
              <p class="card-text fs-4"><?php echo $activeUsers; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-bg-warning mb-3">
            <div class="card-body">
              <h5 class="card-title">Pending Requests</h5>
              <p class="card-text fs-4"><?php echo $pendingRequests; ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Student Form -->
      <h4 id="addUserForm">Add New Student/User</h4>
      <form action="add_user.php" method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-3">
            <input type="text" name="name" class="form-control" placeholder="Name" required>
          </div>
          <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
          </div>
          <div class="col-md-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <div class="col-md-2">
            <select name="role" class="form-select" required>
              <option value="student" selected>Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Add</button>
          </div>
        </div>
      </form>

      <!-- Manage Students Table -->
      <h4 id="manageStudents">Recent Students</h4>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($user = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td>
              <?php if($user['status'] == 'active'): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($user['status']=='active'): ?>
                <a href="?action=deactivate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Deactivate</a>
              <?php else: ?>
                <a href="?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Activate</a>
              <?php endif; ?>
              <a href="?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </main>
  </div>
</div>
</body>
</html>
