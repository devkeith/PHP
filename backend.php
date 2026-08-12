<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nacit";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $identity = mysqli_real_escape_string($conn, $_POST['identity']);
    $login_password = $_POST['password'];

    // Dynamically query against either field structure
    $sql = "SELECT * FROM level_five_first_semister WHERE email_address = '$identity' OR student_id = '$identity' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($login_password, $user['password_one'])) {
            $_SESSION['user_id'] = $user['student_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $conn->close();
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid password entry. Access Denied.";
        }
    } else {
        $_SESSION['error'] = "No student profile found matching that ID or Email.";
    }
    
    $conn->close();
    header("Location: dash_board.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
