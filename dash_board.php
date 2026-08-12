<?php
session_start();

// Strict Session Protection Security Gate
if (empty($_SESSION['student_id'])) {
    $_SESSION['error'] = "Access denied. Please sign in to view your dashboard portal.";
    header("Location: login.php");
    exit();
}

// Global Core Mock Data Context - Will interface natively with MySQL columns in the next step
$student_name = $_SESSION['student_name'] ?? "Alex Phiri";
$student_id = $_SESSION['student_id'] ?? "NACIT/2026/048";
$current_program = "Advanced Diploma in Software Engineering";
$current_semester = "Semester 2, Year 2";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NACIT Portal - Student Dashboard</title>
    
    <link rel="stylesheet" type="text/css" href="style.css?v=1.4">
</head>
<body class="dashboard-body">

    <!-- Left Navigation Rail Panel Sidebar Components -->
    <aside class="sidebar" id="app_sidebar">
        <div class="sidebar-brand">
            <span class="brand-text">NACIT Portal</span>
            <button type="button" class="sidebar-close-btn" id="close_nav_btn">&times;</button>
        </div>
        
        <!-- User Metadata Display Frame -->
        <div class="sidebar-profile">
            <div class="avatar-circle"><?= strtoupper(substr($student_name, 0, 1)) ?></div>
            <h4 class="user-fullname"><?= htmlspecialchars($student_name) ?></h4>
            <span class="user-reg-number"><?= htmlspecialchars($student_id) ?></span>
        </div>

        <!-- Main Portal Nav Items Tree -->
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <span class="menu-icon">📊</span> Dashboard Overview
            </a>
            <a href="courses.php" class="menu-item">
                <span class="menu-icon">📚</span> My Modules
            </a>
            <a href="assignments.php" class="menu-item">
                <span class="menu-icon">📝</span> Assignments Feed
            </a>
            <a href="grades.php" class="menu-item">
                <span class="menu-icon">🏅</span> Grades & Transcripts
            </a>
            <a href="finance.php" class="menu-item">
                <span class="menu-icon">💳</span> Tuition Fees
            </a>
            <div class="menu-divider"></div>
            <a href="logout.php" class="menu-item logout-item">
                <span class="menu-icon">🚪</span> Logout Portal
            </a>
        </nav>
    </aside>

    <!-- Main Viewport Interface Box Container -->
    <div class="dashboard-viewport">
        
        <!-- Top Workspace Bar Header Context -->
        <header class="dashboard-header">
            <button type="button" class="menu-toggle-btn" id="open_nav_btn">☰</button>
            <div class="header-context">
                <span class="academic-tag"><?= htmlspecialchars($current_program) ?></span>
            </div>
            <div class="header-actions">
                <span class="semester-badge"><?= htmlspecialchars($current_semester) ?></span>
            </div>
        </header>

        <!-- Interior Main Workspace Matrix -->
        <main class="dashboard-main">
            <section class="welcome-banner">
                <h1>Welcome Back, <?= htmlspecialchars(explode(' ', $student_name)[0]) ?>! 👋</h1>
                <p>Track your ongoing technical modules, daily schedules, and project submission deadlines below.</p>
            </section>

            <!-- Metrics Performance Summary Cards Grid Row -->
            <section class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-title">Current GPA</span>
                        <span class="metric-icon" style="background: #e0e7ff; color: #4f46e5;">📈</span>
                    </div>
                    <h2 class="metric-value">3.64</h2>
                    <span class="metric-footer text-success">First Class Standing</span>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-title">Registered Modules</span>
                        <span class="metric-icon" style="background: #ecfdf5; color: #059669;">📖</span>
                    </div>
                    <h2 class="metric-value">3 Active</h2>
                    <span class="metric-footer">9 Core Semester Credits</span>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-title">Financial Balance</span>
                        <span class="metric-icon" style="background: #fef2f2; color: #dc2626;">💰</span>
                    </div>
                    <h2 class="metric-value">MK 45,000</h2>
                    <span class="metric-footer text-danger">Payment Due: Next Week</span>
                </div>
            </section>

            <!-- Central Academic Workspace Component Panel -->
            <section class="dashboard-content-split">
                <div class="content-panel panel-large">
                    <div class="panel-header">
                        <h3>Active Modules & Assignments Tracker</h3>
                        <p class="panel-subtitle-text">A real-time ledger matching class time slots directly against evaluation submission deliverables.</p>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th>Module Code & Title</th>
                                    <th>Class Schedule</th>
                                    <th>Current Assignment</th>
                                    <th>Due Date</th>
                                    <th>Task Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Item Entity 1 -->
                                <tr>
                                    <td>
                                        <div class="module-info">
                                            <span class="module-title">Advanced Backend Architecture</span>
                                            <span class="module-code">ASE 421</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-info">
                                            <strong>Mon / Wed 08:30</strong>
                                            <span class="venue-text">Lab Room 3 (Banda. C)</span>
                                        </div>
                                    </td>
                                    <td><span class="assignment-name">Build a Secure PHP MVC Auth API</span></td>
                                    <td><span class="date-highlight text-danger">Aug 18, 2026</span></td>
                                    <td><span class="status-pill status-pending">Pending</span></td>
                                </tr>
                                
                                <!-- Item Entity 2 -->
                                <tr>
                                    <td>
                                        <div class="module-info">
                                            <span class="module-title">Relational Database Systems</span>
                                            <span class="module-code">DBM 422</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-info">
                                            <strong>Tue / Thu 11:00</strong>
                                            <span class="venue-text">Main Hall B (Phiri. F)</span>
                                        </div>
                                    </td>
                                    <td><span class="assignment-name">3rd Normal Form Schema Design</span></td>
                                    <td><span class="date-highlight">Aug 22, 2026</span></td>
                                    <td><span class="status-pill status-submitted">Submitted</span></td>
                                </tr>
                                
                                <!-- Item Entity 3 -->
                                <tr>
                                    <td>
                                        <div class="module-info">
                                            <span class="module-title">Systems Analysis & Design</span>
                                            <span class="module-code">SAD 423</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-info">
                                            <strong>Friday 14:00</strong>
                                            <span class="venue-text">Lecture Room 1 (Lowe. J)</span>
                                        </div>
                                    </td>
                                    <td><span class="assignment-name">UML Class & Use Case Diagrams</span></td>
                                    <td><span class="date-highlight text-success">Evaluated</span></td>
                                    <td><span class="status-pill status-graded">Graded (A)</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>