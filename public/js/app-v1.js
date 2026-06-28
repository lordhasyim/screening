/**
 * Mental Health Screening Quiz - Main JavaScript
 */

$(document).ready(function() {
    // Initialize app
    initializeApp();
});

function initializeApp() {
    // CSRF Token Setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize all components
    initializeFormHandlers();
    initializeDependentDropdowns();
    initializeQuizHandlers();
    initializeValidation();
}

/**
 * Form Handlers
 */
function initializeFormHandlers() {
    // Form submission with loading states
    $('form').on('submit', function(e) {
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        
        if ($submitBtn.length) {
            showLoading($submitBtn);
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(function() {
                hideLoading($submitBtn);
            }, 10000);
        }
    });

    // Real-time form validation feedback
    $('.form-control-pastel, .form-select-pastel').on('input change', function() {
        validateField($(this));
    });
}

/**
 * Dependent Dropdowns (Faculty -> Department)
 */
function initializeDependentDropdowns() {
    if ($('#identityForm').length) return;

    $('#faculty_id').on('change', function() {
        const facultyId = $(this).val();
        const $departmentSelect = $('#department_id');
        
        if (facultyId) {
            loadDepartments(facultyId, $departmentSelect);
        } else {
            resetDepartmentDropdown($departmentSelect);
        }
    });
}

function loadDepartments(facultyId, $departmentSelect) {
    // Show loading state
    $departmentSelect.html('<option value="">Memuat...</option>');
    $departmentSelect.prop('disabled', true);
    
    // AJAX request to get departments
    $.get(`/departments/${facultyId}`)
        .done(function(departments) {
            let options = '<option value="">Pilih Jurusan</option>';
            
            $.each(departments, function(index, department) {
                options += `<option value="${department.id}">${department.name}</option>`;
            });
            
            $departmentSelect.html(options);
            $departmentSelect.prop('disabled', false);
        })
        .fail(function() {
            $departmentSelect.html('<option value="">Gagal memuat data</option>');
            showAlert('danger', 'Gagal memuat data jurusan. Silakan refresh halaman.');
        });
}

function resetDepartmentDropdown($departmentSelect) {
    $departmentSelect.html('<option value="">Pilih Fakultas dulu</option>');
    $departmentSelect.prop('disabled', true);
}

/**
 * Quiz-specific Handlers
 */
function initializeQuizHandlers() {
    // Answer option selection
    $(document).on('click', '.answer-option', function() {
        const $option = $(this);
        const $container = $option.closest('.answer-options');
        const inputName = $option.data('input-name');
        const value = $option.data('value');
        
        // Remove selection from siblings
        $container.find('.answer-option').removeClass('selected');
        
        // Add selection to clicked option
        $option.addClass('selected');
        
        // Update hidden input or create one
        updateHiddenInput(inputName, value, $container);
        
        // Add animation
        $option.addClass('slide-up');
        setTimeout(() => $option.removeClass('slide-up'), 500);
        
        // Validate form progress
        validateQuizProgress();
    });

    // Progress tracking
    updateProgressBar();
}

function updateHiddenInput(name, value, $container) {
    let $input = $container.find(`input[name="${name}"]`);
    
    if ($input.length === 0) {
        $input = $(`<input type="hidden" name="${name}" />`);
        $container.append($input);
    }
    
    $input.val(value);
}

function validateQuizProgress() {
    const totalQuestions = $('.question-card').length;
    const answeredQuestions = $('.answer-option.selected').length;
    const $submitBtn = $('#submitQuiz');
    
    if (totalQuestions === answeredQuestions) {
        $submitBtn.removeClass('btn-pastel-secondary');
        $submitBtn.addClass('btn-pastel-primary');
        $submitBtn.prop('disabled', false);
        $submitBtn.html('<i class="bi bi-check-circle me-2"></i>Lanjutkan');
    } else {
        $submitBtn.removeClass('btn-pastel-primary');
        $submitBtn.addClass('btn-pastel-secondary');
        $submitBtn.prop('disabled', true);
        $submitBtn.html(`Jawab semua pertanyaan (${answeredQuestions}/${totalQuestions})`);
    }
}

/**
 * Form Validation
 */
function initializeValidation() {
    // Real-time NIM validation
    $('#nim').on('input', function() {
        validateNIM($(this));
    });

    // Real-time email validation
    $('#email').on('input', function() {
        validateEmail($(this));
    });

    // Required field validation
    $('input[required], select[required]').on('blur', function() {
        validateRequired($(this));
    });
}

function validateField($field) {
    const fieldType = $field.attr('type') || $field.prop('tagName').toLowerCase();
    
    switch(fieldType) {
        case 'email':
            return validateEmail($field);
        case 'text':
            if ($field.attr('id') === 'nim') {
                return validateNIM($field);
            }
            return validateRequired($field);
        case 'select':
            return validateRequired($field);
        default:
            return validateRequired($field);
    }
}

function validateRequired($field) {
    const value = $field.val().trim();
    const isValid = value.length > 0;
    
    updateFieldValidation($field, isValid, isValid ? '' : 'Field ini wajib diisi');
    return isValid;
}

