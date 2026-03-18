<?php
include '../db.php';

$category = htmlspecialchars_decode($_GET['edit_category'] ?? '');

$services_list = [
    'Technology & Engineering Services' => [
        'AI, Data Analytics, Software & App Dev, Health Tech & Smart Systems', 
        'IoT and Smart Manufacturing',
        'Renewable Energy and Smart Manufacturing'
    ],
    'Startup & Business Support' => [
        'Business Model Development',
        'Startup Mentorship & Coaching',
        'Pitch Deck & Investor Readiness Training',
        'Legal, IP, and Regulatory Advisory',
        'Company Registration & Compliance Guidance'
    ],
    'Innovation & Incubation' => [
        'Startup Incubation & Acceleration Programs',
        'Idea Validation & Product Development Support',
        'Technology Commercialization Assistance',
        'Research-to-Market (R2M) Mentorship',
        'Proof of Concept (PoC) & Prototype Development'
    ],
    'Training & Capacity Building' => [
        'Technical Skills Training & Bootcamps',
        'Industry Certification Programs',
        'Student Internship & Apprenticeship Programs',
        'Faculty & Researcher Upskilling',
        'Innovation & Entrepreneurship Workshops'
    ],
    'Industry & Academic Collaboration' => [
        'Industry-Academe Partnership Programs',
        'Joint Research & Development Projects',
        'Corporate Innovation Challenges & Hackathons',
        'Technology Transfer & Licensing Support'
    ],
    'Funding & Investment Support' => [
        'Startup Grant & Seed Funding Assistance',
        'Government Funding (DOST, CHED, Startup PH, etc.) Advisory',
        'Investor & Venture Capital Matching',
        'Pitching Events & Demo Days'
    ],
    'Facilities & Innovation Spaces' => [
        'Co-working & Collaboration Spaces',
        'Innovation Labs (IOT Lab, PERPSAT, Project DAGAT)',
        'Testing, Development & Demonstration Areas',
        'Virtual Incubation & Remote Mentorship',
        'KIST Park at Molino – Dedicated Innovation, Research, and Startup Zone'
    ],
    'KIST Park at Molino' => [
        'Startup & Research Offices',
        'Technology Demonstration & Pilot Deployment Area',
        'Industry Partner Collaboration Hub',
        'Training, Seminar & Event Facilities',
        'Smart Campus & Living Laboratory Environment',
        'Resource Feeder Campus'
    ],
];

$sub_services = $services_list[$category] ?? [];
if (empty($sub_services)) {
    echo "<div class='alert-error'>Invalid category or no sub-services defined.</div>";
    return;
}

$placeholders = implode(',', array_fill(0, count($sub_services), '?'));
$stmt = $pdo->prepare("SELECT * FROM service_slots WHERE service_name IN ($placeholders) ORDER BY service_name ASC");
$stmt->execute($sub_services);
$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

$locations = array_unique(array_column($slots, 'location'));

echo "<h3 style='margin-bottom:15px;'>Manage: " . htmlspecialchars($category) . "</h3>";
?>

<!-- CAMPUS LOCATION SELECT -->
<div style="margin-bottom: 15px;">
    <label style="font-weight:bold; font-size:0.85rem;">Campus Location *</label>
    <select id="locationSelect" class="form-select" style="width:100%; margin-top:5px;">
        <option value="">— Select a location —</option>
        <?php foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- select sub service-->
<div style="margin-bottom: 20px;">
    <label style="font-weight:bold; font-size:0.85rem;">Select Service *</label>
    <select id="subServiceSelect" class="form-select" style="width:100%; margin-top:5px;">
        <option value="">— Choose a specific service —</option>
        <?php foreach ($slots as $slot): ?>
            <!-- hidden by default via style; filtered via JS -->
            <option value="slot-<?= $slot['id'] ?>" 
                    data-location="<?= htmlspecialchars($slot['location']) ?>" 
                    style="display:none;">
                <?= htmlspecialchars($slot['service_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<?php
foreach ($slots as $slot):
?>
    <div class="sub-service-form" id="slot-<?= $slot['id'] ?>" style="display:none; padding:15px; background:#fff; border:1px solid #ddd; border-radius:10px; margin-bottom:20px;">
        <form action="update_slot.php" method="POST">
            <input type="hidden" name="id" value="<?= $slot['id'] ?>">
            <p style="font-weight:bold; color:#001F3F; margin-bottom:5px;"> <?= htmlspecialchars($slot['service_name']) ?> </p>
            <p style="font-size:0.8rem; color:#666;"> Campus: <?= htmlspecialchars($slot['location']) ?> </p>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:bold;">Slots</label>
                    <input type="number" name="available" class="form-input" value="<?= $slot['available_slots'] ?>" style="width:100%;">
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:bold;">Date</label>
                    <input type="date" name="date" class="form-input" value="<?= $slot['available_date'] ?>" style="width:100%;">
                </div>
            </div>
            
            <div style="margin-top:10px;">
                <label style="font-size:0.75rem; font-weight:bold;">Status</label>
                <select name="is_available" class="form-select" style="width:100%;">
                    <option value="1" <?= $slot['is_available'] ? 'selected' : '' ?>>🟢 Available</option>
                    <option value="0" <?= !$slot['is_available'] ? 'selected' : '' ?>>🔴 Not Available</option>
                </select>
            </div>

            <div style="margin-top:10px;">
                <label style="font-size:0.75rem; font-weight:bold;">Description</label>
                <textarea name="description" class="form-input" style="width:100%; height:60px;"><?= htmlspecialchars($slot['service_description']) ?></textarea>
            </div>

            <button type="submit" class="btn-blue" style="width:100%; margin-top:15px; padding:10px;"> Save Changes </button>
        </form>
    </div>
<?php endforeach; ?>

<!-- JAVASCRIPTTTT-->
<script>
(function() {
    const locSelect = document.getElementById('locationSelect');
    const servSelect = document.getElementById('subServiceSelect');
    const servOptions = Array.from(servSelect.options);

    locSelect.addEventListener('change', function() {
        const selectedLoc = this.value;
        servSelect.value = ""; 
        document.querySelectorAll('.sub-service-form').forEach(f => f.style.display = 'none');

        servOptions.forEach(opt => {
            if (opt.value === "") {
                opt.style.display = "block"; 
            } else if (opt.getAttribute('data-location') === selectedLoc) {
                opt.style.display = "block"; 
            } else {
                opt.style.display = "none"; 
            }
        });
    });

    servSelect.addEventListener('change', function() {
        document.querySelectorAll('.sub-service-form').forEach(f => f.style.display = 'none');
        if (this.value) {
            const activeForm = document.getElementById(this.value);
            if (activeForm) activeForm.style.display = 'block';
        }
    });
})();
</script>