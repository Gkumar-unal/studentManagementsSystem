<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "config.php";

// DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    parse_str(file_get_contents("php://input"), $post_vars);
    if (!empty($post_vars['id'])) {
        $id = (int)$post_vars['id'];
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) echo "success";
        else echo "error";
        $stmt->close();
    } else {
        echo "no id";
    }
    exit; // important! stop further execution
}

// INSERT or UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check that required fields exist
    if (!isset($_POST['firstname'], $_POST['lastname'], $_POST['age'], $_POST['phonenumber'], $_POST['email'], $_POST['gender'], $_POST['degree'])) {
        exit("missing required fields");
    }

    $id = $_POST['id'] ?? '';
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $languages = isset($_POST['langs']) ? implode(',', $_POST['langs']) : '';
    $degree = $_POST['degree'];
    $photoData = '';

    if (isset($_FILES['fileupload']) && $_FILES['fileupload']['tmp_name'] != '') {
        $photoData = addslashes(file_get_contents($_FILES['fileupload']['tmp_name']));
    }

    // Duplicate check for insert
    if ($id === '') {
        $check = $conn->query("SELECT * FROM students WHERE phonenumber='$phonenumber' OR email='$email'");
        if ($check && $check->num_rows > 0) exit('already exist');
    }

    if ($id) {
        $sql = "UPDATE students SET 
            firstname='$firstname',
            lastname='$lastname',
            age='$age',
            phonenumber='$phonenumber',
            email='$email',
            gender='$gender',
            languages='$languages',
            degree='$degree'";
        if ($photoData) $sql .= ", photo='$photoData'";
        $sql .= " WHERE id=$id";
        $conn->query($sql);
        exit('success');
    } else {
        $sql = "INSERT INTO students 
            (firstname, lastname, age, phonenumber, email, gender, languages, degree, photo) 
            VALUES 
            ('$firstname','$lastname','$age','$phonenumber','$email','$gender','$languages','$degree','$photoData')";
        $conn->query($sql);
        exit('success');
    }
}

// FETCH
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['photo'])) $row['photo'] = base64_encode($row['photo']);
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

echo "Invalid request";
?>
