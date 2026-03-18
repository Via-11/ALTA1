<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$userName  = $_SESSION['user_name'] ?? 'User';
$userRole  = $_SESSION['user_role'] ?? 'Student';

try {

       $stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
       $stmt->execute([$user_id]);
       $user = $stmt->fetch();

       $stmt = $pdo->prepare("
        SELECT startup_name, status, created_at
        FROM bookings
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 1
        ");
       $stmt->execute([$user_id]);
       $latest_request = $stmt->fetch();

       $stmt = $pdo->prepare("
        SELECT id, startup_name, industry,
        appointment_date,
        appointment_start_time,
        appointment_end_time
        FROM bookings
        WHERE user_id = ?
        AND appointment_date IS NOT NULL
        AND appointment_date >= CURDATE()
        ORDER BY appointment_date ASC, appointment_start_time ASC
        LIMIT 2
        ");
       $stmt->execute([$user_id]);
       $upcoming_bookings = $stmt->fetchAll();

       $stmt = $pdo->prepare("
        SELECT title, message, created_at
        FROM announcements
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT 3
        ");
       $stmt->execute();
       $announcements = $stmt->fetchAll();

       $stmtMessages = $pdo->prepare("
        SELECT *
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        ORDER BY created_at DESC
        ");
       $stmtMessages->execute([$user_id, $user_id]);
       $allMessages = $stmtMessages->fetchAll();

       $unreadMessages = [];
       $readMessages   = [];
       $sentMessages   = [];

       foreach ($allMessages as $msg) {

        /* SENT MESSAGES */
        if ($msg['sender_id'] == $user_id) {
            $sentMessages[] = $msg;
        }

        /* RECEIVED MESSAGES */
        if ($msg['receiver_id'] == $user_id) {

            if ($msg['status'] === 'unread') {
                $unreadMessages[] = $msg;
            } else {
                $readMessages[] = $msg;
            }

        }

    }

       $stmtUnreadMsg = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM messages
        WHERE receiver_id = ?
        AND status = 'unread'
        ");
       $stmtUnreadMsg->execute([$user_id]);
       $unreadCount = $stmtUnreadMsg->fetch()['count'] ?? 0;

       $stmtBookings = $pdo->prepare("
        SELECT id, startup_name, founder_name,
        email, phone, industry, stage,
        team_size, description,
        additional_comments, pitch_deck_path,
        status, appointment_date,
        appointment_start_time,
        appointment_end_time,
        created_at
        FROM bookings
        WHERE user_id = ?
        ORDER BY status ASC, created_at DESC
        ");
       $stmtBookings->execute([$user_id]);
       $resultBookings = $stmtBookings->fetchAll();

       $stmtOtherUsers = $pdo->prepare("
        SELECT user_id, name
        FROM users
        WHERE user_id != ?
        ORDER BY name ASC
        LIMIT 50
        ");
       $stmtOtherUsers->execute([$user_id]);
       $otherUsers = $stmtOtherUsers->fetchAll();

       $pendingBookings   = [];
       $approvedBookings  = [];
       $completedBookings = [];
       $rejectedBookings  = [];

       foreach ($resultBookings as $booking) {
        switch ($booking['status']) {
            case 'pending':
            $pendingBookings[] = $booking;
            break;
            case 'approved':
            $approvedBookings[] = $booking;
            break;
            case 'completed':
            $completedBookings[] = $booking;
            break;
            case 'rejected':
            $rejectedBookings[] = $booking;
            break;
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

   $stmtAdmin = $pdo->prepare("
    SELECT user_id, name
    FROM users
    WHERE role = 'admin'
    LIMIT 1
    ");
   $stmtAdmin->execute();
   $adminUser = $stmtAdmin->fetch();

   ?>
   <!DOCTYPE html>
   <html lang="en">
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <style>
        .tab-content { display: none; animation: fadeIn 0.3s ease-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }
        .dashboard-mt-3 { margin-top: 1rem; }
        
        /* Details Modal */
        .dashboard-details-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .dashboard-details-modal.active {
            display: flex;
        }

        .dashboard-details-content {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            margin: auto;
        }

        .dashboard-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem;
            border-bottom: 1px solid var(--border);
        }

        .dashboard-details-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--foreground);
            margin: 0;
        }

        .dashboard-details-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--muted-foreground);
            cursor: pointer;
            transition: color 0.3s;
        }

        .dashboard-details-close:hover {
            color: var(--foreground);
        }

        .dashboard-details-body {
            padding: 2rem;
        }

        .dashboard-details-section {
            margin-bottom: 2rem;
        }

        .dashboard-details-section:last-child {
            margin-bottom: 0;
        }

        .dashboard-details-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--brand-gold);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dashboard-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .dashboard-details-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .dashboard-details-item-full {
            grid-column: 1 / -1;
        }

        .dashboard-details-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dashboard-details-value {
            font-size: 1rem;
            color: var(--foreground);
            line-height: 1.5;
        }

        .dashboard-details-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--glass-light);
            color: var(--brand-gold);
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .messages-form-group {
            margin-bottom: 1rem;
        }

        .messages-form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--foreground);
            margin-bottom: 0.5rem;
        }

        .messages-form-textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--input-background);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            color: var(--foreground);
            font-size: 0.95rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .messages-form-textarea:focus {
            outline: none;
            border-color: var(--brand-gold);
            box-shadow: 0 0 0 3px rgba(194, 139, 10, 0.1);
        }
    </style>