function validateNIM($field) {
    const nim = $field.val().trim();
    const isValid = nim.length >= 8; // Minimum NIM length
    
    updateFieldValidation($field, isValid, isValid ? '' : 'NIM harus minimal 8 karakter');
    return isValid;
}

function validateEmail($field) {
    const email = $field.val().trim();
    if (email === '') return true; // Email is optional
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(email);
    
    updateFieldValidation($field, isValid, isValid ? '' : 'Format email tidak valid');
    return isValid;
}

function updateFieldValidation($field, isValid, message) {
    const $feedback = $field.siblings('.invalid-feedback');
    
    if (isValid) {
        $field.removeClass('is-invalid').addClass('is-valid');
        $feedback.hide();
    } else {
        $field.removeClass('is-valid').addClass('is-invalid');
        
        if ($feedback.length === 0) {
            $field.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $feedback.text(message).show();
        }
    }
}

/**
 * Progress Bar
 */
function updateProgressBar() {
    const currentStep = parseInt($('body').data('current-step') || 0);
    const totalSteps = 4; // identity, phq9, dass21, result
    const progress = (currentStep / totalSteps) * 100;
    
    $('.progress-bar-pastel').css('width', `${progress}%`);
}

/**
 * Utility Functions
 */
function showLoading($button) {
    const originalText = $button.html();
    $button.data('original-text', originalText);
    $button.html('<div class="spinner-border spinner-border-sm spinner-pastel me-2"></div>Memproses...');
    $button.prop('disabled', true);
}

function hideLoading($button) {
    const originalText = $button.data('original-text');
    if (originalText) {
        $button.html(originalText);
        $button.prop('disabled', false);
    }
}

function showAlert(type, message, duration = 5000) {
    const alertClass = `alert-pastel-${type}`;
    const iconClass = type === 'success' ? 'bi-check-circle' : 
                     type === 'danger' ? 'bi-exclamation-triangle' : 
                     type === 'info' ? 'bi-info-circle' : 'bi-bell';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container').first().prepend(alertHtml);
    
    // Auto-hide after duration
    setTimeout(function() {
        $('.alert').first().fadeOut('slow', function() {
            $(this).remove();
        });
    }, duration);
}

/**
 * Smooth Scrolling
 */
function scrollToTop() {
    $('html, body').animate({
        scrollTop: 0
    }, 600);
}

function scrollToElement($element, offset = 100) {
    $('html, body').animate({
        scrollTop: $element.offset().top - offset
    }, 600);
}

/**
 * Local Storage for Form Backup (Optional)
 */
function backupFormData(formId) {
    const $form = $(`#${formId}`);
    if ($form.length === 0) return;
    
    const formData = {};
    $form.find('input, select, textarea').each(function() {
        const $field = $(this);
        const name = $field.attr('name');
        const value = $field.val();
        
        if (name && value) {
            formData[name] = value;
        }
    });
    
    localStorage.setItem(`quiz_backup_${formId}`, JSON.stringify(formData));
}

function restoreFormData(formId) {
    const $form = $(`#${formId}`);
    if ($form.length === 0) return;
    
    const backupData = localStorage.getItem(`quiz_backup_${formId}`);
    if (!backupData) return;
    
    try {
        const formData = JSON.parse(backupData);
        
        $.each(formData, function(name, value) {
            const $field = $form.find(`[name="${name}"]`);
            if ($field.length) {
                $field.val(value);
                
                // Trigger change event for dependent dropdowns
                if ($field.is('select')) {
                    $field.trigger('change');
                }
            }
        });
        
        showAlert('info', 'Data form sebelumnya telah dipulihkan', 3000);
        
    } catch (e) {
        console.error('Error restoring form data:', e);
    }
}

function clearFormBackup(formId) {
    localStorage.removeItem(`quiz_backup_${formId}`);
}

/**
 * Quiz Navigation
 */
function initializeQuizNavigation() {
    // Back button handler
    $('.btn-back').on('click', function(e) {
        e.preventDefault();
        
        if (confirm('Apakah Anda yakin ingin kembali? Data yang telah diisi akan tersimpan.')) {
            window.history.back();
        }
    });
    
    // Next button validation
    $('.btn-next').on('click', function(e) {
        const $form = $(this).closest('form');
        if ($form.length && !validateForm($form)) {
            e.preventDefault();
            scrollToFirstError();
        }
    });
}

function validateForm($form) {
    let isValid = true;
    const $fields = $form.find('input[required], select[required]');
    
    $fields.each(function() {
        if (!validateField($(this))) {
            isValid = false;
        }
    });
    
    return isValid;
}

function scrollToFirstError() {
    const $firstError = $('.is-invalid').first();
    if ($firstError.length) {
        scrollToElement($firstError, 150);
        $firstError.focus();
    }
}

/**
 * Quiz Results Display
 */
