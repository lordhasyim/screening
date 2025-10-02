/**
 * Welcome Page JavaScript (jQuery)
 * Mental Health Screening - UM
 */

$(document).ready(function() {
    'use strict';

    // Initialize all functions
    initAnimations();
    initScrollEffects();
    initButtonTracking();
    initParallax();
    initStatsCounter();

    /**
     * Initialize entrance animations with stagger effect
     */
    function initAnimations() {
        // Animate feature cards with delay
        $('.feature-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(30px)'
            });
            
            $(this).delay(200 + (index * 100)).animate({
                opacity: 1
            }, {
                duration: 600,
                step: function(now) {
                    $(this).css('transform', 'translateY(' + (30 - (30 * now)) + 'px)');
                }
            });
        });

        // Animate info cards
        $('.info-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(30px)'
            });
            
            $(this).delay(800 + (index * 200)).animate({
                opacity: 1
            }, {
                duration: 600,
                step: function(now) {
                    $(this).css('transform', 'translateY(' + (30 - (30 * now)) + 'px)');
                }
            });
        });
    }

    /**
     * Scroll-based animations
     */
    function initScrollEffects() {
        // Smooth scroll for scroll indicator
        $('.scroll-indicator').on('click', function() {
            $('html, body').animate({
                scrollTop: $('.features-section').offset().top
            }, 800, 'swing');
        });

        // Scroll animation for elements
        $(window).on('scroll', function() {
            $('.animate-on-scroll').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                // Check if element is in viewport
                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('animate-in');
                }
            });
        });

        // Add animate-on-scroll class to elements
        $('.instructions-card, .stat-card').addClass('animate-on-scroll');
    }

    /**
     * Track button clicks and add effects
     */
    function initButtonTracking() {
        var $startButton = $('.btn-start-screening');
        
        if ($startButton.length) {
            // Add ripple effect on click
            $startButton.on('click', function(e) {
                var $button = $(this);
                var $ripple = $('<span class="ripple-effect"></span>');
                
                // Position ripple at click location
                var rect = this.getBoundingClientRect();
                var size = Math.max(rect.width, rect.height);
                var x = e.clientX - rect.left - size / 2;
                var y = e.clientY - rect.top - size / 2;
                
                $ripple.css({
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px'
                });
                
                $button.append($ripple);
                
                // Remove ripple after animation
                setTimeout(function() {
                    $ripple.remove();
                }, 600);

                // Track event (if analytics function exists)
                if (typeof trackUserInteraction === 'function') {
                    trackUserInteraction('quiz_start_clicked');
                }
            });

            // Add pulse animation on hover
            $startButton.on('mouseenter', function() {
                $(this).css('animation', 'pulse 0.6s ease-in-out');
            });

            $startButton.on('animationend', function() {
                $(this).css('animation', '');
            });
        }
    }

    /**
     * Parallax effect for hero background
     */
    function initParallax() {
        var $heroBackground = $('.hero-background');
        
        if ($heroBackground.length && $(window).width() > 768) {
            $(window).on('scroll', function() {
                var scrolled = $(window).scrollTop();
                var parallaxSpeed = 0.5;
                
                // Apply parallax transform
                $heroBackground.css('transform', 'translateY(' + (scrolled * parallaxSpeed) + 'px)');
            });
        }
    }

    /**
     * Animated counter for statistics
     */
    function animateCounter($element, target, duration) {
        duration = duration || 2000;
        var start = 0;
        var range = target - start;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = timestamp - startTime;
            var percentage = Math.min(progress / duration, 1);
            
            // Easing function (easeOutQuad)
            var easedPercentage = percentage * (2 - percentage);
            var current = Math.floor(start + (range * easedPercentage));
            
            $element.text(current.toLocaleString());
            
            if (percentage < 1) {
                requestAnimationFrame(step);
            } else {
                $element.text(target.toLocaleString());
            }
        }
        
        requestAnimationFrame(step);
    }

    /**
     * Initialize stats counter with scroll detection
     */
    function initStatsCounter() {
        var counted = false;
        
        $(window).on('scroll', function() {
            var $statCards = $('.stat-card');
            
            if ($statCards.length && !counted) {
                var elementTop = $statCards.first().offset().top;
                var elementBottom = elementTop + $statCards.first().outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                // Check if element is 50% visible
                if (elementTop < viewportBottom - ($statCards.first().outerHeight() * 0.5)) {
                    counted = true;
                    
                    $statCards.each(function() {
                        var $numberElement = $(this).find('.stat-number');
                        var targetValue = parseInt($numberElement.text().replace(/,/g, ''));
                        
                        $numberElement.text('0');
                        animateCounter($numberElement, targetValue, 2000);
                    });
                }
            }
        });
    }

    /**
     * Add hover effects to cards
     */
    $('.feature-card, .info-card').hover(
        function() {
            $(this).css('transform', 'translateY(-10px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    /**
     * Fade in alerts automatically
     */
    $('.alert').each(function() {
        $(this).hide().fadeIn(400);
    });

    /**
     * Auto-hide alerts after 5 seconds
     */
    setTimeout(function() {
        $('.alert').fadeOut(400, function() {
            $(this).alert('close');
        });
    }, 5000);

});

// Add CSS for effects dynamically
$('<style>')
    .text(`
        .btn-start-screening {
            position: relative;
            overflow: hidden;
        }

        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card, .info-card {
            transition: all 0.3s ease;
        }
    `)
    .appendTo('head');