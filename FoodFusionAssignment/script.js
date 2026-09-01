/**
 * script.js — FoodFusion
 * Mobile nav, Join Us modal, cookie consent, events carousel, join form validation.
 */
document.addEventListener('DOMContentLoaded', function () {

    /* ---------------------------------------------------------------
       Mobile nav toggle
    --------------------------------------------------------------- */
    var navToggle = document.getElementById('navToggle');
    var mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            var isOpen = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    /* ---------------------------------------------------------------
       Join Us modal
    --------------------------------------------------------------- */
    var joinOverlay = document.getElementById('joinModalOverlay');
    var openTriggers = [
        document.getElementById('openJoinModal'),
        document.getElementById('heroJoinBtn')
    ].filter(Boolean);
    var closeBtn = document.getElementById('closeJoinModal');
    var lastFocusedEl = null;

    function openJoinModal() {
        if (!joinOverlay) return;
        lastFocusedEl = document.activeElement;
        joinOverlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        var firstField = joinOverlay.querySelector('input');
        if (firstField) firstField.focus();
    }

    function closeJoinModal() {
        if (!joinOverlay) return;
        joinOverlay.classList.remove('is-open');
        document.body.style.overflow = '';
        if (lastFocusedEl) lastFocusedEl.focus();
    }

    openTriggers.forEach(function (btn) {
        btn.addEventListener('click', openJoinModal);
    });

    if (closeBtn) closeBtn.addEventListener('click', closeJoinModal);

    if (joinOverlay) {
        joinOverlay.addEventListener('click', function (e) {
            if (e.target === joinOverlay) closeJoinModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && joinOverlay.classList.contains('is-open')) {
                closeJoinModal();
            }
        });
    }

    /* ---------------------------------------------------------------
       Join form validation
       NOTE: register.php doesn't exist yet. Once the registration
       endpoint is built, remove the preventDefault()/demo block below
       and let the form submit normally (or switch to fetch() if we
       want an AJAX submission instead of a full page reload).
    --------------------------------------------------------------- */
    var joinForm = document.getElementById('joinForm');
    var joinFormError = document.getElementById('joinFormError');

    if (joinForm) {
        joinForm.addEventListener('submit', function (e) {
            var firstName = joinForm.first_name.value.trim();
            var lastName = joinForm.last_name.value.trim();
            var email = joinForm.email.value.trim();
            var password = joinForm.password.value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            var errors = [];
            if (!firstName || !lastName) errors.push('First and last name are required.');
            if (!emailPattern.test(email)) errors.push('Enter a valid email address.');
            if (password.length < 8) errors.push('Password must be at least 8 characters.');

            if (errors.length) {
                e.preventDefault();
                joinFormError.textContent = errors[0];
                joinFormError.hidden = false;
                return;
            }

            // TEMPORARY: backend endpoint not built yet.
            // Remove this block once register.php is live.
            e.preventDefault();
            joinFormError.hidden = true;
            alert('Backend registration isn\'t connected yet — this is where account creation will happen.');
        });
    }

    /* ---------------------------------------------------------------
       Cookie consent banner
       Shows 10s after page load unless the visitor already responded.
    --------------------------------------------------------------- */
    var cookieBanner = document.getElementById('cookieBanner');
    var cookieAccept = document.getElementById('cookieAccept');
    var cookieDecline = document.getElementById('cookieDecline');
    var CONSENT_KEY = 'ff_cookie_consent';

    if (cookieBanner && !localStorage.getItem(CONSENT_KEY)) {
        window.setTimeout(function () {
            cookieBanner.classList.add('is-visible');
        }, 10000);
    }

    function setConsent(value) {
        localStorage.setItem(CONSENT_KEY, value);
        cookieBanner.classList.remove('is-visible');
    }

    if (cookieAccept) cookieAccept.addEventListener('click', function () { setConsent('accepted'); });
    if (cookieDecline) cookieDecline.addEventListener('click', function () { setConsent('declined'); });

    /* ---------------------------------------------------------------
       Events carousel
    --------------------------------------------------------------- */
    var track = document.getElementById('carouselTrack');
    var prevBtn = document.getElementById('carouselPrev');
    var nextBtn = document.getElementById('carouselNext');
    var dotsWrap = document.getElementById('carouselDots');

    if (track && dotsWrap) {
        var slides = Array.prototype.slice.call(track.children);

        function slidesPerView() {
            var w = window.innerWidth;
            if (w <= 700) return 1;
            if (w <= 900) return 2;
            return 3;
        }

        function pageCount() {
            return Math.max(1, Math.ceil(slides.length / slidesPerView()));
        }

        function currentPage() {
            var slideWidth = slides[0].getBoundingClientRect().width + 24; // gap
            return Math.round(track.scrollLeft / (slideWidth * slidesPerView()));
        }

        function buildDots() {
            dotsWrap.innerHTML = '';
            var pages = pageCount();
            for (var i = 0; i < pages; i++) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.setAttribute('aria-label', 'Go to slide group ' + (i + 1));
                dot.addEventListener('click', function (i) {
                    return function () { goToPage(i); };
                }(i));
                dotsWrap.appendChild(dot);
            }
            updateDots();
        }

        function updateDots() {
            var dots = dotsWrap.children;
            var active = currentPage();
            for (var i = 0; i < dots.length; i++) {
                dots[i].classList.toggle('active', i === active);
            }
        }

        function goToPage(page) {
            var slideWidth = slides[0].getBoundingClientRect().width + 24;
            track.scrollTo({ left: page * slideWidth * slidesPerView(), behavior: 'smooth' });
        }

        function scrollByOne(direction) {
            var slideWidth = slides[0].getBoundingClientRect().width + 24;
            track.scrollBy({ left: direction * slideWidth, behavior: 'smooth' });
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { scrollByOne(-1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { scrollByOne(1); });

        var scrollTimeout;
        track.addEventListener('scroll', function () {
            window.clearTimeout(scrollTimeout);
            scrollTimeout = window.setTimeout(updateDots, 80);
        });

        var resizeTimeout;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimeout);
            resizeTimeout = window.setTimeout(buildDots, 150);
        });

        buildDots();
    }
});
