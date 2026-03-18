<?php
include 'db.php';

$faqs = [];
try {
    $stmt = $pdo->query("
        SELECT id, question, answer, category, display_order
        FROM faqs
        WHERE status = 'active'
        ORDER BY display_order ASC, created_at DESC
    ");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("FAQ fetch error: " . $e->getMessage());
    // Fallback to default FAQs if database fails
    $faqs = [
        [
            'id' => 1,
            'question' => 'What are your operating hours?',
            'answer' => 'Our physical offices offer 24/7 access for members. Our support staff are available from 9 AM to 6 PM, Monday to Friday.',
            'category' => 'General'
        ],
        [
            'id' => 2,
            'question' => 'Do I need to sign a long-term contract?',
            'answer' => 'No, we offer highly flexible terms ranging from daily passes to month-to-month leases. You can choose what works best for your startup.',
            'category' => 'Booking'
        ],
        [
            'id' => 3,
            'question' => 'Is internet speed reliable?',
            'answer' => 'Yes, all our locations are equipped with enterprise-grade fiber optic internet with full redundancy to ensure uninterrupted connectivity.',
            'category' => 'Access'
        ]
    ];
}

// Get unique categories
$categories = array_values(array_unique(array_column($faqs, 'category')));
array_unshift($categories, 'All');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | ALTA iHub</title>
    <link rel="stylesheet" href="styles/style1.css">
    <link rel="stylesheet" href="styles/faq.css">
</head>
<body class="faq-body">
    <?php include 'header.php'; ?>

    <main class="faq-container">
        <!-- FAQ Header -->
        <div class="faq-header-section">
            <h1 class="faq-main-title">Frequently Asked Questions</h1>
            <p class="faq-main-subtitle">Find answers to common questions about our services, booking process, and how to get started with ALTA iHub</p>
        </div>

        <!-- Search Box -->
        <div class="faq-search-container">
            <div class="faq-search-box">
                <svg class="faq-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                    type="text" 
                    class="faq-search-input" 
                    id="faqSearchInput" 
                    placeholder="Search for answers..."
                    onkeyup="faqFilterItems()"
                >
            </div>
        </div>

        <!-- Category Filter -->
        <div class="faq-category-filter">
            <?php foreach ($categories as $category): ?>
                <button 
                    class="faq-category-btn <?php echo $category === 'All' ? 'faq-category-btn-active' : ''; ?>" 
                    onclick="faqFilterCategory('<?php echo htmlspecialchars($category); ?>', this)"
                    data-category="<?php echo htmlspecialchars($category); ?>"
                >
                    <?php echo htmlspecialchars($category); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- FAQ List -->
        <div class="faq-list" id="faqList">
            <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div 
                        class="faq-item" 
                        data-category="<?php echo htmlspecialchars($faq['category'] ?? 'General'); ?>"
                        data-question="<?php echo htmlspecialchars(strtolower($faq['question'])); ?>"
                    >
                        <button 
                            class="faq-question-btn" 
                            onclick="faqToggleAnswer(this)"
                            aria-expanded="false"
                        >
                            <span class="faq-question-text"><?php echo htmlspecialchars($faq['question']); ?></span>
                            <span class="faq-question-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="faq-answer-container">
                            <div class="faq-answer-content">
                                <?php echo htmlspecialchars($faq['answer']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="faq-empty-state">
                    <p>No FAQs available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Still Need Help Section -->
        <section class="faq-help-section">
            <h2 class="faq-help-title">Still Need Help?</h2>
            <p class="faq-help-subtitle">Can't find what you're looking for? Get in touch with our team.</p>
            
            <div class="faq-contact-grid">
                <!-- Live Chat Card -->
                <div class="faq-contact-card">
                    <div class="faq-contact-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="faq-contact-card-title">Live Chat</h3>
                    <p class="faq-contact-card-desc">Chat with our team in real-time</p>
                    <a href="#" class="faq-contact-link">Start Chat</a>
                </div>

                <!-- Email Card -->
                <div class="faq-contact-card">
                    <div class="faq-contact-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="faq-contact-card-title">Email Us</h3>
                    <p class="faq-contact-card-desc">Get a reply within 24 hours</p>
                    <a href="mailto:information@perpetualdalta.edu.ph" class="faq-contact-link">Send Email</a>
                </div>

                <!-- Phone Card -->
                <div class="faq-contact-card">
                    <div class="faq-contact-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="faq-contact-card-title">Call Us</h3>
                    <p class="faq-contact-card-desc">Speak with our representatives</p>
                    <a href="tel:+63-2-8871-0639" class="faq-contact-link">Call Now</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // FAQ Filter and Toggle Functions
        function faqToggleAnswer(button) {
            const faqItem = button.closest('.faq-item');
            const answerContainer = faqItem.querySelector('.faq-answer-container');
            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            // Close all other items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    const itemBtn = item.querySelector('.faq-question-btn');
                    const itemAnswer = item.querySelector('.faq-answer-container');
                    itemBtn.setAttribute('aria-expanded', 'false');
                    itemAnswer.style.maxHeight = '0';
                }
            });

            // Toggle current item
            if (isExpanded) {
                button.setAttribute('aria-expanded', 'false');
                answerContainer.style.maxHeight = '0';
            } else {
                button.setAttribute('aria-expanded', 'true');
                answerContainer.style.maxHeight = answerContainer.scrollHeight + 'px';
            }
        }

        function faqFilterCategory(category, button) {
            // Update active button
            document.querySelectorAll('.faq-category-btn').forEach(btn => {
                btn.classList.remove('faq-category-btn-active');
            });
            button.classList.add('faq-category-btn-active');

            // Filter items
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                if (category === 'All') {
                    item.style.display = 'block';
                } else if (item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Reset search
            document.getElementById('faqSearchInput').value = '';
        }

        function faqFilterItems() {
            const searchValue = document.getElementById('faqSearchInput').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const question = item.dataset.question;
                if (question.includes(searchValue)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            console.log('FAQ page initialized');
        });
    </script>
</body>
</html>