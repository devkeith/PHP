<?php
session_start();

/*
|--------------------------------------------------------------------------
| STUDENT DASHBOARD
|--------------------------------------------------------------------------
| The dashboard is protected by the login session created in backend.php.
*/

if (empty($_SESSION['student_id'])) {
    $_SESSION['error'] = "Access denied. Please sign in to view your dashboard portal.";
    header("Location: index.php");
    exit();
}

$student_name = $_SESSION['student_name'] ?? "Student";
$student_id = $_SESSION['student_id'] ?? "N/A";
$current_program = $_SESSION['student_program'] ?? "Advance Diploma in Computing";
$student_year = (int)($_SESSION['student_year'] ?? 3);
$student_semester = (int)($_SESSION['student_semester'] ?? 1);

$current_semester = "Semester " . $student_semester . ", Year " . $student_year;

$name_parts = preg_split('/\s+/', trim($student_name));
$first_name = $name_parts[0] ?? "Student";

/*
|--------------------------------------------------------------------------
| READ-ONLY DATABASE CONNECTION
|--------------------------------------------------------------------------
| This dashboard only reads the logged-in student's modules.
| Database: nacit
|
| It uses the internal students.id saved by backend.php as
| $_SESSION['student_db_id'].
*/

$modules = [];
$total_credits = 0;
$module_count = 0;
$db_error = "";

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "nacit";

$conn = @new mysqli($host, $db_user, $db_password, $db_name);

if (!$conn->connect_error) {
    $conn->set_charset("utf8mb4");

    $student_db_id = (int)($_SESSION['student_db_id'] ?? 0);

    if ($student_db_id > 0) {
        $stmt = $conn->prepare(
            "SELECT
                module_code,
                module_name,
                class_schedule,
                venue,
                assignment_name,
                assignment_due_date,
                assignment_status,
                grade,
                credits
             FROM student_modules
             WHERE student_id = ?
               AND module_status = 'Active'
             ORDER BY module_code ASC"
        );

        if ($stmt) {
            $stmt->bind_param("i", $student_db_id);

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $modules[] = $row;
                    $total_credits += (int)$row['credits'];
                }

                $module_count = count($modules);
            }

            $stmt->close();
        } else {
            $db_error = "The student_modules table could not be read.";
        }
    } else {
        $db_error = "Student session information is incomplete. Please sign in again.";
    }

    $conn->close();
} else {
    $db_error = "The portal could not connect to the database.";
}

/*
|--------------------------------------------------------------------------
| DISPLAY HELPERS
|--------------------------------------------------------------------------
*/

function format_due_date($date) {
    if (empty($date)) {
        return "Not set";
    }

    $timestamp = strtotime($date);

    return $timestamp
        ? date("M d, Y", $timestamp)
        : "Not set";
}

function status_class($status) {
    switch ($status) {
        case "Submitted":
            return "status-submitted";

        case "Graded":
            return "status-graded";

        case "Pending":
            return "status-pending";

        default:
            return "status-pending";
    }
}

function status_text($status) {
    return $status !== "" ? $status : "Not Started";
}
/*
|--------------------------------------------------------------------------
| MODULE ENROLLMENT CATALOGUE
|--------------------------------------------------------------------------
*/
$student_program_key = strtoupper(trim($_SESSION['student_program'] ?? 'DC'));

/*
 * Only DC or DCBM are valid programme values.
 * The student cannot select/change this from the UI.
 */
if (!in_array($student_program_key, ['DC', 'DCBM'], true)) {
    $student_program_key = 'DC';
}

