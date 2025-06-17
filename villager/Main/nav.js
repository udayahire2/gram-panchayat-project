// nav.js
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const dropdowns = document.querySelectorAll('.dropdown');

    // Mobile menu toggle functionality
    if (mobileMenuToggle && navLinks && mobileMenuOverlay) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenuToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
        });

        mobileMenuOverlay.addEventListener('click', function() {
            mobileMenuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Close menu when clicking a link
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    mobileMenuToggle.classList.remove('active');
                    navLinks.classList.remove('active');
                    mobileMenuOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    // Handle dropdown menus on mobile
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        const menu = dropdown.querySelector('.dropdown-menu');

        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('show');
            }
        });
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mobileMenuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            dropdowns.forEach(dropdown => dropdown.classList.remove('show'));
        }
    });
});