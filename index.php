<?php
session_start();
include 'db.php'; 
include 'header.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin/admin_index.php");
    exit();
}

$userName = $_SESSION['user_name'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;

try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
} catch (Exception $e) {
    $totalUsers = 0;
    $totalBookings = 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALTA iHUB - Innovation & Technology Business Incubator</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background"></div>
        <div class="hero-glow hero-glow-right"></div>
        <div class="hero-glow hero-glow-left"></div>
        
        <div class="container">
            <div class="hero-content">
<div class="hero-brand-section">
    <div class="hero-partnership-logos">
        <img src="assets/updh4.png" alt="ALTA-iHUB Logo" class="hero-main-logo">
        <div class="logo-divider"></div>
        <img src="assets/DOST.png" alt="DOST-NCR Logo" class="hero-partner-logo">
    </div>

    <div class="hero-university-logo">
        <img src="assets/UPHSD.png" alt="University of Perpetual Help System DALTA" class="uphsd-logo">
    </div>
</div>

                <div class="hero-badge">
                    <i class="fas fa-user-circle"></i>
                    <span> <?= isset($_SESSION['user_name']) ? "Welcome back, ".htmlspecialchars($_SESSION['user_name']) : "Powered by UPHSD"; ?>
                   </span>
                </div>
                
                <h1 class="hero-title">
                    <span class="hero-title-main">ALTA-INNOVATIONS HUB</span>
                    <span class="hero-title-gradient">(ALTA-iHUB)</span>
                </h1>
                
                <p class="hero-description">
                    ALTA-iHUB is an innovation and technology hub committed to startup incubation, 
                    research commercialization, and digital transformation.
                </p>
                
                <div class="hero-buttons">
                    <a href="#dashboard" class="btn btn-primary btn-lg">
                        Apply for Incubation
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#services" class="btn btn-outline btn-lg">Explore Services</a>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <!--<div class="stat-card">
                        <div class="stat-value">100+</div>
                        <div class="stat-label">Startups Incubated</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">₱50M+</div>
                        <div class="stat-label">Funding Facilitated</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">300+</div>
                        <div class="stat-label">Programs Conducted</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">95%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>-->
                    <div class="stat-card">
                        <div class="stat-value">Launching Soon</div>
                        <div class="stat-label">Startups Incubated</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">Recruiting Experts</div>
                        <div class="stat-label">Mentor Network</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">Coming Soon</div>
                        <div class="stat-label">Innovation Workshops</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">Growing</div>
                        <div class="stat-label">Partner Institutions</div>
                    </div>

                </div>
            </div>
        </div>
    </section>
<!-- Featured Announcement -->
<section class="featured-announcement">
    <div class="container">

        <?php
        try {
            $stmt = $pdo->prepare("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC LIMIT 1");
            $stmt->execute();
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $announcement = null;
        }

        if ($announcement):
        ?>

        <div class="announcement-card featured">

            <div class="announcement-header" style="display: flex; gap: 2rem; flex-wrap: wrap;">

                <!-- LEFT: Announcement Image -->
                <div class="announcement-image" style="flex: 1; min-width: 250px; max-width: 400px;">
                    <?php if (!empty($announcement['image'])): ?>
                        <div class="announcement-image-wrapper">
                            <img src="<?= htmlspecialchars($announcement['image']) ?>" 
                                 alt="<?= htmlspecialchars($announcement['title']) ?>" 
                                 class="announcement-image-large">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT: Announcement Content -->
                <div class="announcement-info" style="flex: 2; min-width: 300px;">

                    <div class="announcement-icon">
                        <i class="fas fa-rocket"></i>
                    </div>

                    <div class="announcement-badges">
                        <?php if (!empty($announcement['badge'])): ?>
                            <span class="badge badge-urgent"><?= htmlspecialchars($announcement['badge']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($announcement['badge_priority'])): ?>
                            <span class="badge badge-event"><?= htmlspecialchars($announcement['badge_priority']) ?></span>
                        <?php endif; ?>
                    </div>

                    <h2 class="announcement-title">
                        <?= htmlspecialchars($announcement['title']) ?>
                    </h2>

                    <?php if (!empty($announcement['subtitle'])): ?>
                        <p class="announcement-subtitle">
                            <?= htmlspecialchars($announcement['subtitle']) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($announcement['short_desc'])): ?>
                        <p class="announcement-description">
                            <?= htmlspecialchars($announcement['short_desc']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="announcement-details" style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">

                        <?php if (!empty($announcement['event_date'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <p class="detail-label">Event Dates</p>
                                    <p class="detail-value">
                                        <?= htmlspecialchars($announcement['event_date']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($announcement['location'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <p class="detail-label">Venue</p>
                                    <p class="detail-value">
                                        <?= htmlspecialchars($announcement['location']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="announcement-actions" style="margin-top: 1.5rem;">
                        <a href="announcements.php" class="btn btn-primary btn-lg">
                            <?= htmlspecialchars($announcement['button_text'] ?? 'Learn More & Register') ?>
                            <i class="fas fa-arrow-right"></i>
                        </a>

                        <button class="btn btn-outline btn-lg">
                            View All Announcements
                        </button>
                    </div>

                </div>
            </div>

        </div>

        <?php else: ?>
            <p>No latest announcements.</p>
        <?php endif; ?>

    </div>
</section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Services</h2>
                <p class="section-description">Comprehensive support for every stage of your startup journey</p>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="service-title">Innovation & Incubation</h3>
                    <p class="service-description">Startup programs, R2M mentorship, prototype development</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="service-title">Business Support</h3>
                    <p class="service-description">Mentorship, pitch training, legal & IP advisory</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3 class="service-title">Tech & Engineering</h3>
                    <p class="service-description">AI, IoT, renewable energy, and smart systems</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="service-title">Training & Capacity</h3>
                    <p class="service-description">Skills training, certifications, workshops</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="service-title">Industry Collaboration</h3>
                    <p class="service-description">Partnership programs, joint R&D, hackathons</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="service-title">Funding Support</h3>
                    <p class="service-description">Grant assistance, investor matching, demo days</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="service-title">Innovation Spaces</h3>
                    <p class="service-description">Co-working spaces, labs, testing areas</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="service-title">KIST Park Molino</h3>
                    <p class="service-description">Flagship innovation research hub</p>
                </div>
            </div>

            <div class="section-footer">
                <button class="btn btn-primary btn-lg">
                    View All Services
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="why-choose">
        <div class="container">
            <div class="why-choose-grid">
                <div class="why-choose-content">
                    <h2 class="section-title">Why Choose ALTA iHUB?</h2>
                    <p class="section-description">
                        We provide world-class incubation services backed by government accreditation, 
                        industry partnerships, and proven success stories.
                    </p>
                    
                    <div class="features-list">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>DOST-PCIEERD Accredited</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>State-of-the-art Facilities</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Expert Mentorship Network</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Industry Connections</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Government Funding Access</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Technology Commercialization</span>
                        </div>
                    </div>
                </div>

                <div class="feature-cards">
                    <div class="feature-card accent-orange">
                        <i class="fas fa-award"></i>
                        <h3>Accredited</h3>
                        <p>DOST-PCIEERD Certified</p>
                    </div>
                    <div class="feature-card accent-purple">
                        <i class="fas fa-chart-line"></i>
                        <h3>Growth Focus</h3>
                        <p>Scale Your Startup</p>
                    </div>
                    <div class="feature-card accent-blue">
                        <i class="fas fa-building"></i>
                        <h3>Modern Labs</h3>
                        <p>State-of-art Facilities</p>
                    </div>
                    <div class="feature-card accent-green">
                        <i class="fas fa-dollar-sign"></i>
                        <h3>Funding Access</h3>
                        <p>Connect with Investors</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your Startup?</h2>
                <p class="cta-description">
                    Join the ALTA iHUB community and get access to resources, mentorship, 
                    and funding opportunities.
                </p>
                <div class="cta-buttons">
                    <button class="btn btn-primary btn-lg">
                        Apply for Incubation
                        <i class="fas fa-rocket"></i>
                    </button>
                    <button class="btn btn-outline btn-lg">
                        Book a Space
                    </button>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<?php include 'footer.php'; ?>