</head>
<body>

    <div class="dashboard-container dashboard-wrapper">

        <!-- Welcome Card -->
        <div class="dashboard-card dashboard-gradient-card dashboard-mb-8">
            <div class="dashboard-card-content dashboard-p-8">
                <div class="dashboard-flex-row dashboard-items-center dashboard-justify-between dashboard-gap-4">
                    <div>
                        <h1 class="dashboard-welcome-text"><?php echo isset($_SESSION['user_name']) ? "Welcome back, " . htmlspecialchars($_SESSION['user_name']) : "Powered by UPHSD"; ?>👋</h1>
                        <div class="dashboard-badge"><?= htmlspecialchars($userRole) ?></div>
                    </div>
                    <div class="dashboard-flex-row dashboard-gap-3">
                        <a href="booking.php" class="dashboard-btn dashboard-btn-primary">
                            <svg class="dashboard-icon dashboard-mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Apply for Services
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Switcher -->
        <div class="tab-switcher dashboard-mb-4">
            <button class="tab-btn active" id="btn-home" type="button" onclick="dashboardSwitchTab('home')">🏠 Home</button>
            <button class="tab-btn" id="btn-requests" type="button" onclick="dashboardSwitchTab('requests')">📋 Requests</button>
            <button class="tab-btn" id="btn-messages" type="button" onclick="dashboardSwitchTab('messages')">✉️ Messages <span class="badge"><?= $unreadCount ?></span></button>
        </div>

        <!-- HOME TAB -->
        <section id="homeTab" class="tab-content active">

            <div class="dashboard-grid dashboard-mb-8">
                <!-- Quick Actions -->
                <aside class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">Quick Actions</h3>
                        <p class="dashboard-card-description">Frequently used features</p>
                    </div>
                    <div class="dashboard-card-content dashboard-space-y-3">
                        <a href="booking.php" class="dashboard-btn dashboard-btn-outline dashboard-full-width">
                            <svg class="dashboard-icon dashboard-mr-3 dashboard-text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Apply for Incubation
                        </a>
                        <a href="services.php" class="dashboard-btn dashboard-btn-outline dashboard-full-width">
                            <svg class="dashboard-icon dashboard-mr-3 dashboard-text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            View Services
                        </a>
                        <a href="#" class="dashboard-btn dashboard-btn-outline dashboard-full-width" onclick="dashboardOpenComposeModal(); return false;">
                            <svg class="dashboard-icon dashboard-mr-3 dashboard-text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Send Message
                        </a>
                    </div>
                </aside>

                <!-- Application Status -->
                <section class="dashboard-col-span-2">
                    <div class="dashboard-card">

                        <!-- Card Header -->
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">Application Status Tracker</h3>
                            <p class="card-description">Track your submissions and progress</p>
                        </div>

                        <!-- Card Content -->
                        <div class="dashboard-card-content">

                            <?php if ($latest_request): ?>

                                <?php
                // Progress logic
                                $progress = 50;

                                if ($latest_request['status'] == 'approved') {
                                    $progress = 100;
                                } elseif ($latest_request['status'] == 'rejected') {
                                    $progress = 100;
                                }
                                $badge_class = 'dashboard-badge-warning';
                                if ($latest_request['status'] == 'approved') {
                                    $badge_class = 'dashboard-badge-success';
                                } elseif ($latest_request['status'] == 'rejected') {
                                    $badge_class = 'dashboard-badge-danger';
                                }
                                $icon_class = 'dashboard-status-under-review';
                                if ($latest_request['status'] == 'approved') {
                                    $icon_class = 'dashboard-status-approved';
                                } elseif ($latest_request['status'] == 'rejected') {
                                    $icon_class = 'dashboard-status-rejected';
                                }
                                ?>
                                <div class="dashboard-status-item dashboard-mb-6">
                                    <!-- Top Row -->
                                    <div class="dashboard-flex-row dashboard-justify-between dashboard-mb-3">
                                        <!-- Left Side -->
                                        <div class="dashboard-flex-row dashboard-items-center dashboard-gap-3">
                                            <div class="dashboard-status-icon <?= $icon_class ?>">
                                                <svg class="dashboard-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <?php if($latest_request['status'] == 'approved'): ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    <?php elseif($latest_request['status'] == 'rejected'): ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"/>
                                                    <?php else: ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="dashboard-item-heading">
                                                    Incubation Program Application
                                                </h4>
                                                <p class="dashboard-item-subtext">
                                                    <?= htmlspecialchars($latest_request['startup_name']) ?>
                                                    • Submitted:
                                                    <?= date('M d, Y', strtotime($latest_request['created_at'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Status Badge -->
                                        <span class="dashboard-badge <?= $badge_class ?>">
                                            <?= ucfirst($latest_request['status']) ?>
                                        </span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="dashboard-progress-bar">
                                        <div class="dashboard-progress-fill"
                                        style="width: <?= $progress ?>%">
                                    </div>
                                </div>
                                <!-- Progress Footer -->
                                <div class="dashboard-flex-row dashboard-justify-between dashboard-progress-text">
                                    <span>Application Submitted</span>
                                    <span><?= $progress ?>% Complete</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="dashboard-item-subtext">
                                No active service requests found.
                                <a href="booking.php" style="color: var(--brand-gold);">
                                    Apply now
                                </a>.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
<!-- Announcements & Bookings -->
<div class="dashboard-bottom-grid">
    <!-- Announcements -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3 class="dashboard-card-title">Recent Announcements</h3>
            <a href="announcements.php" class="dashboard-view-all">View All</a>
        </div>
        <div class="dashboard-card-content">
            <?php if($announcements): ?>
                <?php foreach($announcements as $news): ?>
                    <div class="dashboard-announcement-item dashboard-mb-4 dashboard-pb-4 dashboard-border-bottom">
                        <div class="dashboard-flex-row dashboard-items-start dashboard-gap-3">
                            <!-- Priority Badge -->
                            <span class="dashboard-badge dashboard-badge-medium">Info</span>
                            <div class="dashboard-flex-1">
                                <h4 class="dashboard-item-heading dashboard-mb-1">
                                    <?= htmlspecialchars($news['title']) ?>
                                </h4>
                                <p class="dashboard-item-subtext dashboard-mb-2">
                                    <?= htmlspecialchars(substr($news['message'],0,90)) ?>...
                                </p>
                                <span class="dashboard-item-subtext dashboard-text-xs">
                                    Posted <?= date('M d, Y', strtotime($news['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="dashboard-item-subtext">No announcements available.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Upcoming Bookings -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3 class="dashboard-card-title">Upcoming Bookings</h3>
            <a href="booking.php" class="dashboard-view-all">View All</a>
        </div>
        <div class="dashboard-card-content">
            <?php if($upcoming_bookings): ?>
                <?php foreach($upcoming_bookings as $booking): 
                    $appointmentDate = $booking['appointment_date'] ? new DateTime($booking['appointment_date']) : null;
                    ?>
                    <div class="dashboard-booking-item dashboard-mb-4">
                        <div class="dashboard-flex-row dashboard-items-start dashboard-gap-4">
                            <!-- Date Badge -->
                            <div class="dashboard-date-badge">
                                <div class="dashboard-date-day">
                                    <?= $appointmentDate ? $appointmentDate->format('d') : 'TBD' ?>
                                </div>
                                <div class="dashboard-date-month">
                                    <?= $appointmentDate ? strtoupper($appointmentDate->format('M')) : '---' ?>
                                </div>
                            </div>
                            <div class="dashboard-flex-1">
                                <h4 class="dashboard-item-heading dashboard-mb-1">
                                    <?= htmlspecialchars($booking['startup_name']) ?>
                                </h4>
                                <!-- Time -->
                                <p class="dashboard-item-subtext dashboard-mb-2">
                                    <svg class="dashboard-icon-xs dashboard-inline dashboard-mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?= $booking['appointment_start_time'] ? date('h:i A', strtotime($booking['appointment_start_time'])) : 'TBD' ?>
                                    <?= $booking['appointment_end_time'] ? date('h:i A', strtotime($booking['appointment_end_time'])) : 'TBD' ?>
                                </p>
                                <!-- Location / Type -->
                                <p class="dashboard-item-subtext dashboard-text-xs">
                                    <svg class="dashboard-icon-xs dashboard-inline dashboard-mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <?= htmlspecialchars($booking['industry']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="dashboard-item-subtext">No upcoming bookings scheduled.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</section>

<!-- REQUESTS TAB - WITH SEE DETAILS MODAL -->
<section id="requestsTab" class="tab-content">
    <h2 class="title">Requests & Transactions</h2>
    <div class="request-stats-grid">
        <div class="request-stat-card"><p class="stat-number color-warning"><?= count($pendingBookings) ?></p><p>Pending</p></div>
        <div class="request-stat-card"><p class="stat-number color-info"><?= count($approvedBookings) ?></p><p>Approved</p></div>
        <div class="request-stat-card"><p class="stat-number color-success"><?= count($completedBookings) ?></p><p>Completed</p></div>
        <div class="request-stat-card"><p class="stat-number color-danger"><?= count($rejectedBookings) ?></p><p>Rejected</p></div>
    </div>
    <div class="request-sections">
        <?php if(!empty($pendingBookings)): ?>
            <h3 class="request-section-label">Pending Review</h3>
            <?php foreach($pendingBookings as $b): ?>
                <div class="request-card card-flex">
                    <div class="icon-box bg-warning">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-md">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="request-card-content">
                        <div class="request-card-header-row">
                            <h4 class="service-name-text"><?= htmlspecialchars($b['startup_name']) ?></h4>
                            <span class="request-badge badge-warning">Pending</span>
                        </div>
                        <p class="text-sm text-muted">Industry: <?= htmlspecialchars($b['industry']) ?></p>
                        <div class="request-card-footer">
                            <span>ID: REQ-<?= $b['id'] ?></span>
                            <span>Created: <?= date('Y-m-d', strtotime($b['created_at'])) ?></span>
                        </div>
                        <button type="button" class="dashboard-btn dashboard-btn-outline dashboard-mt-3" onclick="dashboardOpenDetailsModal(<?= htmlspecialchars(json_encode($b)) ?>)">
                            See Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($approvedBookings)): ?>
            <h3 class="request-section-label">Approved</h3>
            <?php foreach($approvedBookings as $b): ?>
                <div class="request-card card-flex">
                    <div class="icon-box bg-info">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-md">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="request-card-content">
                        <div class="request-card-header-row">
                            <h4 class="service-name-text"><?= htmlspecialchars($b['startup_name']) ?></h4>
                            <span class="request-badge badge-info">Approved</span>
                        </div>
                        <p class="text-sm text-muted">Industry: <?= htmlspecialchars($b['industry']) ?></p>
                        <div class="request-card-footer">
                            <span>ID: REQ-<?= $b['id'] ?></span>
                            <span>Created: <?= date('Y-m-d', strtotime($b['created_at'])) ?></span>
                        </div>
                        <button type="button" class="dashboard-btn dashboard-btn-outline dashboard-mt-3" onclick="dashboardOpenDetailsModal(<?= htmlspecialchars(json_encode($b)) ?>)">
                            See Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($completedBookings)): ?>
            <h3 class="request-section-label">Completed</h3>
            <?php foreach($completedBookings as $b): ?>
                <div class="request-card card-flex">
                    <div class="icon-box bg-success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-md">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="request-card-content">
                        <div class="request-card-header-row">
                            <h4 class="service-name-text"><?= htmlspecialchars($b['startup_name']) ?></h4>
                            <span class="request-badge badge-success">Completed</span>
                        </div>
                        <p class="text-sm text-muted">Industry: <?= htmlspecialchars($b['industry']) ?></p>
                        <div class="request-card-footer">
                            <span>ID: REQ-<?= $b['id'] ?></span>
                            <span>Created: <?= date('Y-m-d', strtotime($b['created_at'])) ?></span>
                        </div>
                        <button type="button" class="dashboard-btn dashboard-btn-outline dashboard-mt-3" onclick="dashboardOpenDetailsModal(<?= htmlspecialchars(json_encode($b)) ?>)">
                            See Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($rejectedBookings)): ?>
            <h3 class="request-section-label">Rejected</h3>
            <?php foreach($rejectedBookings as $b): ?>
                <div class="request-card card-flex">
                    <div class="icon-box bg-danger">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-md">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="request-card-content">
                        <div class="request-card-header-row">
                            <h4 class="service-name-text"><?= htmlspecialchars($b['startup_name']) ?></h4>
                            <span class="request-badge badge-danger">Rejected</span>
                        </div>
                        <p class="text-sm text-muted">Industry: <?= htmlspecialchars($b['industry']) ?></p>
                        <div class="request-card-footer">
                            <span>ID: REQ-<?= $b['id'] ?></span>
                            <span>Created: <?= date('Y-m-d', strtotime($b['created_at'])) ?></span>
                        </div>
                        <button type="button" class="dashboard-btn dashboard-btn-outline dashboard-mt-3" onclick="dashboardOpenDetailsModal(<?= htmlspecialchars(json_encode($b)) ?>)">
                            See Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- MESSAGES TAB -->
<section id="messagesTab" class="tab-content">

    <div class="min-h-screen bg-background py-8">
        <div class="container">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Messages</h1>
                    <p class="text-gray-400">Communication with mentors, admin, and other startups</p>
                </div>
                <button type="button" class="btn btn-primary" onclick="dashboardOpenComposeModal()">
                    <svg class="icon mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Message
                </button>
            </div>

            <div class="grid lg-grid-cols-3 gap-6">
                <!-- Message List -->
                <div class="messages-card">
                    <div class="messages-card-content p-4">
                        <div class="mb-4">
                            <input type="text" id="message-search" class="form-input" placeholder="Search messages...">

                            <div class="message-filter-tabs" style="margin-top:10px;">
                                <button class="msg-tab active" onclick="switchMessageTab('all')">All</button>
                                <button class="msg-tab" onclick="switchMessageTab('unread')">Unread</button>
                                <button class="msg-tab" onclick="switchMessageTab('read')">Read</button>
                                <button class="msg-tab" onclick="switchMessageTab('sent')">Sent</button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <?php if (!empty($allMessages)): ?>
                                <?php foreach($allMessages as $msg): ?>
                                    <div class="messages-list-item <?= ($msg['status'] === 'unread') ? 'unread' : 'read' ?>"
                                        data-type="<?=
                                        ($msg['sender_id'] == $user_id) ? 'sent' :
                                        (($msg['status'] === 'unread') ? 'unread' : 'read')
                                    ?>"
                                    data-id="<?= $msg['id'] ?>"
                                    data-sender-id="<?= $msg['sender_id'] ?>"
                                    data-subject="<?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>"
                                    data-message="<?= htmlspecialchars($msg['message']) ?>"
                                    data-sender="<?= htmlspecialchars($msg['sender_name'] ?? 'Admin') ?>"
                                    data-date="<?= date("M d, Y h:i A", strtotime($msg['created_at'])) ?>">

                                    <div class="flex items-start gap-3">
                                        <div class="avatar">
                                            <?= strtoupper(substr($msg['sender_name'] ?? 'AD', 0, 2)) ?>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-semibold text-white text-sm">
                                                    <?= htmlspecialchars($msg['sender_name'] ?? 'Admin') ?>
                                                </span>
                                            </div>

                                            <p class="text-sm text-white mb-1">
                                                <?= htmlspecialchars($msg['subject'] ?? 'No Subject') ?>
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                <?= htmlspecialchars(substr($msg['message'], 0, 50)) ?>...
                                            </p>

                                            <span class="text-xs text-gray-500">
                                                <?= date("M d", strtotime($msg['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-400">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Message Detail -->
            <div class="lg-col-span-2" id="message-detail" style="display: none;">
                <div class="messages-card">
                    <div class="messages-card-content p-0">
                        <!-- Header -->
                        <div class="p-6 border-bottom">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start gap-4">
                                    <div class="avatar" id="detail-avatar" style="width: 3rem; height: 3rem; font-size: 1.25rem;">
                                        --
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-white" id="detail-sender">Sender</h3>
                                        <p class="text-xs text-gray-500 mt-1" id="detail-date">Date</p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" class="btn btn-outline btn-sm" id="mark-read-btn" onclick="dashboardMarkAsRead()">Mark Read</button>
                                    <button type="button" class="btn btn-outline btn-sm" id="mark-unread-btn" onclick="dashboardMarkAsUnread()">Mark Unread</button>
                                    <button type="button" class="btn btn-outline btn-sm" onclick="dashboardDeleteMessage()">Delete</button>
                                </div>
                            </div>

                            <h2 class="text-xl font-semibold text-white" id="detail-subject">
                                Subject
                            </h2>
                        </div>

                        <!-- Message Content -->
                        <div class="p-6" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-gray-300" id="detail-message" style="white-space: pre-wrap; line-height: 1.6;">
                                Message content
                            </p>
                        </div>

                        <!-- Reply Form -->
                        <form class="messages-reply-form" id="reply-form">
                            <input type="hidden" name="message_id" id="message_id_input">
                            <div class="p-6 border-top">
                                <h4 class="font-semibold text-white mb-3">Reply</h4>
                                <textarea name="reply_message" class="messages-form-textarea mb-3" placeholder="Type your reply message..." rows="4" required></textarea>
                                <div class="flex items-center justify-between">
                                    <button type="button" class="btn btn-outline btn-sm btn-attach-file">
                                        <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        Attach File
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Send Reply
                                        <svg class="icon-sm ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</section>

</div>

<!-- Booking Details Modal -->
<div id="dashboardDetailsModal" class="dashboard-details-modal">
    <div class="dashboard-details-content">
        <div class="dashboard-details-header">
            <h2 id="detailsModalTitle">Application Details</h2>
            <button type="button" class="dashboard-details-close" onclick="dashboardCloseDetailsModal()">&times;</button>
        </div>
        <div class="dashboard-details-body" id="detailsModalBody">
            <!-- Details will be populated here -->
        </div>
    </div>
</div>

<!-- Compose Modal -->
<div id="compose-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div class="messages-card" style="max-width: 600px; width: 90%;">
        <div class="messages-card-content p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Compose New Message</h2>
                <button type="button" onclick="dashboardCloseComposeModal()" class="text-gray-400 hover:text-white">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="messages-compose-form">
                <!-- Recipient Selection -->
                <div class="messages-form-group">
                    <label class="messages-form-label" for="compose-recipient">Send To: *</label>
                    <select id="compose-recipient" name="recipient_id" class="form-select">
                        <!-- Admin Support -->
                        <option value="<?= $adminUser['user_id'] ?>">📧 Admin Support</option>

                        <!-- Other Users -->
                        <optgroup label="Other Users">
                            <?php foreach ($otherUsers as $otherUser): ?>
                                <option value="<?= $otherUser['user_id'] ?>"><?= htmlspecialchars($otherUser['name']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <!-- Subject -->
                <div class="messages-form-group">
                    <label class="messages-form-label" for="compose-subject">Subject: *</label>
                    <input type="text" id="compose-subject" name="subject" class="form-input" required placeholder="Enter subject line..." style="background: var(--input-background); border: 1px solid var(--border); color: var(--foreground);">
                </div>

                <!-- Message -->
                <div class="messages-form-group">
                    <label class="messages-form-label" for="compose-message">Message: *</label>
                    <textarea id="compose-message" name="message" class="messages-form-textarea" required rows="6" placeholder="Type your message here..."></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" class="btn btn-outline btn-sm btn-attach-file">
                        <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Attach File
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Send Message
                        <svg class="icon-sm ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Tab Switching
    function dashboardSwitchTab(tabName) {

        document.querySelectorAll('.tab-content').forEach(tab => 
            tab.classList.remove('active')
            );

        document.querySelectorAll('.tab-btn').forEach(btn => 
            btn.classList.remove('active')
            );

        document.getElementById(tabName + 'Tab').classList.add('active');
        document.getElementById('btn-' + tabName).classList.add('active');

    /* SAVE ACTIVE TAB */
        localStorage.setItem('dashboardActiveTab', tabName);
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {

        const savedTab = localStorage.getItem('dashboardActiveTab');

        if(savedTab){
            dashboardSwitchTab(savedTab);
        }else{
            dashboardSwitchTab('home');
        }

    });

    // Details Modal Functions
    function dashboardOpenDetailsModal(bookingData) {
        const modal = document.getElementById('dashboardDetailsModal');
        const body = document.getElementById('detailsModalBody');
        
        const appointmentDate = bookingData.appointment_date ? new Date(bookingData.appointment_date) : null;
        const dateStr = appointmentDate ? appointmentDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Not Set';
        
        let html = `
            <div class="dashboard-details-section">
                <h3 class="dashboard-details-section-title">Application Status</h3>
                <div class="dashboard-details-grid">
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Status</span>
                        <div>
                            <span class="dashboard-details-badge">${bookingData.status.toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Application ID</span>
                        <span class="dashboard-details-value">REQ-${bookingData.id}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Created Date</span>
                        <span class="dashboard-details-value">${new Date(bookingData.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-details-section">
                <h3 class="dashboard-details-section-title">Startup Information</h3>
                <div class="dashboard-details-grid">
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Startup Name</span>
                        <span class="dashboard-details-value">${bookingData.startup_name}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Founder Name</span>
                        <span class="dashboard-details-value">${bookingData.founder_name}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Email</span>
                        <span class="dashboard-details-value">${bookingData.email}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Phone</span>
                        <span class="dashboard-details-value">${bookingData.phone}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Industry</span>
                        <span class="dashboard-details-value">${bookingData.industry}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Startup Stage</span>
                        <span class="dashboard-details-value">${bookingData.stage}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Team Size</span>
                        <span class="dashboard-details-value">${bookingData.team_size}</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-details-section">
                <h3 class="dashboard-details-section-title">Description</h3>
                <div class="dashboard-details-item dashboard-details-item-full">
                    <span class="dashboard-details-value">${bookingData.description}</span>
                </div>
            </div>

            <div class="dashboard-details-section">
                <h3 class="dashboard-details-section-title">Session Details</h3>
                <div class="dashboard-details-grid">
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Appointment Date</span>
                        <span class="dashboard-details-value">${dateStr}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">Start Time</span>
                        <span class="dashboard-details-value">${bookingData.appointment_start_time ? new Date('2000-01-01 ' + bookingData.appointment_start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : 'Not Set'}</span>
                    </div>
                    <div class="dashboard-details-item">
                        <span class="dashboard-details-label">End Time</span>
                        <span class="dashboard-details-value">${bookingData.appointment_end_time ? new Date('2000-01-01 ' + bookingData.appointment_end_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : 'Not Set'}</span>
                    </div>
                </div>
            </div>
        `;

        if (bookingData.additional_comments) {
            html += `
                <div class="dashboard-details-section">
                    <h3 class="dashboard-details-section-title">Additional Comments</h3>
                    <div class="dashboard-details-item dashboard-details-item-full">
                        <span class="dashboard-details-value">${bookingData.additional_comments}</span>
                    </div>
                </div>
            `;
        }

        body.innerHTML = html;
        modal.classList.add('active');
    }

    function dashboardCloseDetailsModal() {
        document.getElementById('dashboardDetailsModal').classList.remove('active');
    }

    // Compose Modal Functions
    function dashboardOpenComposeModal() {
        document.getElementById('compose-modal').style.display = 'flex';
    }

    function dashboardCloseComposeModal() {
        document.getElementById('compose-modal').style.display = 'none';
    }

    // Message Functions
    const dashboardMessageItems = document.querySelectorAll('.messages-list-item');
    const dashboardMessageDetail = document.getElementById('message-detail');

    const dashboardDetailSender = document.getElementById('detail-sender');
    const dashboardDetailSubject = document.getElementById('detail-subject');
    const dashboardDetailMessage = document.getElementById('detail-message');
    const dashboardDetailDate = document.getElementById('detail-date');
    const dashboardDetailAvatar = document.getElementById('detail-avatar');
    const dashboardMessageIdInput = document.getElementById('message_id_input');
    let dashboardCurrentMessageId = null;

    dashboardMessageItems.forEach(item => {
        item.addEventListener('click', function () {
            const isActive = this.classList.contains('active');

            if (isActive) {
                this.classList.remove('active');
                dashboardMessageDetail.style.display = 'none';
                return;
            }

            dashboardMessageItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            const sender = this.dataset.sender;
            const subject = this.dataset.subject;
            const message = this.dataset.message;
            const date = this.dataset.date;
            const id = this.dataset.id;

            dashboardCurrentMessageId = id;
            dashboardMessageIdInput.value = id;

            dashboardDetailSender.textContent = sender;
            dashboardDetailSubject.textContent = subject;
            dashboardDetailMessage.textContent = message;
            dashboardDetailDate.textContent = date;
            dashboardDetailAvatar.textContent = sender.substring(0, 2).toUpperCase();
            const markReadBtn = document.getElementById('mark-read-btn');
            const markUnreadBtn = document.getElementById('mark-unread-btn');

            if (this.classList.contains('unread')) {
                markReadBtn.style.display = 'inline-block';
                markUnreadBtn.style.display = 'none';
            } else {
                markReadBtn.style.display = 'none';
                markUnreadBtn.style.display = 'inline-block';
            }

            dashboardMessageDetail.style.display = 'block';
        });
    });

    // Compose Form Handler
    const dashboardComposeForm = document.getElementById('messages-compose-form');
    if (dashboardComposeForm) {
        dashboardComposeForm.addEventListener('submit', e => {
            e.preventDefault();
            
            const receiverId = document.getElementById('compose-recipient').value;
            if (!receiverId) {
                alert('Please select a recipient');
                return;
            }

            const formData = new FormData(dashboardComposeForm);
            formData.set('receiver_id', receiverId);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    dashboardCloseComposeModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to send message'));
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            });
        });
    }

    // Reply Form Handler
    const dashboardReplyForm = document.getElementById('reply-form');
    if (dashboardReplyForm) {
        dashboardReplyForm.addEventListener('submit', e => {
            e.preventDefault();
            
            const messageId = document.getElementById('message_id_input').value;
            if (!messageId) {
                alert('Please select a message to reply to');
                return;
            }

            const formData = new FormData(dashboardReplyForm);

            fetch('reply_message.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Reply sent successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to send reply'));
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Network error. Try again.');
            });
        });
    }

    // Attach File buttons
    document.querySelectorAll('.btn-attach-file').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = btn.closest('form');
            let fileInput = form.querySelector('input[type="file"]');
            if (!fileInput) {
                fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'attachment';
                fileInput.style.display = 'none';
                form.appendChild(fileInput);
                fileInput.addEventListener('change', () => {
                    btn.innerHTML = `
                        <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Attached: ${fileInput.files[0].name}
                    `;
                });
            }
            fileInput.click();
        });
    });

    function dashboardMarkAsRead() {
        if (!dashboardCurrentMessageId) return;
        
        fetch('mark_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message_id=' + dashboardCurrentMessageId
        }).then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    function dashboardMarkAsUnread() {

        if (!dashboardCurrentMessageId) return;

        fetch('mark_unread.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message_id=' + dashboardCurrentMessageId
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error marking message as unread');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error');
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dashboardCloseDetailsModal();
            dashboardCloseComposeModal();
        }
    });
    function switchMessageTab(type){

        document.querySelectorAll('.msg-tab').forEach(btn=>{
            btn.classList.remove('active')
        })

        event.target.classList.add('active')

        const items = document.querySelectorAll('.messages-list-item')

        items.forEach(item=>{

            if(type === 'all'){
                item.style.display = 'block'
                return
            }

            const itemType = item.dataset.type

            if(itemType === type){
                item.style.display = 'block'
            }else{
                item.style.display = 'none'
            }

        })
    }
</script>

</body>
</html>