<?php
include 'db.php'; 
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/about.css">
</head>
<body class="about-body">
    
<main>
    <!-- Hero Section with Title -->
    <div class="about-hero-container">
        <h1 class="about-hero-title">About ALTA iHub</h1>
    </div>

    <!-- Image Slider Section -->
    <section class="about-slider-section">
        <div class="about-hero-slider">
            <div class="about-slider-wrapper">
                <div class="about-slider">
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg1.jpg" alt="ALTA iHub">
                    </div>
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg2.jpg" alt="ALTA iHub">
                    </div>
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg3.jpg" alt="ALTA iHub">
                    </div>
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg4.jpg" alt="ALTA iHub">
                    </div>
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg5.jpg" alt="ALTA iHub">
                    </div>
                    <div class="about-slider-item">
                        <img src="assets/aboutimg/abtimg6.jpg" alt="ALTA iHub">
                    </div>
                    
                </div>

                <!-- Slider Controls -->
                <div class="about-slider-buttons">
                    <button id="about-prev" class="about-slider-btn" aria-label="Previous slide">&lt;</button>
                    <button id="about-next" class="about-slider-btn" aria-label="Next slide">&gt;</button>
                </div>

                <!-- Slider Dots -->
                <ul class="about-slider-dots">
                    <li class="about-dot about-dot-active"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                    <li class="about-dot"></li>
                </ul>
            </div>
        </div>

        <!-- About Description Card -->
        <div class="about-description-card">
            <h2 class="about-description-title">Who We Are</h2>
            <p class="about-description-text">
                ALTA-Innovations Hub (ALTA-iHUB) is the official innovation and incubation center of the University of Perpetual Help System DALTA. We empower startups and student innovators by bridging academe, industry, and government collaboration to drive digital transformation and ICT innovation.
            </p>
        </div>
    </section>

    <!-- Objectives Section -->
    <section class="about-objectives-section">
        <div class="about-container">
            <h2 class="about-section-heading">ALTA iHub Objectives</h2>
            
            <div class="about-objectives-grid">
                <div class="about-objective-card">
                    <div class="about-objective-number">1</div>
                    <p class="about-objective-text">
                        To become a viable participant in nation building through the enactment of Republic Act No. 7916 (as amended by Republic Act No. 8748) otherwise known as "The Special Economic Zone Act of 1995".
                    </p>
                </div>

                <div class="about-objective-card">
                    <div class="about-objective-number">2</div>
                    <p class="about-objective-text">
                        To administer, coordinate, and collaborate with the Philippine Economic Zone Authority (PEZA) officials and abide with the standards set forth by the Republic Act.
                    </p>
                </div>

                <div class="about-objective-card">
                    <div class="about-objective-number">3</div>
                    <p class="about-objective-text">
                        To bring forward the name of the University of Perpetual Help System DALTA and Jonelta as pioneers in being game-changers in the areas of Agriculture, Medicine, Science, Technology, Tourism, and Entrepreneurship.
                    </p>
                </div>

                <div class="about-objective-card">
                    <div class="about-objective-number">4</div>
                    <p class="about-objective-text">
                        To fortify and transform the DALTA/JONELTA Group of Companies into becoming holistically capable in addressing the challenges of the next generation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="about-vision-section">
        <div class="about-container">
            <h2 class="about-section-heading">Our Vision</h2>
            
            <div class="about-vision-card">
                <p class="about-vision-text">
                    The University of Perpetual Help System DALTA shall emerge as a premier university in the Philippines. It shall provide a venue for the pursuit of excellence in academics, technology, and research through local and international linkages.
                </p>
                <p class="about-vision-text">
                    The University shall take the role of a catalyst for human development. It shall inculcate Christian values and Catholic doctrine, as a way of strengthening the moral fiber of the Filipino, a people who are "Helpers of God", proud of their race and prepared for exemplary global participation in the sciences, arts, humanities, sports, and business.
                </p>
                <p class="about-vision-text">
                    It foresees the Filipino people enjoying a quality of life in abundance, living in peace, and building a nation that the next generation will nourish, cherish, and value.
                </p>
            </div>
        </div>
    </section>
<section class="about-achievements-section">
    <div class="about-container">
        <h2 class="about-section-heading">Our Achievements</h2>
        <div class="about-achievements-card">
            <div class="about-card-info">
                <!-- Placeholder Image -->
                <img src="assets/aboutimg/achiv1.jpg" alt="">
                <p> The University of Perpetual Help System DALTA, through its ALTA Innovation Hub (ALTA-IHUB) , is officially part of the DOST-PCIEERD Technology Business Incubator. (TBI) This milestone was formally <br> recognized at the 9th TBI Summit 2025, held at Bacolod City. ALTA Innovation Hub will continue to empower innovators, startups, and researchers in driving digital transformation, IoT, and AI-driven <br> solutions toward a smarter and more sustainable Philippines. </p>
            </div>
             <!-- Team Member 2 -->
             <div class="about-card-info">
                 <!-- Placeholder Image -->
                <img src="assets/aboutimg/achiv2.jpg" alt="">
                <p>Perpetual Satellite-1, the University of Perpetual Help System DALTA’s first nanosatellite, successfully reached the International Space Station aboard Japan’s HTV-X1 spacecraft on October 30, 2025. Developed through international collaboration with in Malaysia, Thailand, and Japan. This landmark achievement showcases how Filipino innovation, powered by global academic partnerships, is now contributing to the future of space science and technology. </p>
            </div>
            
        </div>
    </div>
