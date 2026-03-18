<?php 
include 'admin_header.php'; 
include '../db.php'; 

$userName = $_SESSION['user_name'] ?? 'Admin';
$userRole = $_SESSION['user_role'] ?? 'Administrator';

// Stats Queries
$pendingCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$todayCount = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE appointment_date = ?");
$todayCount->execute([date('Y-m-d')]);
$todayCount = $todayCount->fetchColumn();

$serviceCount = $pdo->query("SELECT COUNT(DISTINCT service_name) FROM service_slots")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
?>
<link rel="stylesheet" href="admin.css">
<div id="dashboardPage" class="user-home-page admin-dashboard">
    
    <!-- Welcome Card (User UI Style) -->
    <div class="welcome-card-gradient">
        <div class="welcome-card-content">
            <div class="welcome-text-area">
                <p class="text-opacity-90">Management Overview,</p>
                <h2 class="user-name-title"><?= htmlspecialchars($userName) ?></h2>
                <div class="user-role-badge badge"><?= htmlspecialchars($userRole) ?></div>
            </div>
            <div class="notification-icon-wrapper">
                <span class="notification-count"><?= $pendingCount ?></span>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="notification-icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Admin Stats Grid (Adaptive UI) -->
    <div class="section-container">
        <h3 class="section-heading">Platform Statistics</h3>
        <div class="admin-stats-grid">
            <div class="admin-stat-card card">
                <span class="admin-stat-label">Pending Bookings</span>
                <h2 class="stat-value text-red"><?= $pendingCount ?></h2>
                <a href="bookings.php" class="view-all-link">Review Now</a>
            </div>
            <div class="admin-stat-card card">
                <span class="admin-stat-label">Today's Schedule</span>
                <h2 class="stat-value"><?= $todayCount ?></h2>
            </div>
            <div class="admin-stat-card card">
                <span class="admin-stat-label">Total Users</span>
                <h2 class="stat-value"><?= $userCount ?></h2>
            </div>
        </div>
    </div>

    <!-- Quick Actions (User UI Style) -->
    <div class="section-container">
        <h3 class="section-heading">Quick Actions</h3>
        <div class="quick-actions-grid">
            <div class="quick-action-item" onclick="window.location.href='bookings.php'">
                <div class="action-icon icon-blue">📋</div>
                <p class="action-text">Review Bookings</p>
            </div>
            <div class="quick-action-item" onclick="window.location.href='admin_announcements.php'">
                <div class="action-icon icon-gold">📢</div>
                <p class="action-text">Post Update</p>
            </div>
            <div class="quick-action-item" onclick="window.location.href='services.php'">
                <div class="action-icon icon-green">🛠️</div>
                <p class="action-text">Manage Services</p>
            </div>
            <div class="quick-action-item" onclick="window.location.href='admin_messages.php'">
                <div class="action-icon icon-red">✉️</div>
                <p class="action-text">View Messages</p>
            </div>
        </div>
    </div>
</div>