$module_catalogue = [
    'DC' => [
        1 => [
            ['code' => 'DC-BA',  'name' => 'Big Data Analysis', 'credits' => 15],
            ['code' => 'DC-MAD', 'name' => 'Mobile App Development', 'credits' => 15],
            ['code' => 'DC-BWD', 'name' => 'Backend Web Development', 'credits' => 15],
        ],
        2 => [
            ['code' => 'DC-NSC', 'name' => 'Network Security and Cryptography', 'credits' => 15],
            ['code' => 'DC-AGD', 'name' => 'Agile Development', 'credits' => 15],
        ],
    ],
    'DCBM' => [
        1 => [
            ['code' => 'DCBM-ITPM', 'name' => 'ITPM', 'credits' => 15],
            ['code' => 'DCBM-BWD',  'name' => 'Backend Web Development', 'credits' => 15],
        ],
        2 => [
            ['code' => 'DCBM-BA',  'name' => 'Big Data Analysis', 'credits' => 15],
            ['code' => 'DCBM-IS',  'name' => 'Information Systems', 'credits' => 15],
            ['code' => 'DCBM-PBO', 'name' => 'Principles of Business Organisation', 'credits' => 15],
        ],
    ],
];

/*
|--------------------------------------------------------------------------
| AJAX ENROLLMENT ENDPOINT
|--------------------------------------------------------------------------
| The same dashboard handles enrollment so no additional page is needed.
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'enroll_module') {
    header('Content-Type: application/json; charset=utf-8');

    if ($student_db_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Your student session is incomplete. Please sign in again.'
        ]);
        exit();
    }

    $posted_programme = strtoupper(trim($_POST['programme'] ?? ''));
    $module_code = trim($_POST['module_code'] ?? '');
    $posted_semester = (int)($_POST['semester'] ?? 0);

    /* Never trust the programme sent by the browser. */
    if ($posted_programme !== $student_program_key) {
        echo json_encode([
            'success' => false,
            'message' => 'Programme mismatch. You can only enroll in modules for your registered programme.'
        ]);
        exit();
    }

    if (!isset($module_catalogue[$student_program_key][$posted_semester])) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid semester selection.'
        ]);
        exit();
    }

    $selected_module = null;

    foreach ($module_catalogue[$student_program_key][$posted_semester] as $candidate) {
        if ($candidate['code'] === $module_code) {
            $selected_module = $candidate;
            break;
        }
    }

    if (!$selected_module) {
        echo json_encode([
            'success' => false,
            'message' => 'That module is not available for your programme and semester.'
        ]);
        exit();
    }

    $conn = @new mysqli("localhost", "root", "", "nacit");

    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'message' => 'The portal could not connect to the database.'
        ]);
        exit();
    }

    $conn->set_charset("utf8mb4");

    $check = $conn->prepare(
        "SELECT module_code
         FROM student_modules
         WHERE student_id = ? AND module_code = ?
         LIMIT 1"
    );

    if (!$check) {
        echo json_encode([
            'success' => false,
            'message' => 'The enrollment check could not be prepared.'
        ]);
        $conn->close();
        exit();
    }

    $check->bind_param("is", $student_db_id, $selected_module['code']);
    $check->execute();
    $already_enrolled = $check->get_result()->num_rows > 0;
    $check->close();

    if ($already_enrolled) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in ' . $selected_module['name'] . '.',
            'already_enrolled' => true
        ]);
        $conn->close();
        exit();
    }

    $insert = $conn->prepare(
        "INSERT INTO student_modules
            (student_id, module_code, module_name, class_schedule,
             venue, assignment_name, assignment_due_date,
             assignment_status, grade, credits, module_status)
         VALUES (?, ?, ?, '', '', '', NULL, 'Pending', NULL, ?, 'Active')"
    );

    if (!$insert) {
        echo json_encode([
            'success' => false,
            'message' => 'Enrollment could not be prepared. Please check the student_modules table.'
        ]);
        $conn->close();
        exit();
    }

    $insert->bind_param(
        "issi",
        $student_db_id,
        $selected_module['code'],
        $selected_module['name'],
        $selected_module['credits']
    );

    $success = $insert->execute();
    $insert->close();
    $conn->close();

    echo json_encode([
        'success' => $success,
        'message' => $success
            ? $selected_module['name'] . ' has been enrolled successfully.'
            : 'The module could not be enrolled. Please try again.'
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>NACIT Portal - Student Dashboard</title>

    <link rel="stylesheet" type="text/css" href="style.css?v=2.0">

<style id="module-enrollment-modal-css">
/* Module Enrollment modal - self-contained so it cannot fall back into page flow */
body.modal-open { overflow: hidden !important; }

.enrollment-modal[hidden] {
    display: none !important;
}

.enrollment-modal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 2147483000 !important;
    display: block;
    isolation: isolate;
}

.enrollment-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(15, 15, 15, .72) !important;
    backdrop-filter: blur(3px);
}

