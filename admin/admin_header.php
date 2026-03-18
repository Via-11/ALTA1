<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit(); 
}

$stmt = $conn->prepare("SELECT title FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$announcementContent = null;

if ($row = $result->fetch_assoc()) {
    $announcementContent = $row['title']; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alta iHUB Admin</title>

    <!-- MAIN STYLES -->
    <link rel="stylesheet" href="../styles/style1.css">

</head>
<body>
<!-- The rest of your HTML remains the same -->
<div class="top-bar <?= $announcementContent ? 'top-bar-scroll' : '' ?>">
    <p class="top-bar-text">
        <?= $announcementContent ? htmlspecialchars($announcementContent) : '🏆 DOST-PCIEERD ACCREDITED TECHNOLOGY BUSINESS INCUBATOR' ?>
    </p>
</div>

<!-- HEADER -->
<header class="main-header">
    <div class="navbar-container">
        <!-- ... rest of header code ... -->
        <div class="logo-area">
            <a href="index.php" class="logo-link">
                <img src="../assets/logo.png" class="main-logo-img" alt="Alta iHUB Logo">
            </a>
            <span class="logo-divider"></span>
            <div class="logo-text-wrapper">
                <div class="logo-text-main">Alta iHUB</div>
                <div class="logo-text-subtitle">Admin Dashboard</div>
            </div>
        </div>

        <nav class="nav-links-wrapper" role="navigation" aria-label="Admin main navigation">
            <a href="admin_index.php" class="nav-link">Dashboard</a>
            <a href="admin_bookings.php" class="nav-link">Bookings</a>
            <a href="admin_services.php" class="nav-link">Services</a>
            <a href="admin_announcements.php" class="nav-link">Announcements</a>
            <a href="admin_messages.php" class="nav-link">Messages</a>
        </nav>

        <div class="profile-buttons-area">
            <span class="admin-badge" aria-label="User role">ADMIN</span>
            <a href="admin_profile.php" class="btn-outline-gold">Profile</a>
            <a href="../logout.php" class="btn-register">Logout</a>
        </div>

    </div>
</header>
