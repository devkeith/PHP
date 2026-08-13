<?php
session_start();

/*
|--------------------------------------------------------------------------
| NACIT PORTAL - SINGLE BACKEND
|--------------------------------------------------------------------------
| Handles:
|   1. Login
|   2. Student registration
|   3. Password reset
|--------------------------------------------------------------------------
*/

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "nacit";

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$action = $_POST["action"] ?? "";


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function redirect_with_error($message, $page) {
    $_SESSION["error"] = $message;
    header("Location: " . $page);
    exit();
}

function redirect_with_success($message, $page) {
    $_SESSION["success"] = $message;
    header("Location: " . $page);
    exit();
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($action === "login") {

    $identity = trim($_POST["identity"] ?? "");
    $login_password = $_POST["password"] ?? "";

    if ($identity === "" || $login_password === "") {
        redirect_with_error(
            "Please enter your Student ID/email and password.",
            "index.php"
        );
    }

    $stmt = $conn->prepare(
        "SELECT id, student_id, full_name, email, password, program,
                year_level, semester, status
         FROM students
         WHERE email = ? OR student_id = ?
         LIMIT 1"
    );

    $stmt->bind_param("ss", $identity, $identity);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();
        redirect_with_error(
            "No student account was found with that Student ID or email.",
            "index.php"
        );
    }

    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student["status"] !== "Active") {
        redirect_with_error(
            "Your student account is not currently active. Please contact the lecturer/administrator.",
            "index.php"
        );
    }

    if (!password_verify($login_password, $student["password"])) {
        redirect_with_error(
            "Invalid password. Access denied.",
            "index.php"
        );
    }

    session_regenerate_id(true);

    $_SESSION["student_db_id"] = $student["id"];
    $_SESSION["student_id"] = $student["student_id"];
    $_SESSION["student_name"] = $student["full_name"];
    $_SESSION["student_email"] = $student["email"];
    $_SESSION["student_program"] = $student["program"];
    $_SESSION["student_year"] = $student["year_level"];
    $_SESSION["student_semester"] = $student["semester"];

    $conn->close();

    header("Location: dash_board.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| REGISTRATION
|--------------------------------------------------------------------------
*/

if ($action === "register") {

    $full_name = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($full_name === "" || $email === "" || $password === "" || $confirm_password === "") {
        redirect_with_error(
            "Please complete all required registration fields.",
            "register.php"
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_error(
            "Please enter a valid email address.",
            "register.php"
        );
    }

    if (strlen($password) < 8) {
        redirect_with_error(
            "Password must contain at least 8 characters.",
            "register.php"
        );
    }

    if ($password !== $confirm_password) {
        redirect_with_error(
            "Passwords do not match.",
            "register.php"
        );
    }

    /*
    | Prevent duplicate email addresses.
    */
    $check = $conn->prepare(
        "SELECT id FROM students WHERE email = ? LIMIT 1"
    );

    $check->bind_param("s", $email);
    $check->execute();

    $existing = $check->get_result();

    if ($existing->num_rows > 0) {
        $check->close();

        redirect_with_error(
            "An account with that email address already exists.",
            "register.php"
        );
    }

    $check->close();


    /*
    | Generate Student ID automatically.
    | Example:
    | NACIT/2026/001
    | NACIT/2026/002
    */
    $current_year = date("Y");
    $prefix = "NACIT/" . $current_year . "/";

    $like_pattern = $prefix . "%";

    $id_stmt = $conn->prepare(
        "SELECT student_id
         FROM students
         WHERE student_id LIKE ?
         ORDER BY id DESC
         LIMIT 1"
    );

    $id_stmt->bind_param("s", $like_pattern);
    $id_stmt->execute();

    $id_result = $id_stmt->get_result();

    $next_number = 1;

    if ($id_result->num_rows > 0) {
        $last_student = $id_result->fetch_assoc();

        $last_id = $last_student["student_id"];

        $parts = explode("/", $last_id);

        if (count($parts) === 3 && is_numeric($parts[2])) {
            $next_number = ((int)$parts[2]) + 1;
        }
    }

    $id_stmt->close();

    $student_id = $prefix . str_pad($next_number, 3, "0", STR_PAD_LEFT);


    /*
    | Secure password hashing.
    */
    $password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /*
    | Default academic information.
    | These can later be changed by the lecturer/admin.
    */
    $program = "Advanced Diploma in Software Engineering";
    $year_level = 1;
    $semester = 1;
    $status = "Active";


    $insert = $conn->prepare(
        "INSERT INTO students
        (student_id, full_name, email, password, program,
         year_level, semester, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $insert->bind_param(
        "sssssiis",
        $student_id,
        $full_name,
        $email,
        $password_hash,
        $program,
        $year_level,
        $semester,
        $status
    );

    if (!$insert->execute()) {
        $insert->close();

        redirect_with_error(
            "Registration failed. Please try again.",
            "register.php"
        );
    }

    $insert->close();
    $conn->close();

    $_SESSION["success"] =
        "Registration successful! Your Student ID is " .
        $student_id .
        ". Please use it or your email to sign in.";

    header("Location: index.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| PASSWORD RESET
|--------------------------------------------------------------------------
*/

if ($action === "reset_password") {

    $identity = trim($_POST["identity"] ?? "");
    $challenge_answer = trim($_POST["challenge_answer"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_new_password = $_POST["confirm_new_password"] ?? "";

    if ($identity === "") {
        redirect_with_error(
            "Please enter your Student ID or email address.",
            "forgotpassword.php"
        );
    }

    /*
    | Server-side validation.
    | The browser JavaScript is only for user experience.
    */
    if ($challenge_answer !== "15") {
        redirect_with_error(
            "Incorrect security question answer.",
            "forgotpassword.php"
        );
    }

    if ($new_password === "" || $confirm_new_password === "") {
        redirect_with_error(
            "Please enter and confirm your new password.",
            "forgotpassword.php"
        );
    }

    if (strlen($new_password) < 8) {
        redirect_with_error(
            "Your new password must contain at least 8 characters.",
            "forgotpassword.php"
        );
    }

    if ($new_password !== $confirm_new_password) {
        redirect_with_error(
            "The new passwords do not match.",
            "forgotpassword.php"
        );
    }

    $find = $conn->prepare(
        "SELECT id
         FROM students
         WHERE email = ? OR student_id = ?
         LIMIT 1"
    );

    $find->bind_param("ss", $identity, $identity);
    $find->execute();

    $found = $find->get_result();

    if ($found->num_rows !== 1) {
        $find->close();

        redirect_with_error(
            "No student account was found with that Student ID or email.",
            "forgotpassword.php"
        );
    }

    $student = $found->fetch_assoc();
    $find->close();

    $new_hash = password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );

    $update = $conn->prepare(
        "UPDATE students
         SET password = ?
         WHERE id = ?"
    );

    $update->bind_param(
        "si",
        $new_hash,
        $student["id"]
    );

    if (!$update->execute()) {
        $update->close();

        redirect_with_error(
            "Password reset failed. Please try again.",
            "forgotpassword.php"
        );
    }

    $update->close();
    $conn->close();

    $_SESSION["success"] =
        "Your password has been reset successfully. You can now sign in.";

    header("Location: index.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| UNKNOWN ACTION
|--------------------------------------------------------------------------
*/

$conn->close();

$_SESSION["error"] = "Invalid request.";
header("Location: index.php");
exit();
?>