.enrollment-modal-dialog {
    position: relative !important;
    z-index: 1 !important;
    width: min(1100px, calc(100% - 32px)) !important;
    max-height: calc(100vh - 40px) !important;
    margin: 20px auto !important;
    overflow: hidden !important;
    background: #fff !important;
    border-top: 5px solid #e06b0b !important;
    box-shadow: 0 22px 60px rgba(0,0,0,.30) !important;
    font-family: Inter, "Segoe UI", Arial, sans-serif !important;
    color: #242424 !important;
}

.enrollment-modal-header {
    padding: 22px 25px !important;
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 20px !important;
    border-bottom: 1px solid #e6e6e6 !important;
}

.enrollment-modal-header h2 {
    margin: 0 0 5px !important;
    color: #242424 !important;
    font-size: 20px !important;
    line-height: 1.3 !important;
}

.enrollment-modal-header p {
    margin: 0 !important;
    color: #666 !important;
    font-size: 12px !important;
}

.enrollment-kicker {
    display: block !important;
    margin-bottom: 4px !important;
    color: #e06b0b !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    letter-spacing: 1px !important;
}

.enrollment-modal-close {
    width: 40px !important;
    height: 40px !important;
    flex: 0 0 auto !important;
    border: 1px solid #d4d4d4 !important;
    background: #fff !important;
    color: #4d4d4d !important;
    font-size: 27px !important;
    line-height: 1 !important;
    cursor: pointer !important;
}

.enrollment-modal-close:hover {
    border-color: #e06b0b !important;
    color: #e06b0b !important;
}

.enrollment-modal-body {
    max-height: calc(100vh - 145px) !important;
    overflow-y: auto !important;
}

.enrollment-selection-grid {
    padding: 20px 25px !important;
    display: grid !important;
    grid-template-columns: 1fr .8fr 1.4fr !important;
    gap: 15px !important;
    background: #f2f2f2 !important;
    border-bottom: 1px solid #e6e6e6 !important;
}

.enrollment-field {
    min-width: 0 !important;
}

.enrollment-field label {
    display: block !important;
    margin-bottom: 7px !important;
    color: #4d4d4d !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: .5px !important;
}

.enrollment-field select,
.enrollment-field input {
    width: 100% !important;
    min-height: 44px !important;
    padding: 10px 12px !important;
    border: 1px solid #d4d4d4 !important;
    border-radius: 0 !important;
    background: #fff !important;
    color: #242424 !important;
    outline: none !important;
}

.programme-locked {
    min-height: 44px !important;
    padding: 7px 11px !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    border: 1px solid #d4d4d4 !important;
    background: #f8f8f8 !important;
}

.programme-locked strong {
    color: #c65d08 !important;
    font-size: 13px !important;
}

.programme-locked span {
    color: #666 !important;
    font-size: 11px !important;
}

.programme-locked b {
    margin-left: auto !important;
}

.enrollment-summary {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    border-bottom: 1px solid #e6e6e6 !important;
}

.enrollment-summary > div {
    padding: 14px 18px !important;
    border-right: 1px solid #e6e6e6 !important;
}

.enrollment-summary > div:last-child {
    border-right: 0 !important;
}

.enrollment-summary-label {
    display: block !important;
    margin-bottom: 3px !important;
    color: #858585 !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
}

.enrollment-summary strong {
    color: #242424 !important;
    font-size: 13px !important;
}

.enrollment-table-wrap {
    width: 100% !important;
    overflow-x: auto !important;
}

.enrollment-table {
    width: 100% !important;
    min-width: 760px !important;
    border-collapse: collapse !important;
}

.enrollment-table th {
    padding: 12px 14px !important;
    background: #fff0df !important;
    color: #4a2108 !important;
    text-align: left !important;
    font-size: 11px !important;
}

.enrollment-table td {
    padding: 12px 14px !important;
    border-bottom: 1px solid #e6e6e6 !important;
    color: #4d4d4d !important;
    font-size: 12px !important;
}

