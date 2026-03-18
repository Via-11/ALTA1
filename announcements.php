<?php
include 'db.php';
include 'header.php';


$db = isset($pdo) ? $pdo : $conn;

if ($db instanceof PDO) {
	$stmt = $db->prepare("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC");
	$stmt->execute();
	$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
	$stmt = $db->prepare("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC");
	$stmt->execute();
	$announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/* Group by type */
$latest = array_filter($announcements, fn($a) => $a['type'] === 'latest');
$ongoing = array_filter($announcements, fn($a) => $a['type'] === 'ongoing');
$upcoming = array_filter($announcements, fn($a) => $a['type'] === 'upcoming');
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Announcements & Events - ALTA iHUB</title>
	<link rel="stylesheet" href="styles/style1.css">
	<link rel="stylesheet" href="styles/announcement.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- HERO (UNCHANGED) -->
<section class="hero">
	<div class="container">
		<div class="hero-content">
			<div class="hero-badge">
				<i class="fas fa-bell"></i>
				<span>Innovation Hub Momentum</span>
			</div>
			<h1 class="hero-title">Announcements & Events</h1>
			<p class="hero-description">
				Stay informed about the latest opportunities, events, and updates from ALTA iHUB
			</p>
		</div>
	</div>
</section>

<div class="main-content">
    <div class="container">

        <!-- ================= LATEST ================= -->
        <section class="announcements-section">
            <div class="section-header">
                <i class="fas fa-star" style="color: var(--color-secondary);"></i>
                <h2 class="section-title">Featured Spotlight</h2>
            </div>

            <?php if (!empty($latest)): ?>
                <?php foreach ($latest as $row): ?>
                    <div class="announcement-side-wrapper">

                        <?php if (!empty($row['image'])): ?>
                            <div class="announcement-image-wrapper-side">
                                <img src="<?= htmlspecialchars($row['image']) ?>" 
                                     alt="<?= htmlspecialchars($row['title']) ?>" 
                                     class="announcement-image-side">
                            </div>
                        <?php endif; ?>

                        <div class="featured-announcement-card">
                            <div class="announcement-content">
                                <div class="announcement-icon-large">
                                    <i class="fas fa-calendar"></i>
                                </div>

                                <div class="announcement-info">
                                    <div class="badges-row">
                                        <?php if (!empty($row['badge'])): ?>
                                            <span class="badge badge-high"><?= htmlspecialchars($row['badge']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['badge_priority'])): ?>
                                            <span class="badge badge-priority"><?= htmlspecialchars($row['badge_priority']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="announcement-title-large"><?= htmlspecialchars($row['title']) ?></h3>

                                    <?php if (!empty($row['subtitle'])): ?>
                                        <p class="announcement-subtitle"><?= htmlspecialchars($row['subtitle']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($row['short_desc'])): ?>
                                        <p class="announcement-desc"><?= htmlspecialchars($row['short_desc']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="details-grid">
                                <?php if (!empty($row['event_date'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-calendar-alt"></i>
                                        <div>
                                            <p class="detail-label">Event Date</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['event_date']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['location'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <p class="detail-label">Location</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['location']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($row['highlights'])): ?>
                                <div class="highlights">
                                    <h4>Event Highlights:</h4>
                                    <ul>
                                        <?php foreach (explode("\n", $row['highlights']) as $highlight): ?>
                                            <?php if (trim($highlight) !== ''): ?>
                                                <li><?= htmlspecialchars(trim($highlight)) ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($row['reg_deadline'])): ?>
                                <div class="deadline-alert">
                                    <span class="label">Registration Deadline:</span>
                                    <span class="date"><?= date('F j, Y', strtotime($row['reg_deadline'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <button class="btn btn-primary btn-lg btn-block" 
                                    style="margin-top: 2rem; font-weight: 800; letter-spacing: 1px;" 
                                    onclick="openRegistrationModal()">
                                <?= htmlspecialchars($row['button_text'] ?? 'LEARN MORE & REGISTER NOW') ?>
                                <i class="fas fa-arrow-right"></i>
                            </button>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured announcements.</p>
            <?php endif; ?>
        </section>

        <!-- ================= ONGOING ================= -->
        <section class="announcements-section">
            <div class="section-header">
                <i class="fas fa-clock section-icon ongoing-icon" style="color: var(--color-secondary);"></i>
                <h2 class="section-title">Ongoing</h2>
            </div>

            <?php if (!empty($ongoing)): ?>
                <?php foreach ($ongoing as $row): ?>
                    <div class="announcement-side-wrapper">

                        <?php if (!empty($row['image'])): ?>
                            <div class="announcement-image-wrapper-side">
                                <img src="<?= htmlspecialchars($row['image']) ?>" 
                                     alt="<?= htmlspecialchars($row['title']) ?>" 
                                     class="announcement-image-side">
                            </div>
                        <?php endif; ?>

                        <div class="featured-announcement-card">
                            <div class="announcement-content">
                                <div class="announcement-icon-large">
                                    <i class="fas fa-calendar"></i>
                                </div>

                                <div class="announcement-info">
                                    <div class="badges-row">
                                        <?php if (!empty($row['badge'])): ?>
                                            <span class="badge badge-high"><?= htmlspecialchars($row['badge']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['badge_priority'])): ?>
                                            <span class="badge badge-priority"><?= htmlspecialchars($row['badge_priority']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="announcement-title-large"><?= htmlspecialchars($row['title']) ?></h3>

                                    <?php if (!empty($row['subtitle'])): ?>
                                        <p class="announcement-subtitle"><?= htmlspecialchars($row['subtitle']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($row['short_desc'])): ?>
                                        <p class="announcement-desc"><?= htmlspecialchars($row['short_desc']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="details-grid">
                                <?php if (!empty($row['event_date'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-calendar-alt"></i>
                                        <div>
                                            <p class="detail-label">Event Date</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['event_date']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['location'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <p class="detail-label">Location</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['location']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($row['highlights'])): ?>
                                <div class="highlights">
                                    <h4>Event Highlights:</h4>
                                    <ul>
                                        <?php foreach (explode("\n", $row['highlights']) as $highlight): ?>
                                            <?php if (trim($highlight) !== ''): ?>
                                                <li><?= htmlspecialchars(trim($highlight)) ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($row['reg_deadline'])): ?>
                                <div class="deadline-alert">
                                    <span class="label">Registration Deadline:</span>
                                    <span class="date"><?= date('F j, Y', strtotime($row['reg_deadline'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <button class="btn btn-primary btn-lg btn-block" 
                                    style="margin-top: 2rem; font-weight: 800; letter-spacing: 1px;" 
                                    onclick="openRegistrationModal()">
                                <?= htmlspecialchars($row['button_text'] ?? 'LEARN MORE & REGISTER NOW') ?>
                                <i class="fas fa-arrow-right"></i>
                            </button>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No ongoing announcements.</p>
            <?php endif; ?>
        </section>

        <!-- ================= UPCOMING ================= -->
        <section class="announcements-section">
            <div class="section-header">
                <i class="fas fa-calendar-plus section-icon upcoming-icon" style="color: var(--color-secondary);"></i>
                <h2 class="section-title">Upcoming</h2>
            </div>

            <?php if (!empty($upcoming)): ?>
                <?php foreach ($upcoming as $row): ?>
                    <div class="announcement-side-wrapper">

                        <?php if (!empty($row['image'])): ?>
                            <div class="announcement-image-wrapper-side">
                                <img src="<?= htmlspecialchars($row['image']) ?>" 
                                     alt="<?= htmlspecialchars($row['title']) ?>" 
                                     class="announcement-image-side">
                            </div>
                        <?php endif; ?>

                        <div class="featured-announcement-card">
                            <div class="announcement-content">
                                <div class="announcement-icon-large">
                                    <i class="fas fa-calendar"></i>
                                </div>

                                <div class="announcement-info">
                                    <div class="badges-row">
                                        <?php if (!empty($row['badge'])): ?>
                                            <span class="badge badge-high"><?= htmlspecialchars($row['badge']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['badge_priority'])): ?>
                                            <span class="badge badge-priority"><?= htmlspecialchars($row['badge_priority']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="announcement-title-large"><?= htmlspecialchars($row['title']) ?></h3>

                                    <?php if (!empty($row['subtitle'])): ?>
                                        <p class="announcement-subtitle"><?= htmlspecialchars($row['subtitle']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($row['short_desc'])): ?>
                                        <p class="announcement-desc"><?= htmlspecialchars($row['short_desc']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="details-grid">
                                <?php if (!empty($row['event_date'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-calendar-alt"></i>
                                        <div>
                                            <p class="detail-label">Event Date</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['event_date']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['location'])): ?>
                                    <div class="detail-card">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <p class="detail-label">Location</p>
                                            <p class="detail-value"><?= htmlspecialchars($row['location']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($row['highlights'])): ?>
                                <div class="highlights">
                                    <h4>Event Highlights:</h4>
                                    <ul>
                                        <?php foreach (explode("\n", $row['highlights']) as $highlight): ?>
                                            <?php if (trim($highlight) !== ''): ?>
                                                <li><?= htmlspecialchars(trim($highlight)) ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($row['reg_deadline'])): ?>
                                <div class="deadline-alert">
                                    <span class="label">Registration Deadline:</span>
                                    <span class="date"><?= date('F j, Y', strtotime($row['reg_deadline'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <button class="btn btn-primary btn-lg btn-block" 
                                    style="margin-top: 2rem; font-weight: 800; letter-spacing: 1px;" 
                                    onclick="openRegistrationModal()">
                                <?= htmlspecialchars($row['button_text'] ?? 'LEARN MORE & REGISTER NOW') ?>
                                <i class="fas fa-arrow-right"></i>
                            </button>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming announcements.</p>
            <?php endif; ?>
        </section>

    </div>
</div>

<!-- Registration Modal -->
<div id="registrationModal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<h2>Innovation Week 2026 Registration</h2>
			<button class="modal-close" onclick="closeRegistrationModal()">
				<i class="fas fa-times"></i>
			</button>
		</div>
		<div class="modal-body">
			<div class="form-header">
				<h3>INNOVATIONS WEEK 2026 – Digital Research Output Registration</h3>
				<p>Welcome to INNOVATIONS WEEK 2026: "From Ideas to Impact", happening on April 6-8, 2026 at the University of Perpetual Help System DALTA (UPHSD), Las Piñas Campus.</p>
				<p>In celebration of UPHSD's 50th Anniversary, this three-day event will showcase research outputs with innovation impact and commercialization potential, with special emphasis on Digital Transformation and ICT.</p>
				<p class="deadline"><strong>Registration Deadline: February 20, 2026</strong></p>
				<p class="contact">For inquiries, contact the ALTA Innovation Hub Secretariat at <a href="mailto:alta.ihub@perpetualdalta.edu.ph">alta.ihub@perpetualdalta.edu.ph</a></p>
			</div>
			
			<form class="registration-form">
				<!-- Section A: Registration Type -->
				<div class="form-section">
					<h4>SECTION A: REGISTRATION TYPE</h4>
					<label class="form-label">Please select your participation category: *</label>
					<div class="radio-group">
						<label class="radio-option">
							<input type="radio" name="category" value="pitching" required>
							<span>Category 1: Digital Pitching Competition Participant</span>
						</label>
						<label class="radio-option">
							<input type="radio" name="category" value="exhibit">
							<span>Category 2: Digital Innovation Exhibit Exhibitor</span>
						</label>
						<label class="radio-option">
							<input type="radio" name="category" value="both">
							<span>Category 3: Both Exhibit and Pitching</span>
						</label>
						<label class="radio-option">
							<input type="radio" name="category" value="adviser">
							<span>Category 4: Research Adviser / Faculty Endorser</span>
						</label>
						<label class="radio-option">
							<input type="radio" name="category" value="guest">
							<span>Category 5: Guest / Speaker / Judge / Panelist</span>
						</label>
						<label class="radio-option">
							<input type="radio" name="category" value="partner">
							<span>Category 6: Tech Industry Partner / Investor</span>
						</label>
					</div>
				</div>
				
				<!-- Section B: Research Team Information -->
				<div class="form-section">
					<h4>SECTION B: RESEARCH TEAM INFORMATION</h4>
					<div class="form-grid">
						<div class="form-field">
							<label>Lead Researcher / Team Leader: *</label>
							<input type="text" required>
						</div>
						<div class="form-field">
							<label>Name of Team / Group:</label>
							<input type="text">
						</div>
						<div class="form-field">
							<label>University / School: *</label>
							<select required>
								<option value="">Select campus</option>
								<option value="laspinas">UPHSD Las Piñas</option>
								<option value="calamba">UPHSD Calamba</option>
								<option value="molino">UPHSD Molino</option>
								<option value="other">Other School</option>
							</select>
						</div>
						<div class="form-field">
							<label>College / Department: *</label>
							<input type="text" required>
						</div>
						<div class="form-field">
							<label>Contact Number: *</label>
							<input type="tel" placeholder="+63 912 345 6789" required>
						</div>
						<div class="form-field">
							<label>Email Address: *</label>
							<input type="email" required>
						</div>
					</div>
				</div>
				
				<!-- Section C: Research Output Details -->
				<div class="form-section">
					<h4>SECTION C: RESEARCH OUTPUT DETAILS</h4>
					<div class="form-field">
						<label>Research Title: *</label>
						<input type="text" required>
					</div>
					<div class="form-field">
						<label>Digital Innovation Title (if different):</label>
						<input type="text">
					</div>
					<div class="form-field">
						<label>Brief Description of Research (100 words max): *</label>
						<textarea rows="3" required></textarea>
					</div>
					<div class="form-field">
						<label>Brief Description of Digital Innovation (100 words max): *</label>
						<textarea rows="3" required></textarea>
					</div>
				</div>
				
				<!-- Section D: Digital Category -->
				<div class="form-section">
					<h4>SECTION D: DIGITAL TRANSFORMATION & ICT CATEGORY</h4>
					<div class="form-field">
						<label>Primary Digital Category: *</label>
						<select required>
							<option value="">Select category</option>
							<option value="healthtech">HealthTech / Digital Health</option>
							<option value="edutech">EduTech / Digital Learning</option>
							<option value="fintech">FinTech / Digital Finance</option>
							<option value="agritech">AgriTech / Digital Agriculture</option>
							<option value="smart">Smart Communities</option>
							<option value="environmental">Environmental Tech</option>
							<option value="ai">AI & Data Science</option>
							<option value="iot">IoT (Internet of Things)</option>
						</select>
					</div>
				</div>
				
				<!-- Section H: Development Status -->
				<div class="form-section">
					<h4>SECTION H: DEVELOPMENT STATUS & READINESS</h4>
					<div class="form-field">
						<label>Current Development Status: *</label>
						<select required>
							<option value="">Select status</option>
							<option value="research">Research Stage Only</option>
							<option value="concept">Concept / Wireframe Stage</option>
							<option value="prototype">Basic Prototype</option>
							<option value="beta">Beta Version</option>
							<option value="v1">Version 1.0 Released</option>
						</select>
					</div>
					<div class="form-field">
						<label>Demo Video Link (required): *</label>
						<input type="url" placeholder="https://youtube.com/..." required>
					</div>
				</div>
				
				<!-- Section L: Event Participation -->
				<div class="form-section">
					<h4>SECTION L: EVENT PARTICIPATION</h4>
					<label class="form-label">Which days will you attend? *</label>
					<div class="checkbox-group">
						<label class="checkbox-option">
							<input type="checkbox">
							<span>Day 1 – April 6, 2026 (Innovation & Capacity Building)</span>
						</label>
						<label class="checkbox-option">
							<input type="checkbox">
							<span>Day 2 – April 7, 2026 (Digital Pitching Day)</span>
						</label>
						<label class="checkbox-option">
							<input type="checkbox">
							<span>Day 3 – April 8, 2026 (Entrepreneurship & Recognition)</span>
						</label>
					</div>
				</div>
				
				<!-- Section N: Declaration -->
				<div class="form-section">
					<h4>SECTION N: DECLARATION & DATA PRIVACY</h4>
					<label class="checkbox-option consent">
						<input type="checkbox" required>
						<span>I/we certify that the information provided in this registration form is true, complete, and accurate to the best of my/our knowledge. *</span>
					</label>
					<label class="checkbox-option consent">
						<input type="checkbox" required>
						<span>I hereby allow UPHSD and ALTA-iHub to collect, process, and store my personal information for the purpose of registration and communication. *</span>
					</label>
				</div>
				
				<button type="submit" class="btn btn-primary btn-lg btn-block">
					REGISTER MY DIGITAL RESEARCH OUTPUT
					<i class="fas fa-paper-plane"></i>
				</button>
				
				<p class="form-footer-note">
					*Thank you for registering! You will receive a confirmation email within 3-5 working days.
				</p>
			</form>
		</div>
	</div>
</div>

<script>
	function openRegistrationModal() {
		document.getElementById('registrationModal').classList.add('active');
		document.body.style.overflow = 'hidden';
	}
	function closeRegistrationModal() {
		document.getElementById('registrationModal').classList.remove('active');
		document.body.style.overflow = 'auto';
	}
</script>

</body>
</html>