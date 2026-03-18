<?php
session_start();
include 'db.php';
/* ================= SERVICES DATA ================= */

$services = [
    [
        'name' => 'Innovation & Incubation',
        'description' => 'Innovation & Incubation is a supportive ecosystem designed to transform raw ideas into viable businesses, providing the protective environment, expert coaching, and resources needed to take a rough concept and polish it into a business that\'s ready to stand on its own.',
        'features' => ['Startup Incubation & Acceleration: Providing a roadmap and the momentum to scale fast.', 'Idea Validation & Product Development: Stress-test your theories to make sure you\'re building something people actually need.', 'Technology Commercialization: Turning "cool tech" into a "real product" by navigating patents and market entry.', 'Research to Market (R2M) Mentorship: Helping researchers step out of the lab and into the boardroom with confidence.', 'PoC & Prototype Development: Get the technical support required to build your first working model and prove your tech works.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 012-2h8a2 2 0 012 2v18M6 22h12M6 22v-4a2 2 0 012-2h8a2 2 0 012 2v4M10 6h4M10 10h4M10 14h4"></path></svg>',
        'image_url' => 'assets/serv-img/innov.jpg',
        'booklabel' => ['Innovation Consultation'],
        'amenities' => ['High Speed Fiber Internet', 'Unlimited Coffee  & Refreshments', 'On-Demand Session Recordings', 'Program Certification', 'Knowledge Manuals'],
        'slots_locations' => ['25 Startups per Cohort', 'Las Piñas, Molino, and Calamba'],
    ],

    [
        'name' => 'Startup & Business Support',
        'description' => 'This pillar provides the strategic backbone for emerging ventures, offering the mentorship and structural guidance necessary to navigate the complexities of the corporate world. We bridge the gap between "having a business" and "running a sustainable enterprise."',
        'features' => ['Business Model Development: Architecting a sustainable revenue and growth strategy.', 'Startup Mentorship & Coaching: Direct access to industry veterans who have walked the path before.', 'Pitch Deck & Investor Readiness Training: Refining your story and your data to secure vital capital.', 'Legal, IP, and Regulatory Advisory: Protecting your intellectual property and ensuring your venture is legally sound','Company Registration & Compliance Guidance: Navigating the bureaucracy of business permits and government requirements.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path></svg>',
        'image_url' => 'assets/serv-img/startup.jpg',
        'booklabel' => ['Startup Advisory Session'],
        'amenities' => ['On-Demand Session Recordings', 'Knowledge Manuals','Program Certification'],
        'slots_locations' => ['Flexible', 'All Campuses'],
    ],
    [
        'name' => 'Training & Capacity Building',
        'description' => 'We are committed to closing the skills gap. This service focuses on human capital—empowering students, faculty, and professionals with the technical and entrepreneurial "muscle" needed to lead in a digital economy.',
        'features' => ['Technical Skills Training & Bootcamps: Intensive, hands-on learning for high-demand technologies.', 'Industry Certification Programs: Validating expertise through recognized professional standards.', 'Student & Faculty Upskilling: Transforming the academic community into a powerhouse of innovation.', 'Innovation & Entrepreneurship Workshops: Cultivating the "Founder Mindset" across all disciplines.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M10 10h.01"></path><path d="M14 10h.01"></path><path d="M10 14h.01"></path><path d="M14 14h.01"></path><path d="M10 18h.01"></path><path d="M14 18h.01"></path></svg>',
        'image_url' => 'assets/serv-img/training.jpg',
        'booklabel' => ['View Available Schedule'],
        'amenities' => ['Program Certification', 'Knowledge Manuals', 'Unlimited Coffee'],
        'slots_locations' => ['N/A', 'Various rooms'],
    ], 
    [
        'name' => 'Industry & Academic Collaboration',
        'description' => 'Acting as a bridge between the classroom and the boardroom, we facilitate partnerships that turn academic research into industrial solutions through shared resources and collective intelligence. Industry-Academe Partnership Programs: Aligning curriculum and research with real-world industry needs',
        'features' => ['Joint Research & Development (R&D): Solving high-level problems through collaborative experimentation.', 'Corporate Innovation Challenges: Helping established companies find fresh solutions through startup thinking.', 'Technology Transfer: Moving intellectual property from the university into the commercial market.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><path d="M17 2v5"></path><path d="M7 2v5"></path><path d="M2 11h20"></path><path d="M12 16v4"></path></svg>',
        'image_url' => 'assets/serv-img/acad.jpg',
        'booklabel' => ['Request Partnership Meeting'],
        'amenities' => ['On-Demand Session Recordings', 'Knowledge Manuals', 'Industry Networking Events.'], 
        'slots_locations' => ['N/A', 'Auditorium/Rooms'], 
    ], 
    [
        'name' => 'Funding & Investment Support',
        'description' => 'We demystify the financial landscape by connecting startups with the capital they need to survive the "valley of death" and scale their impact.',
        'features' => ['Grant & Seed Funding Assistance: Helping startups navigate the initial stages of capital injection.', 'Government Advisory (DOST, CHED, etc.): Streamlining the application process for public innovation grants.', 'Investor & VC Matching: Facilitating warm introductions to private capital and angel investors.', 'Pitching Events & Demo Days: Providing the stage to showcase your progress to the world.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="18" rx="3"></rect><path d="M12 2v20"></path><path d="M8 8H6"></path><path d="M8 12H6"></path><path d="M8 16H6"></path></svg>',
        'image_url' => 'assets/serv-img/funding.jpg',
        'booklabel' => ['Funding Consultation'],
        'amenities' => ['Program Certification', 'Investor Databases', 'Pitch Practice Sessions'], 
        'slots_locations' => ['Per agreement', 'All Campuses'],
    ],
]; 

$spaces = [
    [
        'name' => 'Technology & Engineering Services',
        'description' => 'Professional business address and support without needing a physical office space.',
        'features' => ['Leveraging the specialized technical strengths of the UPHSD campuses, we provide the high-level engineering expertise required to build complex, future-ready systems.', 'AI, Data Analytics, & Health Tech (Las Piñas): Specialized support for software solutions and smart healthcare systems.', 'IoT and Smart Manufacturing (Molino): Engineering the hardware and connectivity for the next industrial revolution.', 'Renewable Energy & Smart Systems (Calamba): Developing sustainable power solutions and green technologies.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="18" x2="12" y2="12"></line><line x1="12" y1="12" x2="16" y2="10"></line><line x1="12" y1="12" x2="8" y2="10"></line></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'locations' => ['Las Piñas', 'Molino', 'Calamba'],
        'amenities' => ['Access to specialized labs', 'High-Speed Fiber Internet', 'Technical Troubleshooting'],
    ],

    [
        'name' => 'Facilities & Innovation Spaces',
        'description' => 'Innovation needs a home. We provide the physical infrastructure—from quiet desks to high-tech command centers—where founders can build, test, and collaborate.',
        'features' => ['Co-working & Collaboration Spaces: Dynamic environments for networking and daily operations.', 'Innovation Labs (IoT, PERPSAT, Project DAGAT): Access to specialized hardware and testing grounds.', 'Virtual Incubation: Providing remote mentorship and digital resources for founders on the move.', 'KIST Park at Molino: Our flagship zone for dedicated research and startup offices.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="18" rx="3"></rect><path d="M12 2v20"></path><path d="M8 8H6"></path><path d="M8 12H6"></path><path d="M8 16H6"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'locations' => ['IOT Lab', 'PERPSAT Ground Station and Command Center','Project DAGAT Center'], 
        'amenities' => ['High-Speed Fiber Internet', 'Unlimited Coffee', 'Access to Specialized Lab Equipment'], 
    ],
    [
        'name' => 'KIST Park at Molino (The Flagship)',
        'description' => 'As our premier innovation site, KIST Park represents the pinnacle of the ALTA iHUB ecosystem—a "Living Laboratory" where technology is deployed in real-time.',
        'features' => ['Technology Demonstration & Pilot Areas: A playground for deploying prototypes in a controlled environment.', 'Industry Partner Collaboration Hub: A dedicated space for multinational and local corporate partners.', 'Smart Campus Environment: Integrating innovation into the very fabric of the campus infrastructure.'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="18" rx="3"></rect><path d="M12 2v20"></path><path d="M8 8H6"></path><path d="M8 12H6"></path><path d="M8 16H6"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'locations' => ['Molino'],
        'amenities' => ['Premium Office Space', 'High-Speed Fiber', 'Full Event Facilities', '24/7 Security.'], 
    ]
]; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/services.css">
</head>
<body class="services-body">

    <?php include 'header.php'; ?>

<!-- ================= SERVICES HERO SLIDER ================= -->
<section class="services-hero-slider">
    <div class="slider-wrapper">
        <div class="slider-items">
            <div class="slide"><img src="assets/serv-img/serv1.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv2.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv3.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv4.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv5.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv6.jpg" alt=""></div>
            <div class="slide"><img src="assets/serv-img/serv7.jpg" alt=""></div>
        </div>
        <button class="prev-slide">&lt;</button>
        <button class="next-slide">&gt;</button>
    </div>
    <ul class="slider-dots">
        <li class="active"></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</section>

<section class="services-container">

    <h2 class="services-section-title">Our Offerings</h2>

    <div class="services-tabs">
        <button class="services-tab active" onclick="switchTab('services')">
            Our Services
        </button>
        <button class="services-tab" onclick="switchTab('spaces')">
            Our Spaces
        </button>
    </div>
    <!-- ================= SERVICES TAB ================= -->
    <div id="servicesTab" class="tab-content active">
        <div class="services-interactive">
            <div class="services-grid" id="servicesGrid">
                <?php foreach ($services as $i => $service): ?>
                    <div class="services-card" data-index="<?= $i ?>">
                        <div class="services-card-image">
                            <img src="<?= $service['image_url'] ?>" alt="">
                        </div>
                        <div class="services-card-content">
                            <div class="services-card-icon"><?= $service['icon_html'] ?></div>
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <p><?= htmlspecialchars($service['description']) ?></p>
                            <a href="#detailPanel" class="services-learn-more-btn" onclick="openService(<?= $i ?>,'services'); return false;">
                                Learn More
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="servicesdetailPanel" class="services-detail-panel"></div>
        </div>
    </div>

    <div id="spacesTab" class="tab-content">
        <div class="spaces-list">
            <?php foreach ($spaces as $i => $space): ?>
                <div class="space-item" data-index="<?= $i ?>">
                    <div class="space-left">
                        <div class="space-card-icon"><?= $space['icon_html'] ?></div>
                        <div class="space-info">
                            <h3><?= htmlspecialchars($space['name']) ?></h3>
                            <p style="color: var(--services-white);"><?= htmlspecialchars($space['description']) ?></p>
                            <a href="#" class="space-learn-more-btn" onclick="openService(<?= $i ?>,'spaces'); return false;">Learn More</a>
                        </div>
                    </div>

                    <div class="space-locations">
                        <?php foreach ($space['locations'] as $location): ?>
                            <div class="location-card">
                                <p class="location-name"><?= htmlspecialchars($location) ?></p>
                                <a href="booking.php?service=<?= urlencode($space['name']) ?>&location=<?= urlencode($location) ?>" 
                                 class="location-book-btn">Book</a>
                             </div>
                         <?php endforeach; ?>
                     </div>
                 </div>
             <?php endforeach; ?>
         </div>

         <!-- ✅ Only one detail panel here -->
         <div id="spacesdetailPanel" class="spaces-detail-panel"></div>
     </div>
 </section>




 <?php include 'footer.php'; ?>

 <script>
    const servicesData = <?= json_encode($services) ?>;
    const spacesData = <?= json_encode($spaces) ?>;

            /* ================= SERVICE OPEN ================= */
    /* ================= SERVICE OPEN ================= */
function openService(index, type) {
    const data = type === 'services' ? servicesData : spacesData;
    const tabId = type === 'services' ? 'servicesTab' : 'spacesTab';
    const panelClass = type === 'services' ? '.services-detail-panel' : '.spaces-detail-panel';
    
    // 1. Find all cards in this tab
    const cards = document.querySelectorAll(`#${tabId} .services-card, #${tabId} .space-item`);
    let targetCard = null;

    // 2. FIXED LOGIC: Add 'hidden' to others and 'expanded' to target
    cards.forEach(card => {
        if (+card.dataset.index === index) {
            card.classList.remove('hidden');
            card.classList.add('expanded');
            targetCard = card;
        } else {
            card.classList.add('hidden'); 
            card.classList.remove('expanded');
        }
    });

    const s = data[index];
    const panel = document.querySelector(panelClass);

    // 3. PHYSICALLY MOVE the panel to be directly after the clicked card
    if (targetCard && panel) {
        targetCard.after(panel);
        panel.style.display = 'block'; 
    }

    panel.innerHTML = `
    <button class="services-close-btn" onclick="closeService('${type}')">✕</button>
    <div class="services-detail-content">
        <h3 style="color: var(--services-gold); margin-bottom: 1.5rem;">${s.name}</h3>

        <h4 style="font-weight: bold; margin-top: 1rem;">What we offer</h4>
        <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 1.5rem;">
        ${s.features.map(f => `<li>${f}</li>`).join('')}
        </ul>

        <h4 style="font-weight: bold; margin-top: 1rem;">Amenities</h4>
        <div class="services-amenities" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
        ${s.amenities.map(a => `<span class="services-amenity-tag">${a}</span>`).join('')}
        </div>

        <a href="booking.php?service=${encodeURIComponent(s.name)}"
           class="location-book-btn" style="display: inline-block; width: auto; padding: 0.8rem 2rem;">
            Book Now
        </a>
    </div>
    `;

    panel.classList.add('active');
}




function closeService(type) {
    const tabId = type === 'services' ? 'servicesTab' : 'spacesTab';
    const panelClass = type === 'services' ? '.services-detail-panel' : '.spaces-detail-panel';

            // Reset cards
    document.querySelectorAll(`#${tabId} .services-card, #${tabId} .space-item`)
    .forEach(c => c.classList.remove('hidden','expanded'));

            // Reset the correct detail panel
    const panel = document.querySelector(panelClass);
    panel.classList.remove('active');
    panel.innerHTML = '';
}


            /* ===================== SERVICES HERO SLIDER JS ===================== */
function initHeroSlider() {
    const slider = document.querySelector('.services-hero-slider');
    if (!slider) return;

    const sliderItems = slider.querySelector('.slider-items');
    const slides = slider.querySelectorAll('.slide');
    const prevBtn = slider.querySelector('.prev-slide');
    const nextBtn = slider.querySelector('.next-slide');
    const dots = slider.querySelectorAll('.slider-dots li');

    let currentIndex = 0;

    function updateSlider() {
        sliderItems.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((dot,i) => dot.classList.toggle('active', i === currentIndex));
    }

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateSlider();
    });

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateSlider();
    });

    dots.forEach((dot,i) => {
        dot.addEventListener('click', () => {
            currentIndex = i;
            updateSlider();
        });
    });

            // Auto-play every 5s
    setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateSlider();
    }, 3000);

    updateSlider();
}

            // Initialize slider
initHeroSlider();

function switchTab(tabName) {
    const servicesTab = document.getElementById('servicesTab');
    const spacesTab = document.getElementById('spacesTab');
    const tabs = document.querySelectorAll('.services-tab');

    tabs.forEach(tab => tab.classList.remove('active'));

    if (tabName === 'services') {
        servicesTab.style.display = 'block';
        spacesTab.style.display = 'none';
        tabs[0].classList.add('active');

        // Clear spaces detail panel when switching
        const spacesPanel = document.querySelector('.spaces-detail-panel');
        spacesPanel.classList.remove('active');
        spacesPanel.innerHTML = '';
    } else {
        servicesTab.style.display = 'none';
        spacesTab.style.display = 'block';
        tabs[1].classList.add('active');

        // Clear services detail panel when switching
        const servicesPanel = document.querySelector('.services-detail-panel');
        servicesPanel.classList.remove('active');
        servicesPanel.innerHTML = '';
    }
}

</script>

</body>
</html>
