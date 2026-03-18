/* ============================================
   ALTA iHUB - JavaScript Functionality
   ============================================ */

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Active navigation link on scroll
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('section[id]');
    const scrollY = window.pageYOffset;

    sections.forEach(section => {
        const sectionHeight = section.offsetHeight;
        const sectionTop = section.offsetTop - 100;
        const sectionId = section.getAttribute('id');
        
        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
});

// Card hover effects
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px)';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Accordion functionality for FAQ
function initAccordion() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const accordionItem = this.parentElement;
            const wasActive = accordionItem.classList.contains('active');
            
            // Close all accordion items
            document.querySelectorAll('.accordion-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!wasActive) {
                accordionItem.classList.add('active');
            }
        });
    });
}

// Tabs functionality
function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#ef4444';
            } else {
                input.style.borderColor = '';
            }
        });
        
        if (isValid) {
            showToast('Form submitted successfully!', 'success');
            form.reset();
        } else {
            showToast('Please fill in all required fields', 'error');
        }
    });
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background-color: ${type === 'success' ? '#22c55e' : '#ef4444'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 9999;
        animation: slideInUp 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutDown 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Message list functionality
function initMessageList() {
    const messageItems = document.querySelectorAll('.message-list-item');
    
    messageItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            messageItems.forEach(i => i.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');
            // Remove unread class
            this.classList.remove('unread');
        });
    });
}

// File upload preview
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            if (fileName) {
                showToast(`File "${fileName}" selected`, 'success');
            }
        });
    });
}

// Search functionality
function initSearch(searchInputId, itemsSelector) {
    const searchInput = document.getElementById(searchInputId);
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const items = document.querySelectorAll(itemsSelector);
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideOutDown {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }
`;
document.head.appendChild(style);

// Initialize all functionality on page load
document.addEventListener('DOMContentLoaded', function() {
    initAccordion();
    initTabs();
    initMessageList();
    initFileUpload();
    validateForm('apply-form');
    validateForm('booking-form');
    validateForm('request-form');
    validateForm('contact-form');
    initSearch('message-search', '.message-list-item');
    
    console.log('ALTA iHUB Website Loaded Successfully!');
});
// AJAX slot checking
    const serviceSelect = document.getElementById('service-detail-select');
    const locationSelect = document.getElementById('location-select');
    const slotStatus = document.getElementById('slot-status');

    function checkSlots() {
        const service = serviceSelect.value;
        const location = locationSelect.value;

        if (!service || !location) {
            slotStatus.style.display = 'none';
            return;
        }

        fetch(`check_slots.php?service=${encodeURIComponent(service)}&location=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
            slotStatus.style.display = 'block';
            slotStatus.innerHTML = data.message;
            if (data.status === 'success') {
                slotStatus.style.background = '#d4edda';
                slotStatus.style.color = '#155724';
                slotStatus.style.border = '1px solid #c3e6cb';
            } else {
                slotStatus.style.background = '#f8d7da';
                slotStatus.style.color = '#721c24';
                slotStatus.style.border = '1px solid #f5c6cb';
            }
        });
    }

    serviceSelect.addEventListener('change', checkSlots);
    locationSelect.addEventListener('change', checkSlots);


