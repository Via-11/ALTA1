<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

function cleanUrl($url) {
    $url = trim($url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('/^http/', $url)) {
        return 'https://' . $url;
    }

    return $url;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal_info'])) {

    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $github = cleanUrl($_POST['github'] ?? '');
    $linkedin = cleanUrl($_POST['linkedin'] ?? '');
    $facebook = cleanUrl($_POST['facebook'] ?? '');
    $website = cleanUrl($_POST['website'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    if (!empty($name) && !empty($contact)) {
        try {

            $stmt = $pdo->prepare("
                UPDATE users 
                    SET name = ?, contact = ?, bio = ?, github = ?, linkedin = ?, facebook = ?, website = ?, skills = ?
                    WHERE user_id = ?
                    ");

            $success = $stmt->execute([
                $name,
                $contact,
                $bio,
                $github,
                $linkedin,
                $facebook,
                $website,
                $skills,
                $userId
            ]);

            if ($success) {
                $_SESSION['profile_success'] = "Personal information updated successfully!";
                $_SESSION['user_name'] = $name;
            } else {
                $_SESSION['profile_error'] = "Failed to update personal information.";
            }

        } catch (PDOException $e) {
            $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
        }

    } else {
        $_SESSION['profile_error'] = "Name and contact are required fields.";
    }

    header("Location: profile.php?tab=personal");
    exit;
}

// Handle Startup Details Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_startup_details'])) {

    $startupName = trim($_POST['startup_name'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $startupStage = trim($_POST['startup_stage'] ?? '');
    $foundedYear = trim($_POST['founded_year'] ?? '');
    $teamSize = trim($_POST['team_size'] ?? '');

    $description = trim($_POST['startup_description'] ?? '');
    $targetMarket = trim($_POST['target_market'] ?? '');
    $revenueModel = trim($_POST['revenue_model'] ?? '');
    $fundingStatus = trim($_POST['funding_status'] ?? '');

    try {

        $stmt = $pdo->prepare("
            UPDATE users 
                SET startup_name = ?, 
                industry = ?, 
                startup_stage = ?, 
                founded_year = ?, 
                team_size = ?, 
                startup_description = ?, 
                target_market = ?, 
                revenue_model = ?, 
                funding_status = ?
                WHERE user_id = ?
                ");

        $stmt->execute([
            $startupName,
            $industry,
            $startupStage,
            $foundedYear,
            $teamSize,
            $description,
            $targetMarket,
            $revenueModel,
            $fundingStatus,
            $userId
        ]);

        $_SESSION['profile_success'] = "Startup details updated successfully!";

    } catch (PDOException $e) {
        $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
    }

    header("Location: profile.php?tab=startup");
    exit;
}

// Handle Document Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_document'])) {
    $documentType = trim($_POST['document_type'] ?? '');

    if (empty($documentType)) {
        $_SESSION['profile_error'] = "Please select a document type.";
    } elseif (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['profile_error'] = "Failed to upload file. Please try again.";
    } else {
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $fileMime = mime_content_type($_FILES['document']['tmp_name']);
        
        if (!in_array($fileMime, $allowedMimes)) {
            $_SESSION['profile_error'] = "Invalid file type. Allowed: PDF, JPG, PNG only.";
        } elseif ($_FILES['document']['size'] > $maxSize) {
            $_SESSION['profile_error'] = "File exceeds 5MB limit.";
        } else {
            $uploadDir = 'uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
            $fileName = $userId . '_' . $documentType . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['document']['tmp_name'], $filePath)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO documents (user_id, document_type, file_name, file_path, file_size, file_type, uploaded_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                        ");
                    $stmt->execute([
                        $userId,
                        $documentType,
                        $_FILES['document']['name'],
                        $filePath,
                        $_FILES['document']['size'],
                        $fileMime
                    ]);
                    $_SESSION['profile_success'] = "Document uploaded successfully!";
                } catch (PDOException $e) {
                    $_SESSION['profile_error'] = "Failed to save document information.";
                }
            } else {
                $_SESSION['profile_error'] = "Failed to move uploaded file.";
            }
        }
    }
    header("Location: profile.php?tab=documents");
    exit;
}

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $newEmail = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newEmail)) {
        $_SESSION['profile_error'] = "Email is required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch();
            
            $errors = [];
            
            // Verify current password if changing email or password
            if ($newEmail !== $currentUser['password'] || !empty($newPassword)) {
                if (empty($currentPassword)) {
                    $errors[] = "Current password is required.";
                } elseif ($currentPassword !== $currentUser['password']) {
                    $errors[] = "Current password is incorrect.";
                }
            }
            
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $errors[] = "New passwords do not match.";
                }
                if (strlen($newPassword) < 8) {
                    $errors[] = "New password must be at least 8 characters.";
                }
            }
            
            if (empty($errors)) {
                if (!empty($newPassword)) {
                    $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE user_id = ?");
                    $stmt->execute([$newEmail, $newPassword, $userId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
                    $stmt->execute([$newEmail, $userId]);
                }
                $_SESSION['profile_success'] = "Settings updated successfully!";
            } else {
                $_SESSION['profile_error'] = implode("<br>", $errors);
            }
        } catch (PDOException $e) {
            $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
        }
    }
    header("Location: profile.php?tab=settings");
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// User variables
$userName = $user['name'] ?? 'User';
$userRole = $user['role'] ?? 'Student';
$userEmail = $user['email'] ?? '';
$userContact = $user['contact'] ?? '';
$userBio = $user['bio'] ?? '';
$userPosition = $user['position'] ?? '';
$userGithub = $user['github'] ?? '';
$userLinkedin = $user['linkedin'] ?? '';
$userFacebook = $user['facebook'] ?? '';
$userWebsite = $user['website'] ?? '';
$userSkills = $user['skills'] ?? '';
$userCompany = $user['company'] ?? '';
$userAddress = $user['address'] ?? '';
$userEducation = $user['education'] ?? '';
$userExperience = $user['experience'] ?? '';

