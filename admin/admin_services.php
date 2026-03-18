<?php
session_start();
include '../db.php'; 
include 'admin_header.php'; 

/*************** MAIN SERVICES DATA  ***************/
$services = [
    [
        'name' => 'Innovation & Incubation',
        'price' => '',
        'description' => 'Innovation & Incubation is a supportive ecosystem designed to transform raw ideas into viable businesses, providing the protective environment, expert coaching, and resources needed to take a rough concept and polish it into a business that\'s ready to stand on its own.',
        'features' => ['Startup Incubation & Acceleration', 'Idea Validation & Product Development', 'Technology Commercialization', 'Research to Market (R2M) Mentorship', 'PoC & Prototype Development'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 012-2h8a2 2 0 012 2v18M6 22h12M6 22v-4a2 2 0 012-2h8a2 2 0 012 2v4M10 6h4M10 10h4M10 14h4"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg1.jpg', 'assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg'],
        'amenities' => ['High Speed Fiber Internet', 'Unlimited Coffee', 'Program Certification'],
        'slots_locations' => ['25 Startups per Cohort', 'Las Piñas, Molino, and Calamba'],
    ],
    [
        'name' => 'Startup & Business Support',
        'price' => '₱299',
        'description' => 'A collaborative environment designed to support new businesses with essential resources and networking opportunities.',
        'features' => ['Hot desk', 'Community events', 'Networking opportunities', 'Free coffee'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'],
        'amenities' => ['High Speed Fiber Internet', 'Knowledge Manuals', 'Program Certification'],
        'slots_locations' => ['Flexible', 'All Campuses'],
    ],
    [
        'name' => 'Technology & Engineering Services',
        'price' => '₱0.00',
        'description' => 'Professional business address and support without needing a physical office space.',
        'features' => ['Business address', 'Mail handling', 'Phone answering', 'Meeting room credits'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="18" x2="12" y2="12"></line><line x1="12" y1="12" x2="16" y2="10"></line><line x1="12" y1="12" x2="8" y2="10"></line></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'],
        'amenities' => ['Professional Address', 'Mail Handling'],
        'slots_locations' => ['N/A', 'Online Services'],
    ],
    [
        'name' => 'Training & Capacity Building',
        'price' => '₱0.00',
        'description' => 'Professional meeting spaces for all your needs, equipped with modern technology.',
        'features' => ['Video conferencing', 'Whiteboard', 'Presentation equipment', 'Refreshments'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M10 10h.01"></path><path d="M14 10h.01"></path><path d="M10 14h.01"></path><path d="M14 14h.01"></path><path d="M10 18h.01"></path><path d="M14 18h.01"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'],
        'amenities' => ['High Speed Internet', 'AV Equipment'],
        'slots_locations' => ['N/A', 'Various rooms'],
    ], 
    [
        'name' => 'Industry & Academic Collaboration',
        'price' => '₱0.00',
        'description' => 'Versatile spaces for workshops, seminars, and corporate events with full support.',
        'features' => ['Flexible layout', 'AV equipment', 'Catering options', 'Event support'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><path d="M17 2v5"></path><path d="M7 2v5"></path><path d="M2 11h20"></path><path d="M12 16v4"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'], 
        'amenities' => ['AV Equipment', 'Catering Options'], 
        'slots_locations' => ['N/A', 'Auditorium/Rooms'], 
    ], 
    [
        'name' => 'Funding & Investment Support',
        'price' => '₱0.00',
        'description' => 'Dedicated private offices with full amenities for focused work environments.',
        'features' => ['Lockable space', 'Customizable', 'Storage included', '24/7 access'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="18" rx="3"></rect><path d="M12 2v20"></path><path d="M8 8H6"></path><path d="M8 12H6"></path><path d="M8 16H6"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'], 
        'amenities' => ['24/7 Access', 'Lockable Doors', 'Storage'], 
        'slots_locations' => ['Per agreement', 'All Campuses'],
    ],
    [
        'name' => 'Facilities & Innovation Spaces',
        'price' => '₱0.00',
        'description' => 'Modern co-working spaces and specialized labs equipped for research, testing, and collaboration across multiple campuses.',
        'features' => ['Co-working Spaces', 'Innovation Labs', 'Testing Areas', 'Virtual Incubation'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'],
        'amenities' => ['Lab Equipment', 'Meeting Rooms', 'High Speed WiFi'],
        'slots_locations' => ['Various', 'All Campuses'],
    ],
    [
        'name' => 'KIST Park at Molino',
        'price' => '₱0.00',
        'description' => 'A dedicated Special Economic Zone for Innovation and Research, providing a premier environment for startups and industry partners.',
        'features' => ['Research Offices', 'Pilot Deployment Area', 'Collaboration Hub', 'Smart Campus'],
        'icon_html' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path></svg>',
        'image_url' => 'assets/dashboardimg/coworking.jpg',
        'photo_urls' => ['assets/aboutimg/abtimg2.jpg', 'assets/aboutimg/abtimg3.jpg', 'assets/aboutimg/abtimg1.jpg'],
        'amenities' => ['Economic Zone Incentives', 'Industry Networking', 'State-of-the-Art Facilities'],
        'slots_locations' => ['Dedicated Zone', 'Molino Campus'],
    ]
];
?>

<section id="services" class="container">
    <h2 class="section-title">Admin Dashboard: Edit Services</h2>
    <div class="services-interactive">
        <div class="services-grid">
         <?php
$editing = $_GET['edit_category'] ?? null;
foreach ($services as $cat):
    if ($editing && $cat['name'] !== $editing) continue;
?>

                <div class="service-card">
                    <div class="card-image">
                        <img src="../<?= htmlspecialchars($cat['image_url']) ?>" alt="">
                    </div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                        <p><?= htmlspecialchars($cat['description']) ?></p>
                        
                        <a href="?edit_category=<?= urlencode($cat['name']) ?>#detailPanel" class="learn-more-btn" style="text-decoration:none; display:block; text-align:center;">
                            Edit Access
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($_GET['edit_category'])): ?>
           <div id="detailPanel" class="detail-panel active">

                <a href="admin_services.php" class="close-btn" style="text-decoration:none;">✕</a>
                <div class="detail-content" style="padding-top: 40px;">
                    <?php 
                    
                        $category = $_GET['edit_category'];
                        include 'get_sub_services.php'; 
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

