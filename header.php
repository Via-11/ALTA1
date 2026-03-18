<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';
// Check login
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? 'Guest';

// Get active announcement
$announcementContent = null;
try {
    $stmt = $pdo->prepare("SELECT message FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute();
    $announcement = $stmt->fetch();
    if ($announcement) {
        $announcementContent = $announcement['message'];
    }
} catch (Exception $e) {
    // Silently fail
}

?>

<!-- Top Bar -->
<div class="top-bar" >
    <!--<img src="assets/aboutprtnrimg/dost.jpg" alt="DOST Logo" class="top-bar-logo">-->
    <p class="top-bar-text">🏆 DOST-PCIEERD ACCREDITED TECHNOLOGY BUSINESS INCUBATOR </p>
</div>

<!-- Main Navigation -->
<header class="main-header" id="site-header">
    <nav class="navbar-container">

        <!-- Logo Area -->
        <div class="logo-area">
            <div class="logo-text-wrapper">
                <img src="assets/updh4.png" alt="ALTA iHub Logo" class="main-logo-img">
            </div>
            <div class="logo-divider"></div>
        </div>

        <button id="hamburger-btn" class="hamburger-btn" aria-label="Toggle Menu">☰</button>

        <!-- Navigation Links -->
        <div class="nav-links-wrapper" id="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="services.php" class="nav-link">Services</a>
            <a href="booking.php" class="nav-link">Apply Now</a>
            <a href="announcements.php" class="nav-link">Announcements</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="KIST.php" class="nav-link">KIST</a>
            <a href="faq.php" class="nav-link">FAQ</a>
            
        </div>

        <!-- Right Side Buttons -->
        <div class="profile-buttons-area">

            <!-- Theme Toggle -->
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
                🌙
            </button>

            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="btn-outline-gold">
                    PROFILE
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-outline-gold">
                    Log In
                </a>
                <a href="register.php" class="btn-register">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("theme-toggle");
        const body = document.body;
        const header = document.getElementById("site-header");
        const hamburgerBtn = document.getElementById("hamburger-btn");
        const navLinks = document.getElementById("nav-links");

        // Load saved theme
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "light") {
            body.classList.add("light-theme");
            toggleBtn.textContent = "☀️";
        }

        toggleBtn.addEventListener("click", function () {
            body.classList.toggle("light-theme");

            if (body.classList.contains("light-theme")) {
                localStorage.setItem("theme", "light");
                toggleBtn.textContent = "☀️";
            } else {
                localStorage.setItem("theme", "dark");
                toggleBtn.textContent = "🌙";
            }
        });

        // Scroll transparency effect
        window.addEventListener("scroll", function () {
            if (window.scrollY > 50) {
                header.classList.add("scrolled");
            } else {
                header.classList.remove("scrolled");
            }
        });

        // Hamburger menu toggle
        hamburgerBtn.addEventListener("click", function () {
            navLinks.classList.toggle("open");
        });
    });
</script>