$userStartupName = $user['startup_name'] ?? '';
$userIndustry = $user['industry'] ?? '';
$userStartupStage = $user['startup_stage'] ?? '';
$userFoundedYear = $user['founded_year'] ?? '';
$userTeamSize = $user['team_size'] ?? '';

$userStartupDescription = $user['startup_description'] ?? '';
$userTargetMarket = $user['target_market'] ?? '';
$userRevenueModel = $user['revenue_model'] ?? '';
$userFundingStatus = $user['funding_status'] ?? '';

// Fetch user documents
try {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$userId]);
    $documents = $stmt->fetchAll();
} catch (PDOException $e) {
    $documents = [];
}

// Get initials
$nameParts = explode(" ", $userName);
$initials = strtoupper(
    substr($nameParts[0], 0, 1) . 
    (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : "")
);

// Get active tab
$activeTab = $_GET['tab'] ?? 'personal';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/profile.css">
</head>
<body class="profile-body">

    <?php include 'header.php'; ?>

    <main class="profile-main-container">
        <!-- Page Header -->
        <div class="profile-page-header">
            <h1 class="profile-page-title">My Profile</h1>
            <p class="profile-page-subtitle">Manage your account and personal information</p>
        </div>

        <!-- Toast Messages -->
        <?php if (isset($_SESSION['profile_success'])): ?>
            <div class="profile-toast profile-toast-success">
                <svg class="profile-toast-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><?= htmlspecialchars($_SESSION['profile_success']) ?></span>
            </div>
            <?php unset($_SESSION['profile_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['profile_error'])): ?>
            <div class="profile-toast profile-toast-error">
                <svg class="profile-toast-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span><?= $_SESSION['profile_error'] ?></span>
            </div>
            <?php unset($_SESSION['profile_error']); ?>
        <?php endif; ?>

        <!-- Profile Header Card -->
        <div class="profile-header-card">
            <div class="profile-header-gradient"></div>
            <div class="profile-header-content">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar"><?= $initials ?></div>
                </div>
                <div class="profile-user-info">
                    <h2 class="profile-user-name"><?= htmlspecialchars($userName) ?></h2>
                    <p class="profile-user-role"><?= htmlspecialchars($userRole) ?></p>
                    <?php if (!empty($userCompany)): ?>
                        <p class="profile-user-company"><?= htmlspecialchars($userCompany) ?></p>
                    <?php endif; ?>
                </div>
                <button type="button" class="profile-edit-btn" onclick="profileToggleEditMode()">
                    <svg class="profile-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="profile-tabs-container">
            <nav class="profile-tabs-nav">
                <a href="?tab=personal" class="profile-tab-link <?= $activeTab === 'personal' ? 'profile-tab-link-active' : '' ?>">
                    <svg class="profile-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Info
                </a>
                <a href="?tab=startup" class="profile-tab-link <?= $activeTab === 'startup' ? 'profile-tab-link-active' : '' ?>">
                    <svg class="profile-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Startup Details
                </a>
                <a href="?tab=documents" class="profile-tab-link <?= $activeTab === 'documents' ? 'profile-tab-link-active' : '' ?>">
                    <svg class="profile-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Documents
                </a>
                <a href="?tab=settings" class="profile-tab-link <?= $activeTab === 'settings' ? 'profile-tab-link-active' : '' ?>">
                    <svg class="profile-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="profile-tabs-content">

            <!-- Personal Info Tab -->
            <?php if ($activeTab === 'personal'): ?>
                <div class="profile-tab-pane">
                    <div class="profile-view-mode" id="profile-personal-view">
                        <div class="profile-info-grid">
                            <div class="profile-card">
                                <h3>Contact Information</h3>

                                <div class="profile-info-item">
                                    <label class="profile-info-label">Full Name</label>
                                    <p class="profile-info-value"><?= htmlspecialchars($userName) ?></p>
                                </div>
                                <div class="profile-info-item">
                                    <label class="profile-info-label">Email</label>
                                    <p class="profile-info-value"><?= htmlspecialchars($userEmail) ?></p>
                                </div>
                                <div class="profile-info-item">
                                    <label class="profile-info-label">Contact Number</label>
                                    <p class="profile-info-value"><?= !empty($userContact) ? htmlspecialchars($userContact) : '<em>Not provided</em>' ?></p>
                                </div>
                                <div class="profile-info-item">
                                    <label class="profile-info-label">Address</label>
                                    <p class="profile-info-value">
                                        <?= !empty($userAddress) ? htmlspecialchars($userAddress) : '<em>Not provided</em>' ?> 
                                    </p>
                                </div>

                                <div class="profile-info-item">
                                    <label class="profile-info-label">Online Presence</label>
                                    <div class="profile-social-links">
                                        <?php if (!empty($userGithub)): ?>
                                            <a href="<?= htmlspecialchars($userGithub) ?>" target="_blank" rel="noopener" class="profile-social-link">
                                                <svg class="profile-social-icon" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v 3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                                </svg>
                                                GitHub
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($userLinkedin)): ?>
                                            <a href="<?= htmlspecialchars($userLinkedin) ?>" target="_blank" rel="noopener" class="profile-social-link">
                                                <svg class="profile-social-icon" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"/>
                                                </svg>
                                                LinkedIn
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($userFacebook)): ?>
                                            <a href="<?= htmlspecialchars($userFacebook) ?>" target="_blank" rel="noopener" class="profile-social-link">
                                                <svg class="profile-social-icon" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                                Facebook
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($userWebsite)): ?>
                                            <a href="<?= htmlspecialchars($userWebsite) ?>" target="_blank" rel="noopener" class="profile-social-link">
                                                <svg class="profile-social-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                                Website
                                            </a>
                                        <?php endif; ?>
                                        <?php if (empty($userGithub) && empty($userLinkedin) && empty($userFacebook) && empty($userWebsite)): ?>
                                        <p class="profile-info-value"><em>No links added</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Professional Background -->
                        <div class="profile-card">
                            <h3>Professional Background</h3>
                            <div class="profile-info-item">
                                <label class="profile-info-label">Role</label>
                                <p class="profile-info-value"><?= htmlspecialchars(ucfirst($userRole)) ?></p>
                            </div>
                            <div class="profile-info-item profile-info-item-full">
                                <label class="profile-info-label">Bio</label>
                                <p class="profile-info-value"><?= !empty($userBio) ? htmlspecialchars($userBio) : '<em>No bio added yet.</em>' ?></p>
                            </div>
                            
                            <div class="profile-info-item">
                                <label class="profile-info-label">Education</label>
                                <p class="profile-info-value">
                                    <?= !empty($userEducation) ? htmlspecialchars($userEducation) : '<em>Not added</em>' ?>
                                </p>
                            </div>

                            <div class="profile-info-item">
                                <label class="profile-info-label">Previous Experience</label>
                                <p class="profile-info-value">
                                    <?= !empty($userExperience) ? htmlspecialchars($userExperience) : '<em>Not added</em>' ?>
                                </p>
                            </div>
                            <div class="profile-info-item">
                                <label class="profile-info-label">Skills</label>
                                <p class="profile-info-value skills-container">
                                    <?php
                                    if(!empty($userSkills)){
                                        $skillsArray = explode(",", $userSkills);

                                        foreach($skillsArray as $skill){
                                            echo '<span class="skill-badge">'.htmlspecialchars(trim($skill)).'</span>';
                                        }
                                    }else{
                                        echo "<em>No skills added</em>";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-edit-mode" id="profile-personal-edit" style="display: none;">
                    <form method="POST" action="profile.php?tab=<?= $activeTab ?>">
                        <div class="profile-form-grid">
                            <div class="profile-form-group">
                                <label for="profile-name" class="profile-form-label">Full Name *</label>
                                <input type="text" id="profile-name" name="name" class="profile-form-input" 
                                value="<?= htmlspecialchars($userName) ?>" required>
                            </div>
                            <div class="profile-form-group">
                                <label for="profile-contact" class="profile-form-label">Contact Number *</label>
                                <input type="tel" id="profile-contact" name="contact" class="profile-form-input" 
                                value="<?= htmlspecialchars($userContact) ?>" required>
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label for="profile-bio" class="profile-form-label">Bio</label>
                                <textarea id="profile-bio" name="bio" class="profile-form-textarea" rows="4" 
                                placeholder="Tell us about yourself..."><?= htmlspecialchars($userBio) ?></textarea>
                            </div>
                            <div class="profile-form-divider profile-form-group-full">
                                <h4>Online Presence</h4>
                            </div>

                            <div class="profile-form-group profile-form-group-full">
                                <label for="profile-github" class="profile-form-label">GitHub Profile</label>
                                <input type="url" id="profile-github" name="github" class="profile-form-input" 
                                value="<?= htmlspecialchars($userGithub) ?>" 
                                placeholder="https://github.com/yourusername">
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label for="profile-linkedin" class="profile-form-label">LinkedIn Profile</label>
                                <input type="url" id="profile-linkedin" name="linkedin" class="profile-form-input" 
                                value="<?= htmlspecialchars($userLinkedin) ?>" 
                                placeholder="https://linkedin.com/in/yourusername">
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label for="profile-facebook" class="profile-form-label">Facebook Profile</label>
                                <input type="url" id="profile-facebook" name="facebook" class="profile-form-input" 
                                value="<?= htmlspecialchars($userFacebook) ?>" 
                                placeholder="https://facebook.com/yourusername">
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label for="profile-website" class="profile-form-label">Website</label>
                                <input type="url" id="profile-website" name="website" class="profile-form-input" 
                                value="<?= htmlspecialchars($userWebsite) ?>" 
                                placeholder="https://yourwebsite.com">
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label class="profile-form-label">Address</label>
                                <input type="text" name="address" class="profile-form-input"
                                value="<?= htmlspecialchars($userAddress) ?>"
                                placeholder="City, Province, Country">
                            </div>
                            <div class="profile-form-group profile-form-group-full">
                                <label class="profile-form-label">Education</label>
                                <input type="text" name="education" class="profile-form-input"
                                value="<?= htmlspecialchars($userEducation) ?>"
                                placeholder="BS Computer Science - University">
                            </div>

                            <div class="profile-form-group profile-form-group-full">
                                <label class="profile-form-label">Previous Experience</label>
                                <textarea name="experience" class="profile-form-textarea" rows="3">
                                    <?= htmlspecialchars($userExperience) ?>
                                </textarea>
                            </div>
                            <div class="profile-form-group profile-form-group-full">

                                <label class="profile-form-label">Skills</label>

                                <div class="skills-wrapper">
                                    <select id="skillSelect" class="profile-form-select">
                                        <option value="">Select Skill</option>
                                        <option value="Web Development">Web Development</option>
                                        <option value="UI/UX Design">UI/UX Design</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="AI / Machine Learning">AI / Machine Learning</option>
                                        <option value="Data Analytics">Data Analytics</option>
                                        <option value="Cybersecurity">Cybersecurity</option>
                                        <option value="Mobile Development">Mobile Development</option>
                                    </select>

                                    <div id="selectedSkills" class="skills-container"></div>

                                    <input type="hidden" name="skills" id="skillsInput">

                                </div>

                            </div>

                        </div>
                        <div class="profile-form-actions">
                            <button type="button" class="profile-btn profile-btn-outline" onclick="profileToggleEditMode()">
                                Cancel
                            </button>
                            <button type="submit" name="update_personal_info" class="profile-btn profile-btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Startup Details Tab -->
        <?php if ($activeTab === 'startup'): ?>
            <div class="profile-tab-pane">
                <div class="profile-view-mode" id="profile-startup-view">
                    <div class="profile-info-grid">

                        <div class="profile-info-item">
                            <label class="profile-info-label">Startup Name</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userStartupName) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Industry</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userIndustry) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Startup Stage</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userStartupStage) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Founded</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userFoundedYear) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Team Size</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userTeamSize) ?></p>
                        </div>

                        <div class="profile-info-item profile-info-item-full">
                            <label class="profile-info-label">Description</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userStartupDescription) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Target Market</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userTargetMarket) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Revenue Model</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userRevenueModel) ?></p>
                        </div>

                        <div class="profile-info-item">
                            <label class="profile-info-label">Funding Status</label>
                            <p class="profile-info-value"><?= htmlspecialchars($userFundingStatus) ?></p>
                        </div>

                    </div>
                </div>

                <div class="profile-edit-mode" id="profile-startup-edit" style="display: none;">
                    <form method="POST" action="profile.php?tab=<?= $activeTab ?>">
                        <div class="profile-form-grid">

                            <div class="profile-form-group">
                                <label class="profile-form-label">Startup Name</label>
                                <input type="text" name="startup_name" class="profile-form-input"
                                value="<?= htmlspecialchars($userStartupName) ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Industry</label>
                                <input type="text" name="industry" class="profile-form-input"
                                value="<?= htmlspecialchars($userIndustry) ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Startup Stage</label>
                                <select name="startup_stage" class="profile-form-select">
                                    <option value="Idea" <?= $userStartupStage=='Idea'?'selected':'' ?>>Idea</option>
                                    <option value="MVP" <?= $userStartupStage=='MVP'?'selected':'' ?>>MVP</option>
                                    <option value="Growth" <?= $userStartupStage=='Growth'?'selected':'' ?>>Growth</option>
                                </select>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Funding Status</label>
                                <select name="funding_status" class="profile-form-select">
                                    <option value="Bootstrapped" <?= $userFundingStatus=='Bootstrapped'?'selected':'' ?>>Bootstrapped</option>
                                    <option value="Seed" <?= $userFundingStatus=='Seed'?'selected':'' ?>>Seed</option>
                                    <option value="Series A" <?= $userFundingStatus=='Series A'?'selected':'' ?>>Series A</option>
                                </select>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Founded Year</label>
                                <input type="text" name="founded_year" class="profile-form-input"
                                value="<?= htmlspecialchars($userFoundedYear) ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Team Size</label>
                                <input type="text" name="team_size" class="profile-form-input"
                                value="<?= htmlspecialchars($userTeamSize) ?>">
                            </div>

                            <div class="profile-form-group profile-form-group-full">
                                <label class="profile-form-label">Description</label>
                                <textarea name="startup_description" class="profile-form-textarea"><?= htmlspecialchars($userStartupDescription) ?></textarea>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Target Market</label>
                                <input type="text" name="target_market" class="profile-form-input"
                                value="<?= htmlspecialchars($userTargetMarket) ?>">
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Revenue Model</label>
                                <input type="text" name="revenue_model" class="profile-form-input"
                                value="<?= htmlspecialchars($userRevenueModel) ?>">
                            </div>



                        </div>
                        <div class="profile-form-actions">
                            <button type="button" class="profile-btn profile-btn-outline" onclick="profileToggleEditMode()">
                                Cancel
                            </button>
                            <button type="submit" name="update_startup_details" class="profile-btn profile-btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Documents Tab -->
        <?php if ($activeTab === 'documents'): ?>
            <div class="profile-tab-pane">
                <div class="profile-documents-section">
                    <div class="profile-section-header">
                        <h3>Upload Documents</h3>
                        <p class="profile-section-description">Upload important documents (Max 5MB, PDF/JPG/PNG)</p>
                    </div>

                    <form method="POST" action="profile.php" enctype="multipart/form-data" class="profile-upload-form">
                        <div class="profile-form-grid">
                            <div class="profile-form-group">
                                <label for="profile-document-type" class="profile-form-label">Document Type *</label>
                                <select id="profile-document-type" name="document_type" class="profile-form-select" required>
                                    <option value="">Select type</option>
                                    <option value="resume">Resume/CV</option>
                                    <option value="id">ID Document</option>
                                    <option value="certificate">Certificate</option>
                                    <option value="business_plan">Business Plan</option>
                                    <option value="pitch_deck">Pitch Deck</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="profile-form-group">
                                <label for="profile-document" class="profile-form-label">Choose File *</label>
                                <input type="file" id="profile-document" name="document" class="profile-form-file" 
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                        </div>
                        <div class="profile-form-actions">
                            <button type="submit" name="upload_document" class="profile-btn profile-btn-primary">
                                <svg class="profile-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Upload Document
                            </button>
                        </div>
                    </form>

                    <div class="profile-documents-list">
                        <h4>Uploaded Documents</h4>
                        <?php if (count($documents) > 0): ?>
                            <div class="profile-documents-grid">
                                <?php foreach ($documents as $doc): ?>
                                    <div class="profile-document-card">
                                        <div class="profile-document-icon">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7a2 2 0 11-4 0 2 2 0 014 0zM3 20a1 1 0 001 1h12a1 1 0 001-1V10a1 1 0 00-1-1H4a1 1 0 00-1 1v10z"></path>
                                            </svg>
                                        </div>
                                        <div class="profile-document-info">
                                            <h5 class="profile-document-name"><?= htmlspecialchars($doc['file_name']) ?></h5>
                                            <p class="profile-document-type"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $doc['document_type']))) ?></p>
                                            <p class="profile-document-date">
                                                <?= date('M d, Y', strtotime($doc['uploaded_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="profile-document-actions">
                                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="profile-btn-icon-small" title="View">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" download class="profile-btn-icon-small" title="Download">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="profile-empty-state">
                                <svg class="profile-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>No documents uploaded yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Settings Tab -->
        <?php if ($activeTab === 'settings'): ?>
            <div class="profile-tab-pane">
                <form method="POST" action="profile.php?tab=<?= $activeTab ?>">
                    <div class="profile-form-grid">
                        <div class="profile-form-group profile-form-group-full">
                            <label for="profile-email" class="profile-form-label">Email Address *</label>
                            <input type="email" id="profile-email" name="email" class="profile-form-input" 
                            value="<?= htmlspecialchars($userEmail) ?>" required>
                        </div>

                        <div class="profile-form-divider profile-form-group-full">
                            <h4>Change Password</h4>
                            <p class="profile-section-description">Leave blank if you don't want to change your password</p>
                        </div>

                        <div class="profile-form-group profile-form-group-full">
                            <label for="profile-current-password" class="profile-form-label">Current Password</label>
                            <div class="profile-password-wrapper">
                                <input type="password" id="profile-current-password" name="current_password" 
                                class="profile-form-input profile-password-input" 
                                placeholder="Enter current password">
                                <button type="button" class="profile-password-toggle" onclick="profileTogglePassword('profile-current-password')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="profile-form-group profile-form-group-full">

                            <label>Email Notifications</label>

                            <label>
                                <input type="checkbox" name="notify_applications" checked>
                                Receive updates about applications
                            </label>

                            <label>
                                <input type="checkbox" name="notify_announcements" checked>
                                Receive announcements and news
                            </label>

                            <label>
                                <input type="checkbox" name="notify_weekly">
                                Receive weekly digest
                            </label>

                        </div>
                        <div class="profile-form-group profile-form-group-full">

                            <label>Profile Visibility</label>

                            <select name="visibility" class="profile-form-select">

                                <option value="public">Public (Visible to incubatees)</option>
                                <option value="private">Private (Only visible to admins)</option>

                            </select>

                        </div>

                        <div class="profile-form-group">
                            <label for="profile-new-password" class="profile-form-label">New Password</label>
                            <div class="profile-password-wrapper">
                                <input type="password" id="profile-new-password" name="new_password" 
                                class="profile-form-input profile-password-input" 
                                placeholder="Enter new password (min 8 characters)">
                                <button type="button" class="profile-password-toggle" onclick="profileTogglePassword('profile-new-password')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="profile-form-group">
                            <label for="profile-confirm-password" class="profile-form-label">Confirm New Password</label>
                            <div class="profile-password-wrapper">
                                <input type="password" id="profile-confirm-password" name="confirm_password" 
                                class="profile-form-input profile-password-input" 
                                placeholder="Confirm new password">
                                <button type="button" class="profile-password-toggle" onclick="profileTogglePassword('profile-confirm-password')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="profile-form-actions">
                        <button type="submit" name="update_settings" class="profile-btn profile-btn-primary">
                            <svg class="profile-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V3"></path>
                            </svg>
                            Save Settings
                        </button>
                    </div>
                </form>

                <!-- Danger Zone -->
                <div class="profile-danger-zone">
                    <h4>Danger Zone</h4>
                    <p class="profile-section-description">Once you log out, you'll need to log in again to access your account.</p>
                    <a href="logout.php" class="profile-btn profile-btn-destructive">
                        <svg class="profile-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    function profileSwitchTab(event, tabName) {
    event.preventDefault(); // Prevent the browser from jumping
    const allTabs = document.querySelectorAll('.profile-tab-pane');
    allTabs.forEach(tab => tab.style.display = 'none');

    const activeTabPane = document.getElementById('profile-' + tabName + '-view');
    if(activeTabPane){
        activeTabPane.style.display = 'block';
    }

    // Optional: highlight active tab link
    document.querySelectorAll('.profile-tab-link').forEach(link => {
        link.classList.remove('profile-tab-link-active');
    });
    event.currentTarget.classList.add('profile-tab-link-active');

    // Update URL without scrolling
    history.replaceState(null, '', '?tab=' + tabName);
}
        // Get currently active tab
    function profileGetActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('tab') || 'personal';
    }

        // Toggle Edit Mode - Fixed to work with all tabs
function profileToggleEditMode() {
    const activeTab = profileGetActiveTab();
    const viewMode = document.getElementById('profile-' + activeTab + '-view');
    const editMode = document.getElementById('profile-' + activeTab + '-edit');

    if (!viewMode || !editMode) {
        console.error('View or edit mode element not found for tab: ' + activeTab);
        return;
    }

    const isViewVisible = viewMode.style.display !== 'none';

    if (isViewVisible) {
        // Switch to edit mode
        viewMode.style.display = 'none';
        editMode.style.display = 'block';

        // Only scroll if edit pane is not fully visible
        const rect = editMode.getBoundingClientRect();
        if (rect.top < 0 || rect.bottom > window.innerHeight) {
            editMode.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    } else {
        // Switch back to view mode
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
    }
}

        // Toggle Password Visibility
    function profileTogglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const button = event.currentTarget;
        const svg = button.querySelector('svg');

        if (input.type === 'password') {
            input.type = 'text';
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>';
        } else {
            input.type = 'password';
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
        }
    }

        // Auto-hide toast messages
    document.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.profile-toast');
        if (toasts.length > 0) {
            setTimeout(() => {
                toasts.forEach(toast => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 300);
                });
            }, 5000);
        }
    });
    const select = document.getElementById("skillSelect");

    if(select){

        const container = document.getElementById("selectedSkills");
        const hiddenInput = document.getElementById("skillsInput");

        let skills = [];

        select.addEventListener("change", function(){

            const skill = this.value;

            if(skill && !skills.includes(skill)){

                skills.push(skill);

                const tag = document.createElement("div");
                tag.className = "skill-tag";
                tag.innerHTML = skill + ' <span class="skill-remove">×</span>';

                tag.querySelector(".skill-remove").onclick = function(){
                    container.removeChild(tag);
                    skills = skills.filter(s => s !== skill);
                    hiddenInput.value = skills.join(",");
                };

                container.appendChild(tag);
                hiddenInput.value = skills.join(",");
            }

            select.value = "";

        });

    }
</script>
</body>
</html>