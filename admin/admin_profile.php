<?php
include '../db.php';
include 'admin_header.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$userName  = $user['name'];
$userRole  = $user['role'];
$userEmail = $user['email'];
$userContact = $user['contact']; 

//initials
$nameParts = explode(" ", $userName);
$initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ""));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | ALTA iHub</title>
    <link rel="stylesheet" href="admin/admin.css"> 
</head>
<body class="profile-body">
    

    <div id="profilePage" class="profile-container">
        <h2 class="page-main-title">Profile & Settings</h2>
        <p class="page-subtitle">Manage your account information</p>

        <!-- Profile Card -->
        <div class="profile-gradient-card">
            <div class="profile-header-content">
                <div id="userInitials" class="user-initials-display"><?= $initials ?></div>
                <div class="user-info-text-wrapper">
                    <h3 class="user-name-display"><?= htmlspecialchars($userName) ?></h3>
                    <p class="user-role-display">
                        <span class="capitalize"><?= htmlspecialchars($userRole) ?></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="info-card">
            <div class="card-section-header">
                <h3>Personal Information</h3>
            </div>
            <div class="info-group-wrapper">
                <div class="info-group">
                    <label class="info-label">Email Address</label>
                    <p class="info-value"><?= htmlspecialchars($userEmail) ?></p>
                </div>
<div class="info-group">
    <label class="info-label">Contact Number</label>
    <p class="info-value"><?= htmlspecialchars($userContact) ?></p>
</div>

                <div class="info-group">
                    <label class="info-label">Role</label>
                    <p class="info-value capitalize"><?= htmlspecialchars($userRole) ?></p>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="info-card">
            <div class="card-section-header">
                <h3>Security</h3>
            </div>
            <button class="btn btn-muted btn-full-width">🔒 Change Password</button>
        </div>

        <!-- Logout Button -->
        <a href="../logout.php" class="btn btn-destructive btn-full-width">🚪 Logout</a>
    </div>
</body>
</html>