.enroll-btn {
    min-height: 33px !important;
    padding: 0 13px !important;
    border: 0 !important;
    background: #e06b0b !important;
    color: #fff !important;
    font-size: 10px !important;
    font-weight: 750 !important;
    cursor: pointer !important;
}

.enroll-btn:hover {
    background: #7a3b0c !important;
}

.enroll-btn.enrolled {
    background: #17845b !important;
}

.enrollment-empty {
    padding: 45px 20px !important;
    text-align: center !important;
    color: #858585 !important;
}

.enrollment-modal-message {
    display: none !important;
    margin: 0 25px 20px !important;
    padding: 12px 14px !important;
    border-left: 4px solid !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}

.enrollment-modal-message.message-success,
.enrollment-modal-message.message-error {
    display: block !important;
}

.enrollment-modal-message.message-success {
    background: #e8f6ef !important;
    color: #116b4a !important;
    border-left-color: #17845b !important;
}

.enrollment-modal-message.message-error {
    background: #fdecec !important;
    color: #8d2626 !important;
    border-left-color: #c83d3d !important;
}

@media (max-width: 800px) {
    .enrollment-selection-grid {
        grid-template-columns: 1fr 1fr !important;
    }

    .enrollment-search-field {
        grid-column: 1 / -1 !important;
    }

    .enrollment-summary {
        grid-template-columns: 1fr 1fr !important;
    }
}

@media (max-width: 600px) {
    .enrollment-modal-dialog {
        width: calc(100% - 16px) !important;
        max-height: calc(100vh - 16px) !important;
        margin: 8px auto !important;
    }

    .enrollment-selection-grid {
        grid-template-columns: 1fr !important;
        padding: 18px !important;
    }

    .enrollment-search-field {
        grid-column: auto !important;
    }
}
</style>

</head>

<body class="dashboard-body">

<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar" id="app_sidebar">

    <div class="sidebar-brand">

        <span class="brand-text">
            NACIT Portal
        </span>

        <button
            type="button"
            class="sidebar-close-btn"
            id="close_nav_btn"
            aria-label="Close navigation">

            &times;

        </button>

    </div>


    <div class="sidebar-profile">

        <div class="avatar-circle">
            <?= htmlspecialchars(strtoupper(substr($student_name, 0, 1))) ?>
        </div>

        <h4 class="user-fullname">
            <?= htmlspecialchars($student_name) ?>
        </h4>

        <span class="user-reg-number">
            <?= htmlspecialchars($student_id) ?>
        </span>

    </div>


    <nav class="sidebar-menu">

        <a href="dash_board.php" class="menu-item active">
            <span class="menu-icon">📊</span>
            Dashboard Overview
        </a>

        <a href="courses.php" class="menu-item">
            <span class="menu-icon">📚</span>
            My Modules
        </a>

        <a href="assignments.php" class="menu-item">
            <span class="menu-icon">📝</span>
            Assignments Feed
        </a>

        <a href="grades.php" class="menu-item">
            <span class="menu-icon">🏅</span>
            Grades & Transcripts
        </a>

        <a href="finance.php" class="menu-item">
            <span class="menu-icon">💳</span>
            Tuition Fees
        </a>

        <div class="menu-divider"></div>

        <a href="logout.php" class="menu-item logout-item">
            <span class="menu-icon">🚪</span>
            Logout Portal
        </a>

    </nav>

</aside>


<!-- =========================================================
     MAIN VIEWPORT
========================================================= -->