function displayQuizResults(data) {
    const $resultsContainer = $('#quiz-results');
    if ($resultsContainer.length === 0) return;
    
    let resultsHtml = '';
    
    // PHQ-9 Results
    if (data.phq9) {
        resultsHtml += createResultCard('PHQ-9', data.phq9);
    }
    
    // DASS-21 Results
    if (data.dass21) {
        resultsHtml += createResultCard('DASS-21', data.dass21);
    }
    
    $resultsContainer.html(resultsHtml);
    
    // Add animations
    $('.result-card').each(function(index) {
        const $card = $(this);
        setTimeout(function() {
            $card.addClass('fade-in');
        }, index * 200);
    });
}

function createResultCard(testType, result) {
    const badgeClass = `badge-${result.category.toLowerCase().replace(' ', '-')}`;
    
    return `
        <div class="result-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">${testType}</h5>
                <span class="score-badge ${badgeClass}">${result.category}</span>
            </div>
            <div class="mb-2">
                <strong>Skor Total:</strong> ${result.score}
            </div>
            <div class="text-muted">
                ${getInterpretationText(result.category)}
            </div>
        </div>
    `;
}

function getInterpretationText(category) {
    const interpretations = {
        'sangat rendah': 'Tidak terlihat adanya indikasi keluhan signifikan berdasarkan skor tes.',
        'rendah': 'Kondisi relatif baik, namun perhatikan gejala ringan jika muncul perubahan.',
        'sedang': 'Terdapat indikasi gejala sedang. Disarankan melakukan pemeriksaan lebih lanjut.',
        'tinggi': 'Terdapat indikasi gejala berat. Segera pertimbangkan konsultasi ke layanan kesehatan mental.',
        'sangat tinggi': 'Terdapat indikasi gejala yang sangat berat. Disarankan segera menghubungi layanan profesional.'
    };
    
    return interpretations[category.toLowerCase()] || '';
}

/**
 * Keyboard Navigation
 */
function initializeKeyboardNavigation() {
    $(document).on('keydown', function(e) {
        // Enter key to select answer options
        if (e.key === 'Enter' && $(e.target).hasClass('answer-option')) {
            e.preventDefault();
            $(e.target).click();
        }
        
        // Arrow keys for navigation between options
        if ((e.key === 'ArrowUp' || e.key === 'ArrowDown') && $(e.target).hasClass('answer-option')) {
            e.preventDefault();
            navigateAnswerOptions(e.key, $(e.target));
        }
        
        // Escape to clear selection
        if (e.key === 'Escape') {
            $('.answer-option.selected').removeClass('selected');
        }
    });
}

function navigateAnswerOptions(direction, $currentOption) {
    const $container = $currentOption.closest('.answer-options');
    const $options = $container.find('.answer-option');
    const currentIndex = $options.index($currentOption);
    
    let nextIndex;
    if (direction === 'ArrowDown') {
        nextIndex = (currentIndex + 1) % $options.length;
    } else {
        nextIndex = (currentIndex - 1 + $options.length) % $options.length;
    }
    
    $options.eq(nextIndex).focus();
}

/**
 * Data Analytics (for admin insights)
 */
function trackUserInteraction(action, data = {}) {
    // Only track if analytics is enabled and user consented
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            'custom_parameter': data,
            'page_title': document.title
        });
    }
    
    // Could also send to custom analytics endpoint
    // sendAnalytics(action, data);
}

function sendAnalytics(action, data) {
    $.post('/api/analytics', {
        action: action,
        data: data,
        timestamp: new Date().toISOString(),
        user_agent: navigator.userAgent
    }).fail(function() {
        // Silently fail analytics
    });
}

/**
 * Accessibility Improvements
 */
function initializeAccessibility() {
    // Add ARIA labels
    $('.answer-option').attr('role', 'button');
    $('.answer-option').attr('tabindex', '0');
    
    // Announce selection changes to screen readers
    $(document).on('click', '.answer-option', function() {
        const questionText = $(this).closest('.question-card').find('.question-text').text();
        const answerText = $(this).text();
        
        announceToScreenReader(`Terpilih: ${answerText} untuk pertanyaan: ${questionText}`);
    });
    
    // Focus management
    $('.btn-next').on('click', function() {
        setTimeout(function() {
            $('h1, .step-title').first().focus();
        }, 100);
    });
}

function announceToScreenReader(message) {
    const $announcement = $('<div>', {
        'aria-live': 'polite',
        'aria-atomic': 'true',
        'class': 'sr-only',
        'text': message
    });
    
    $('body').append($announcement);
    
    setTimeout(function() {
        $announcement.remove();
    }, 1000);
}

/**
 * Initialize all components when DOM is ready
 */
$(document).ready(function() {
    initializeApp();
    initializeQuizNavigation();
    initializeKeyboardNavigation();
    initializeAccessibility();
    
    // Track page load
    trackUserInteraction('page_view', {
        page: window.location.pathname
    });
    
    // Auto-backup form data every 30 seconds
    setInterval(function() {
        $('form').each(function() {
            const formId = $(this).attr('id');
            if (formId) {
                backupFormData(formId);
            }
        });
    }, 30000);
});