</section>

    <!-- Achievements/Team Section -->
    <section class="about-achievements-section">
        <div class="about-container">
            <h2 class="about-section-heading">Leadership Team</h2>
            
            <div class="about-team-grid">
                <div class="about-team-member">
                    <div class="about-team-avatar">
                        <img src="https://via.placeholder.com/300x300?text=Team+Member+3" alt="Jane Doe">
                    </div>
                    <h3 class="about-team-name">Jane Doe</h3>
                    <p class="about-team-position">CEO & Founder</p>
                </div>

                <div class="about-team-member">
                    <div class="about-team-avatar">
                        <img src="https://via.placeholder.com/300x300?text=Team+Member+3" alt="John Smith">
                    </div>
                    <h3 class="about-team-name">John Smith</h3>
                    <p class="about-team-position">Chief Technology Officer</p>
                </div>

                <div class="about-team-member">
                    <div class="about-team-avatar">
                        <img src="https://via.placeholder.com/300x300?text=Team+Member+3" alt="Sarah Connor">
                    </div>
                    <h3 class="about-team-name">Sarah Connor</h3>
                    <p class="about-team-position">Chief Operating Officer</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="about-partners-section">
        <div class="about-container">
            <h2 class="about-section-heading">Our Partners</h2>
            
            <div class="about-partners-carousel">
                <!-- First Group -->
                <div class="about-partners-group">
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/adunest.png" alt="ADU NEST">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/qbo.png" alt="QBO Innovation">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/tbi.png" alt="TBI">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/upscale.png" alt="UPSCALE">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/think.png" alt="THINK">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/dost.jpg" alt="DOST-NCR">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/bluenest.png" alt="BLUE NEST ATENEO">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/nu.png" alt="NU">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/nansy.jpg" alt="NANSY CARE">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/perpetual.jpg" alt="PERPETUAL">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/phildev.png" alt="PHILDEV">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/tup.png" alt="TUP">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/express.jpg" alt="EXPRESS">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/wadhwani.png" alt="WADHWANI">
                    </div>
                </div>

                <!-- Duplicate Group for Infinite Scroll -->
                <div aria-hidden="true" class="about-partners-group">
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/adunest.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/qbo.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/tbi.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/upscale.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/think.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/dost.jpg" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/bluenest.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/nu.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/nansy.jpg" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/perpetual.jpg" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/phildev.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/tup.png" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/express.jpg" alt="">
                    </div>
                    <div class="about-partner-logo">
                        <img src="assets/aboutprtnrimg/wadhwani.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>

<script>
    // About Page Slider
    let aboutSlider = document.querySelector('.about-slider');
    let aboutItems = document.querySelectorAll('.about-slider-item');
    let aboutDots = document.querySelectorAll('.about-dot');
    let aboutPrev = document.getElementById('about-prev');
    let aboutNext = document.getElementById('about-next');

    let aboutActive = 0;
    let aboutLengthItems = aboutItems.length - 1;

    aboutNext.onclick = function() {
        aboutActive = (aboutActive + 1 > aboutLengthItems) ? 0 : aboutActive + 1;
        aboutReloadSlider();
    }

    aboutPrev.onclick = function() {
        aboutActive = (aboutActive - 1 < 0) ? aboutLengthItems : aboutActive - 1;
        aboutReloadSlider();
    }

    let aboutRefreshSlider = setInterval(() => { aboutNext.click() }, 5000);

    function aboutReloadSlider() {
        let checkLeft = aboutItems[aboutActive].offsetLeft;
        aboutSlider.style.left = -checkLeft + 'px';

        let lastActiveDot = document.querySelector('.about-dot.about-dot-active');
        if (lastActiveDot) lastActiveDot.classList.remove('about-dot-active');
        if (aboutDots[aboutActive]) aboutDots[aboutActive].classList.add('about-dot-active');

        clearInterval(aboutRefreshSlider);
        aboutRefreshSlider = setInterval(() => { aboutNext.click() }, 5000);
    }

    aboutDots.forEach((dot, key) => {
        dot.addEventListener('click', function() {
            aboutActive = key;
            aboutReloadSlider();
        })
    });
</script>
<script src="app.js"></script>
</body>
</html>