<div class="dashboard-viewport">

    <header class="dashboard-header">

        <button
            type="button"
            class="menu-toggle-btn"
            id="open_nav_btn"
            aria-label="Open navigation">

            ☰

        </button>


        <div class="header-context">

            <span class="academic-tag">
                <?= htmlspecialchars($current_program) ?>
            </span>

        </div>


        <div class="header-actions">

            <span class="semester-badge">
                <?= htmlspecialchars($current_semester) ?>
            </span>

        </div>

    </header>


    <main class="dashboard-main">

        <!-- =================================================
             WELCOME
        ================================================== -->

        <section class="welcome-banner">

            <h1>
                Welcome Back,
                <?= htmlspecialchars($first_name) ?>! 👋
            </h1>

            <p>
                Here is your academic overview, registered modules,
                assignments and current study information.
            </p>

        </section>

        <!-- =================================================
             MODULE ENROLLMENT SHORTCUT
        ================================================== -->

        <section class="enrollment-shortcut">
            <div class="enrollment-shortcut-content">
                <div>
                    <span class="enrollment-kicker">ACADEMIC REGISTRATION</span>
                    <h2>Module Enrollment</h2>
                    <p>
                        Enroll in modules available for your registered programme.
                    </p>
                </div>

                <button type="button" class="enrollment-shortcut-btn" id="open_enrollment_btn">
                    Open Module Enrollment
                </button>
            </div>
        </section>

        <!-- =================================================
             MODULE ENROLLMENT MODAL
        ================================================== -->

        <div class="enrollment-modal" id="enrollment_modal" aria-hidden="true" hidden>
            <div class="enrollment-modal-overlay" id="enrollment_overlay"></div>

            <section
                class="enrollment-modal-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="enrollment_modal_title">

                <div class="enrollment-modal-header">
                    <div>
                        <span class="enrollment-kicker">ACADEMIC REGISTRATION</span>
                        <h2 id="enrollment_modal_title">Module Enrollment</h2>
                        <p>
                            Select a semester to view the modules available to you.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="enrollment-modal-close"
                        id="close_enrollment_btn"
                        aria-label="Close Module Enrollment">
                        &times;
                    </button>
                </div>

                <div class="enrollment-modal-body">

                    <div class="enrollment-selection-grid">

                        <div class="enrollment-field">
                            <label>Programme</label>
                            <div class="programme-locked">
                                <strong>
                                    <?= htmlspecialchars($student_program_key) ?>
                                </strong>

                                <span>
                                    <?= $student_program_key === 'DC'
                                        ? 'Computing'
                                        : 'Computing & Business Management' ?>
                                </span>

                                <b title="Your programme is locked to your student account">
                                    🔒
                                </b>
                            </div>
                        </div>

                        <div class="enrollment-field">
                            <label for="enrollment_semester">Semester</label>

                            <select id="enrollment_semester">
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>

                        <div class="enrollment-field enrollment-search-field">
                            <label for="enrollment_search">Search Modules</label>

                            <input
                                type="search"
                                id="enrollment_search"
                                placeholder="Search module name or code...">
                        </div>

                    </div>

                    <div class="enrollment-summary">
                        <div>
                            <span class="enrollment-summary-label">Programme</span>
                            <strong><?= htmlspecialchars($student_program_key) ?></strong>
                        </div>

                        <div>
                            <span class="enrollment-summary-label">Academic Level</span>
                            <strong>Year 3</strong>
                        </div>

                        <div>
                            <span class="enrollment-summary-label">Semester</span>
                            <strong id="enrollment_summary_semester">Semester 1</strong>
                        </div>

                        <div>
                            <span class="enrollment-summary-label">Modules Found</span>
                            <strong id="enrollment_module_count">0</strong>
                        </div>
                    </div>

                    <div class="enrollment-table-wrap">
                        <table class="portal-table enrollment-table">
                            <thead>
                                <tr>
                                    <th>Module Code</th>
                                    <th>Module Name</th>
                                    <th>Semester</th>
                                    <th>Credits</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="enrollment_table_body"></tbody>
                        </table>
                    </div>

                    <div class="enrollment-modal-message" id="enrollment_modal_message"></div>

                </div>
            </section>
        </div>


        <?php if ($db_error): ?>

            <div class="error-banner">
                <?= htmlspecialchars($db_error) ?>
            </div>

        <?php endif; ?>


        <!-- =================================================
             METRICS
        ================================================== -->

        <section class="metrics-grid">

            <div class="metric-card">

                <div class="metric-header">

                    <span class="metric-title">
                        Registered Modules
                    </span>

                    <span class="metric-icon"
                          style="background: #e8f1fc; color: #1557a6;">
                        📚
                    </span>

                </div>

                <h2 class="metric-value">
                    <?= $module_count ?>
                </h2>

                <span class="metric-footer">
                    Active modules
                </span>

            </div>


            <div class="metric-card">

                <div class="metric-header">

                    <span class="metric-title">
                        Semester Credits
                    </span>

                    <span class="metric-icon"
                          style="background: #e8f1fc; color: #1769d1;">
                        🎓
                    </span>

                </div>

                <h2 class="metric-value">
                    <?= $total_credits ?>
                </h2>

                <span class="metric-footer">
                    Core semester credits
                </span>

            </div>


            <div class="metric-card">

                <div class="metric-header">

                    <span class="metric-title">
                        Academic Level
                    </span>

                    <span class="metric-icon"
                          style="background: #e8f1fc; color: #123a68;">
                        📖
                    </span>

                </div>

                <h2 class="metric-value">
                    Year <?= $student_year ?>
                </h2>

                <span class="metric-footer">
                    <?= htmlspecialchars($current_semester) ?>
                </span>

            </div>

        </section>


        <!-- =================================================
             MODULES AND ASSIGNMENTS
        ================================================== -->

        <section class="dashboard-content-split">

            <div class="content-panel panel-large">

                <div class="panel-header">

                    <h3>
                        Active Modules & Assignments Tracker
                    </h3>

                    <p class="panel-subtitle-text">
                        Your current modules, class schedules and
                        assignment submission information.
                    </p>

                </div>


                <div class="table-responsive">

                    <table class="portal-table">

                        <thead>

                            <tr>

                                <th>
                                    Module Code & Title
                                </th>

                                <th>
                                    Class Schedule
                                </th>

                                <th>
                                    Current Assignment
                                </th>

                                <th>
                                    Due Date
                                </th>

                                <th>
                                    Task Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (empty($modules)): ?>

                            <tr>

                                <td colspan="5"
                                    style="text-align: center; padding: 40px;">

                                    <strong>
                                        No active modules have been registered yet.
                                    </strong>

                                    <br>

                                    <span style="color: #788497;">
                                        Your modules will appear here once they
                                        have been assigned to your account.
                                    </span>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($modules as $module): ?>

                                <tr>

                                    <td>

                                        <div class="module-info">

                                            <span class="module-title">
                                                <?= htmlspecialchars($module['module_name']) ?>
                                            </span>

                                            <span class="module-code">
                                                <?= htmlspecialchars($module['module_code']) ?>
                                            </span>

                                        </div>

                                    </td>


                                    <td>

                                        <div class="schedule-info">

                                            <strong>
                                                <?= htmlspecialchars($module['class_schedule'] ?: "Schedule not set") ?>
                                            </strong>

                                            <span class="venue-text">
                                                <?= htmlspecialchars($module['venue'] ?: "Venue not set") ?>
                                            </span>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="assignment-name">
                                            <?= htmlspecialchars(
                                                $module['assignment_name'] ?: "No current assignment"
                                            ) ?>
                                        </span>

                                    </td>


                                    <td>

                                        <span class="date-highlight">

                                            <?= htmlspecialchars(
                                                format_due_date($module['assignment_due_date'])
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="status-pill <?= htmlspecialchars(
                                            status_class($module['assignment_status'])
                                        ) ?>">

                                            <?= htmlspecialchars(
                                                status_text($module['assignment_status'])
                                            ) ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- =========================================================
     MOBILE SIDEBAR SCRIPT
========================================================= -->

<script>

const sidebar = document.getElementById('app_sidebar');
const openButton = document.getElementById('open_nav_btn');
const closeButton = document.getElementById('close_nav_btn');

if (openButton) {

    openButton.addEventListener('click', function () {
        sidebar.classList.add('open');
    });

}

if (closeButton) {

    closeButton.addEventListener('click', function () {
        sidebar.classList.remove('open');
    });

}

document.querySelectorAll('.sidebar .menu-item').forEach(function (item) {

    item.addEventListener('click', function () {

        if (window.innerWidth <= 1000) {
            sidebar.classList.remove('open');
        }

    });

});

</script>

</body>
</html>
