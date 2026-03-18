<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$spaces_services_names = [
    'Technology & Engineering Services',
    'Facilities & Innovation Spaces',
    'KIST Park at Molino'
];

$spaces_locations_mapping = [
    'Technology & Engineering Services' => ['Las Piñas', 'Molino', 'Calamba'],
    'Facilities & Innovation Spaces' => ['Las Piñas', 'Molino', 'Calamba'],
    'KIST Park at Molino' => ['Las Piñas', 'Molino', 'Calamba']
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        /***********form data********/
        $applied_service = trim($_POST['applied_service'] ?? '');
        $startup_name = trim($_POST['startup_name'] ?? '');
        $founder_name = trim($_POST['founder_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $industry = trim($_POST['industry'] ?? '');
        $stage = trim($_POST['stage'] ?? '');
        $team_size = trim($_POST['team_size'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $additional_comments = trim($_POST['additional_comments'] ?? '');
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        $terms_agreed = isset($_POST['booking_terms_agree']);
        $location = null;

         // for space
        if (in_array($applied_service, $spaces_services_names)) {
            $location = trim($_POST['location'] ?? '');
            if (empty($location)) {
                $_SESSION['booking_error'] = "Please select a location for this service.";
                header("Location: booking.php");
                exit;
            }
        }

        // Validation
        if (
            empty($applied_service) ||
            empty($startup_name) ||
            empty($founder_name) ||
            empty($email) ||
            empty($phone) ||
            empty($industry) ||
            empty($stage) ||
            empty($description) ||
            !$terms_agreed
        ) {
            $_SESSION['booking_error'] = "Required fields missing or terms not accepted.";
            header("Location: booking.php");
            exit;
        }

        /* ===============================
           FILE UPLOAD HANDLING
           ================================= */
           $upload_path = null;

           if (!empty($_FILES['pitch_deck']['name'])) {
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileMime = mime_content_type($_FILES['pitch_deck']['tmp_name']);
            
            if (!in_array($fileMime, $allowedMimes)) {
                $_SESSION['booking_error'] = "Invalid file type. Allowed: PDF, JPG, PNG only.";
                header("Location: booking.php");
                exit;
            }

            if ($_FILES['pitch_deck']['size'] > $maxSize) {
                $_SESSION['booking_error'] = "File exceeds 5MB limit.";
                header("Location: booking.php");
                exit;
            }

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $fileExtension = pathinfo($_FILES['pitch_deck']['name'], PATHINFO_EXTENSION);
            $fileName = $user_id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'uploads/' . $fileName;

            if (!move_uploaded_file($_FILES['pitch_deck']['tmp_name'], $filePath)) {
                $_SESSION['booking_error'] = "Failed to upload file.";
                header("Location: booking.php");
                exit;
            }

            $upload_path = $filePath;
        }

        /* ===============================
           INSERT INTO BOOKINGS TABLE
           ================================= */
           $stmt = $pdo->prepare("
            INSERT INTO bookings
            (user_id, service_name, startup_name, founder_name, email, phone, industry, stage, team_size, description, additional_comments, pitch_deck_path, appointment_date, appointment_start_time, appointment_end_time, location,status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

           $stmt->execute([
            $user_id,
            $applied_service,
            $startup_name,
            $founder_name,
            $email,
            $phone,
            $industry,
            $stage,
            $team_size,
            $description,
            $additional_comments,
            $upload_path,
            $appointment_date,
            $start_time,
            $end_time,
            $location
        ]);

        /* ===============================
           CREATE NOTIFICATION
           ================================= */
           $notify = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, ?, ?, 'application')
            ");

           $notify->execute([
            $user_id,
            "Application Submitted",
            "Your application for {$applied_service} has been received and is under review."
        ]);

           $_SESSION['booking_success'] = "Application submitted successfully!";
           header("Location: booking.php?success=1");
           exit;

       } catch (PDOException $e) {
        $_SESSION['booking_error'] = "Database Error: Please try again later.";
        error_log("Booking error: " . $e->getMessage());
        header("Location: booking.php");
        exit;
    }
}

$booking_success = isset($_GET['success']);
$booking_error = $_SESSION['booking_error'] ?? '';
unset($_SESSION['booking_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Services | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/booking.css">
</head>
<body class="booking-body">

    <main class="booking-main-content">
        <!-- Services View -->
        <div id="bookingServicesView" class="booking-services-view">
            <h2 class="booking-section-title">Select a Service Category</h2>
            <div class="booking-services-grid">
                <button type="button" class="booking-service-card" onclick="bookingSelectService(0)">TECHNOLOGY & ENGINEERING SERVICES</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(1)">STARTUP & BUSINESS SUPPORT</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(2)">INNOVATION & INCUBATION</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(3)">TRAINING & CAPACITY BUILDING</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(4)">INDUSTRY & ACADEMIC COLLABORATION</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(5)">FUNDING & INVESTMENT SUPPORT</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(6)">FACILITIES & INNOVATION SPACES</button>
                <button type="button" class="booking-service-card" onclick="bookingSelectService(7)">KIST PARK AT MOLINO</button>
            </div>
            
            <div class="booking-welcome-section" id="bookingWelcomeSection">
                <h1 class="booking-welcome-title">Welcome to the Application Portal</h1>
                <p class="booking-welcome-text">Please select a service category above to view available dates and begin your application.</p>
            </div>
        </div>

        <!-- Benefits and Calendar View -->
        <div id="bookingBenefitsCalendarView" class="booking-benefits-calendar-view" style="display: none;">
            <button type="button" class="booking-back-btn" onclick="bookingBackToServices()">← Back to Services</button>
            
            <h2 class="booking-selected-service-title" id="bookingSelectedServiceTitle"></h2>
            <p class="booking-selected-service-subtitle">Select an available date to submit your application</p>

            <div class="booking-benefits-calendar-container">
                <!-- Program Benefits Panel -->
                <div class="booking-benefits-panel">
                    <div class="booking-benefits-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="M22 4L12 14.01l-3-3"/>
                        </svg>
                        <h3>Program Benefits</h3>
                    </div>
                    <ul class="booking-benefits-list" id="bookingBenefitsList"></ul>
                </div>

                <!-- Calendar Panel -->
                <div class="booking-calendar-panel">
                    <div class="booking-calendar-controls">
                        <button type="button" class="booking-calendar-nav-btn" onclick="bookingPreviousMonth()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <div class="booking-calendar-month-year">
                            <h2 id="bookingMonthName">JANUARY</h2>
                            <h2 id="bookingYearNumber">2026</h2>
                        </div>
                           <!-- Location Dropdown -->
                    <div class="booking-form-group" id="booking-location-group" style="display: none;">
                        <label for="booking-location">Preferred Location *</label>
                        <select id="booking-location" name="location" class="booking-form-select"></select>
                    </div>
                        <button type="button" class="booking-calendar-nav-btn" onclick="bookingNextMonth()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>

                    <div class="booking-calendar-days-header">
                        <div class="booking-calendar-day-header">SUN</div>
                        <div class="booking-calendar-day-header">MON</div>
                        <div class="booking-calendar-day-header">TUE</div>
                        <div class="booking-calendar-day-header">WED</div>
                        <div class="booking-calendar-day-header">THU</div>
                        <div class="booking-calendar-day-header">FRI</div>
                        <div class="booking-calendar-day-header">SAT</div>
                    </div>

                    <div class="booking-calendar-grid" id="bookingCalendarGrid"></div>

                 

                    <div class="booking-calendar-legend">
                        <div class="booking-legend-item">
                            <div class="booking-legend-box booking-legend-available"></div>
                            <span>Available</span>
                        </div>
                        <div class="booking-legend-item">
                            <div class="booking-legend-box booking-legend-unavailable"></div>
                            <span>Not Available</span>
                        </div>
                        <div class="booking-legend-item">
                            <div class="booking-legend-box booking-legend-past"></div>
                            <span>Past Date</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php if ($booking_success): ?>
        <div class="booking-success-message">
            ✅ Application submitted successfully! 🎉
        </div>
    <?php endif; ?>

    <?php if ($booking_error): ?>
        <div class="booking-error-message">
            ❌ <?php echo htmlspecialchars($booking_error); ?>
        </div>
    <?php endif; ?>

    <!-- Application Modal -->
    <div id="bookingApplicationModal" class="booking-modal-overlay" style="display: none;" onclick="bookingCloseModalOnBackdrop(event)">
        <div class="booking-modal-card" onclick="event.stopPropagation()">
            <section class="booking-form-card">
                <div class="booking-card-header">
                    <span class="booking-icon-yellow">📝</span>
                    <h3>Application Form</h3>
                    <button type="button" class="booking-close-modal-btn" onclick="bookingCloseModal()">&times;</button>
                </div>
                <p class="booking-form-instruction">Fill out the form below to apply for the service</p>

                <form action="booking.php" method="POST" enctype="multipart/form-data" class="booking-form">
                    <input type="hidden" name="applied_service" id="bookingAppliedService">
                    <input type="hidden" name="appointment_date" id="bookingAppointmentDate">

                    <!-- Basic Info Section -->
                    <h4 class="booking-form-section-title">Basic Information</h4>
                    <div class="booking-grid-2">
                        <div class="booking-form-group">
                            <label for="booking-startup-name">Startup Name *</label>
                            <input type="text" id="booking-startup-name" name="startup_name" class="booking-form-input" required placeholder="TechVenture Inc.">
                        </div>
                        <div class="booking-form-group">
                            <label for="booking-founder-name">Founder Name *</label>
                            <input type="text" id="booking-founder-name" name="founder_name" class="booking-form-input" required placeholder="Juan Dela Cruz">
                        </div>
                    </div>
                    <div class="booking-grid-2">
                        <div class="booking-form-group">
                            <label for="booking-email">Email Address *</label>
                            <input type="email" id="booking-email" name="email" class="booking-form-input" required placeholder="founder@startup.com">
                        </div>
                        <div class="booking-form-group">
                            <label for="booking-phone">Phone Number *</label>
                            <input type="tel" id="booking-phone" name="phone" class="booking-form-input" required placeholder="+63 912 345 6789">
                        </div>
                    </div>

                    <!-- Startup Details Section -->
                    <h4 class="booking-form-section-title">Startup Details</h4>
                    <div class="booking-grid-3">
                        <div class="booking-form-group">
                            <label for="booking-industry">Industry *</label>
                            <select id="booking-industry" name="industry" class="booking-form-select" required>
                                <option value="">Select Industry</option>
                                <option value="technology">Technology & Software</option>
                                <option value="healthcare">Healthcare & MedTech</option>
                                <option value="fintech">FinTech</option>
                                <option value="ecommerce">E-Commerce</option>
                                <option value="agritech">AgriTech</option>
                                <option value="edtech">EdTech</option>
                                <option value="cleantech">Clean Tech & Sustainability</option>
                                <option value="iot">IoT & Smart Systems</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="booking-form-group">
                            <label for="booking-stage">Startup Stage *</label>
                            <select id="booking-stage" name="stage" class="booking-form-select" required>
                                <option value="">Select Stage</option>
                                <option value="idea">Idea Stage</option>
                                <option value="mvp">Prototype/MVP</option>
                                <option value="early">Early Stage</option>
                                <option value="growth">Growth Stage</option>
                            </select>
                        </div>
                        <div class="booking-form-group">
                            <label for="booking-team-size">Team Size *</label>
                            <select id="booking-team-size" name="team_size" class="booking-form-select" required>
                                <option value="">Select Team Size</option>
                                <option value="solo">Solo Founder</option>
                                <option value="2-3">2-3 Members</option>
                                <option value="4-5">4-5 Members</option>
                                <option value="6+">6+ Members</option>
                            </select>
                        </div>
                    </div>

                    <div class="booking-form-group">
                        <label for="booking-description">Startup Description *</label>
                        <textarea id="booking-description" name="description" class="booking-form-textarea" required placeholder="Brief description of your startup..."></textarea>
                    </div>

                    <!-- Session Time Section -->
                    <h4 class="booking-form-section-title">Session Time</h4>
                    <div class="booking-grid-2">
                        <div class="booking-form-group">
                            <label for="booking-start-time">Start Time *</label>
                            <input type="time" id="booking-start-time" name="start_time" class="booking-form-input" required>
                        </div>
                        <div class="booking-form-group">
                            <label for="booking-end-time">End Time *</label>
                            <input type="time" id="booking-end-time" name="end_time" class="booking-form-input" required>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <h4 class="booking-form-section-title">Supporting Documents</h4>
                    <div class="booking-form-group">
                        <label for="booking-pitch-deck">Upload Pitch Deck / Business Plan (Optional)</label>
                        <div class="booking-file-upload-wrapper">
                            <input type="file" id="booking-pitch-deck" name="pitch_deck" accept=".pdf,.ppt,.pptx,.doc,.docx">
                            <p class="booking-form-help">Accepted formats: PDF, PPT, PPTX, DOC, DOCX (Max 5MB)</p>
                        </div>
                    </div>

                    <!-- Additional Comments -->
                    <div class="booking-form-group">
                        <label for="booking-additional-comments">Additional Comments</label>
                        <textarea id="booking-additional-comments" name="additional_comments" class="booking-form-textarea" placeholder="Any additional information you'd like to share"></textarea>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="booking-terms-box">
                        <label for="booking-terms-agree" class="booking-terms-label">
                            <input type="checkbox" id="booking-terms-agree" name="booking_terms_agree" required class="booking-form-checkbox">
                            <span>
                                I agree to the terms and conditions and confirm that all information provided is accurate. 
                                I understand that ALTA iHub will review my application and contact me within 5-7 business days.
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="booking-btn-submit-full">Submit Application 🚀</button>
                </form>
            </section>

            <div class="booking-info-card">
                <div class="booking-info-card-content">
                    <h3 class="booking-info-card-title">What Happens Next?</h3>
                    <div class="booking-info-card-list">
                        <div class="booking-info-item">
                            <span class="booking-badge booking-badge-success">1</span>
                            <div>
                                <strong class="booking-info-item-title">Review (5-7 business days)</strong>
                                <p class="booking-info-item-desc">Our team will review your application</p>
                            </div>
                        </div>
                        <div class="booking-info-item">
                            <span class="booking-badge booking-badge-success">2</span>
                            <div>
                                <strong class="booking-info-item-title">Interview (if shortlisted)</strong>
                                <p class="booking-info-item-desc">Present your startup to our selection committee</p>
                            </div>
                        </div>
                        <div class="booking-info-item">
                            <span class="booking-badge booking-badge-success">3</span>
                            <div>
                                <strong class="booking-info-item-title">Final Decision</strong>
                                <p class="booking-info-item-desc">Receive notification within 2-3 weeks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
// Services Data
const bookingServices = [
'Technology & Engineering Services',
'Startup & Business Support',
'Innovation & Incubation',
'Training & Capacity Building',
'Industry & Academic Collaboration',
'Funding & Investment Support',
'Facilities & Innovation Spaces',
'KIST Park at Molino'
];

const spacesServices = [
'Technology & Engineering Services',
'Facilities & Innovation Spaces',
'KIST Park at Molino'
];

const spacesLocationsMapping = {
'Technology & Engineering Services': ['Las Piñas','Molino','Calamba'],
'Facilities & Innovation Spaces': ['Las Piñas','Molino','Calamba'],
'KIST Park at Molino': ['Las Piñas','Molino','Calamba']
};

// Program Benefits
const bookingProgramBenefits = {
'Technology & Engineering Services': [
'AI, Data Analytics, Software & App Dev',
'IoT and Smart Manufacturing',
'Renewable Energy and Smart Manufacturing'
],
'Startup & Business Support': [
'Business Model Development',
'Startup Mentorship & Coaching',
'Pitch Deck & Investor Readiness Training',
'Legal, IP, and Regulatory Advisory',
'Company Registration & Compliance Guidance'
],
'Innovation & Incubation': [
'Startup Incubation & Acceleration Programs',
'Idea Validation & Product Development Support',
'Technology Commercialization Assistance',
'Research-to-Market (R2M) Mentorship',
'Proof of Concept (PoC) & Prototype Development'
],
'Training & Capacity Building': [
'Technical Skills Training & Bootcamps',
'Industry Certification Programs',
'Student Internship & Apprenticeship Programs',
'Faculty & Researcher Upskilling',
'Innovation & Entrepreneurship Workshops'
],
'Industry & Academic Collaboration': [
'Industry-Academe Partnership Programs',
'Joint Research & Development Projects',
'Corporate Innovation Challenges & Hackathons',
'Technology Transfer & Licensing Support'
],
'Funding & Investment Support': [
'Startup Grant & Seed Funding Assistance',
'Government Funding Advisory',
'Investor & Venture Capital Matching',
'Pitching Events & Demo Days'
],
'Facilities & Innovation Spaces': [
'Co-working & Collaboration Spaces',
'Innovation Labs',
'Testing & Development Areas',
'Virtual Incubation & Remote Mentorship',
'KIST Park at Molino'
],
'KIST Park at Molino': [
'Startup & Research Offices',
'Technology Demonstration Area',
'Industry Partner Collaboration Hub',
'Training & Event Facilities',
'Smart Campus Environment'
]
};

const bookingGenerateAvailableDates = () => {
const dates = [];
const today = new Date();
for(let month=0; month<12; month++){
for(let day=1; day<=31; day++){
const date = new Date(today.getFullYear(),today.getMonth()+month,day);
if(date.getMonth()===(today.getMonth()+month)%12){
dates.push(date);
}

}
}
return dates;
};
const bookingServiceAvailability =
bookingServices.map(()=>bookingGenerateAvailableDates());
// State
let bookingCurrentServiceIndex=null;
let bookingCurrentDate=new Date();
let bookingSelectedDate=null;
let bookingSelectedLocation=null;

const bookingMonthNames=[
'JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'
];
// SELECT SERVICE
function bookingSelectService(index){
bookingCurrentServiceIndex=index;
bookingSelectedLocation=null;
const serviceName=bookingServices[index];
document.getElementById('bookingWelcomeSection').style.display='none';
document.getElementById('bookingBenefitsCalendarView').style.display='block';
document.getElementById('bookingSelectedServiceTitle').textContent=
serviceName.toUpperCase();
// highlight selected card
const serviceCards=document.querySelectorAll('.booking-service-card');
serviceCards.forEach((card,idx)=>{
if(idx===index){
card.classList.add('booking-selected');
}else{
card.classList.remove('booking-selected');
}
});
// benefits
const benefitsList=document.getElementById('bookingBenefitsList');
benefitsList.innerHTML='';
bookingProgramBenefits[serviceName].forEach(benefit=>{
const li=document.createElement('li');
li.textContent=benefit;
benefitsList.appendChild(li);
});
// LOCATION DROPDOWN
const locationGroup=document.getElementById('booking-location-group');
const locationSelect=document.getElementById('booking-location');
locationSelect.innerHTML='';
if(spacesServices.includes(serviceName)){
locationGroup.style.display='block';
const defaultOpt=document.createElement('option');
defaultOpt.value='';
defaultOpt.textContent='Select Location';
locationSelect.appendChild(defaultOpt);
spacesLocationsMapping[serviceName].forEach(loc=>{
const opt=document.createElement('option');
opt.value=loc;
opt.textContent=loc;
locationSelect.appendChild(opt);
});
// wait for location selection before showing calendar
locationSelect.onchange=function(){
bookingSelectedLocation=this.value;
if(bookingSelectedLocation){
bookingRenderCalendar();
}
};
}else{
locationGroup.style.display='none';
bookingSelectedLocation='default';
bookingRenderCalendar();
}
window.scrollTo({top:0,behavior:'smooth'});
}
// BACK BUTTON
function bookingBackToServices(){
bookingCurrentServiceIndex=null;
bookingCurrentDate=new Date();
bookingSelectedLocation=null;
document.getElementById('bookingBenefitsCalendarView').style.display='none';
document.getElementById('bookingWelcomeSection').style.display='block';
const serviceCards=document.querySelectorAll('.booking-service-card');
serviceCards.forEach(card=>{
card.classList.remove('booking-selected');
});
}
// CALENDAR RENDER
function bookingRenderCalendar(){
if(!bookingSelectedLocation)return;
const monthName=document.getElementById('bookingMonthName');
const yearNumber=document.getElementById('bookingYearNumber');
const calendarGrid=document.getElementById('bookingCalendarGrid');
monthName.textContent=bookingMonthNames[bookingCurrentDate.getMonth()];
yearNumber.textContent=bookingCurrentDate.getFullYear();
const firstDay=new Date(
bookingCurrentDate.getFullYear(),
bookingCurrentDate.getMonth(),
1
).getDay();
const daysInMonth=new Date(
bookingCurrentDate.getFullYear(),
bookingCurrentDate.getMonth()+1,
0
).getDate();
calendarGrid.innerHTML='';
for(let i=0;i<firstDay;i++){
const emptyDiv=document.createElement('div');
emptyDiv.className='booking-calendar-day booking-calendar-day-empty';
calendarGrid.appendChild(emptyDiv);
}
const today=new Date();
today.setHours(0,0,0,0);
for(let day=1;day<=daysInMonth;day++){
const date=new Date(
bookingCurrentDate.getFullYear(),
bookingCurrentDate.getMonth(),
day
);
const dayDiv=document.createElement('div');
dayDiv.className='booking-calendar-day';
dayDiv.textContent=day;
const isPast=date<today;
const isAvailable=bookingIsDateAvailable(date);
if(isPast){
dayDiv.classList.add('booking-calendar-day-past');
}
else if(isAvailable){
dayDiv.classList.add('booking-calendar-day-available');
dayDiv.onclick=()=>bookingOpenApplicationModal(date);
}
else{
dayDiv.classList.add('booking-calendar-day-unavailable');
}
calendarGrid.appendChild(dayDiv);
}
}
// =============================
function bookingIsDateAvailable(date){
if(bookingCurrentServiceIndex===null)return false;
return bookingServiceAvailability[
bookingCurrentServiceIndex
].some(availableDate=>{
return availableDate.getDate()===date.getDate() &&
availableDate.getMonth()===date.getMonth() &&
availableDate.getFullYear()===date.getFullYear();
});
}
// =============================
function bookingPreviousMonth(){
bookingCurrentDate=new Date(
bookingCurrentDate.getFullYear(),
bookingCurrentDate.getMonth()-1,
1
);
bookingRenderCalendar();
}
// =============================
function bookingNextMonth(){
bookingCurrentDate=new Date(
bookingCurrentDate.getFullYear(),
bookingCurrentDate.getMonth()+1,
1
);
bookingRenderCalendar();
}
// OPEN MODAL
function bookingOpenApplicationModal(date){
bookingSelectedDate=date;
const serviceName=bookingServices[bookingCurrentServiceIndex];
document.getElementById('bookingAppliedService').value=serviceName;
const year=date.getFullYear();
const month=String(date.getMonth()+1).padStart(2,'0');
const day=String(date.getDate()).padStart(2,'0');
document.getElementById('bookingAppointmentDate').value=
`${year}-${month}-${day}`;
const modal=document.getElementById('bookingApplicationModal');
modal.style.display='flex';
document.body.style.overflow='hidden';
}
// =============================
function bookingCloseModal(){
document.getElementById('bookingApplicationModal').style.display='none';
document.body.style.overflow='auto';
bookingSelectedDate=null;
}
// =============================
function bookingCloseModalOnBackdrop(event){
if(event.target===event.currentTarget){
bookingCloseModal();
}
}
// ESC CLOSE
document.addEventListener('keydown',(e)=>{
if(e.key==='Escape'){
bookingCloseModal();
}
});
    </script>
</body>
</html>