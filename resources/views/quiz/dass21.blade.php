@extends('layouts.app')

@section('title', 'Skrining DASS-21 - Kesehatan Mental ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Step Navigation -->
            <div class="step-nav fade-in">
                <div class="step-indicator">
                    <div class="d-flex align-items-center">
                        <div class="step-number">3</div>
                        <div class="ms-3">
                            <div class="step-title">Skrining Kesehatan Mental - Tahap II</div>
                            <small class="text-muted">Penilaian lanjutan untuk evaluasi yang lebih komprehensif</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Langkah 3 dari 4</small>
                    </div>
                </div>
                <div class="progress progress-pastel mt-3">
                    <div class="progress-bar progress-bar-pastel" style="width: 75%;"></div>
                </div>
            </div>

            <!-- Information Card (Show only on first question) -->
            <div id="infoCard" class="card card-gradient text-white mb-4 slide-up">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="font-size: 2.5rem;">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <div>
                            <h5 class="mb-2">Tahap Lanjutan Diperlukan</h5>
                            <p class="mb-0 opacity-90">
                                Berdasarkan hasil tahap sebelumnya, kami perlu melakukan penilaian yang lebih mendalam. 
                                Klik "Mulai" untuk memulai {{ count($dass21Questions) }} pertanyaan berikutnya.
                            </p>
                        </div>
                        <div class="ms-auto">
                            <button id="startBtn" class="btn btn-light btn-lg">
                                <i class="bi bi-play-circle me-2"></i>Mulai
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Container -->
            <div id="quizContainer" class="card card-pastel slide-up" style="min-height: 500px; display: none;">
                <div class="card-body d-flex flex-column justify-content-center p-5">
                    
                    <!-- Question Header -->
                    <div class="text-center mb-4">
                        <h5 class="text-primary mb-2">Silahkan Jawab Pertanyaan ini</h5>
                        <div class="small text-muted">
                            Pertanyaan <span id="currentQuestionNumber">1</span> dari {{ count($dass21Questions) }}
                        </div>
                        <div class="small text-muted mt-1">
                            Estimasi waktu tersisa: <span id="timeEstimate">-</span> menit
                        </div>
                    </div>

                    <!-- Current Question Display -->
                    <div id="questionContainer" class="text-center mb-5">
                        <h4 id="questionText" class="mb-4 text-dark">
                            <!-- Question will be populated by JavaScript -->
                        </h4>
                    </div>

                    <!-- Answer Options -->
                    <div id="answerOptions" class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Tidak Pernah" data-color="success" role="button" tabindex="0">
                                <i class="bi bi-circle me-2"></i>Tidak Pernah
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Kadang-Kadang" data-color="info" role="button" tabindex="0">
                                <i class="bi bi-circle-half me-2"></i>Kadang-Kadang
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Sering" data-color="warning" role="button" tabindex="0">
                                <i class="bi bi-circle-fill me-2"></i>Sering
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Sering Sekali" data-color="danger" role="button" tabindex="0">
                                <i class="bi bi-record-circle me-2"></i>Sering Sekali
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="progress progress-pastel mb-4">
                        <div id="questionProgress" class="progress-bar progress-bar-pastel" style="width: 3.33%;"></div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="prevBtn" class="btn btn-pastel-secondary" style="visibility: hidden;">
                            <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">Pilih jawaban untuk melanjutkan</small>
                        </div>
                        
                        <button type="button" id="nextBtn" class="btn btn-pastel-secondary" disabled>
                            Selanjutnya<i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form for Final Submission -->
            <form id="dass21Form" method="POST" action="{{ route('quiz.dass21') }}" style="display: none;">
                @csrf
                <!-- Hidden inputs will be populated by JavaScript -->
            </form>

            <!-- Support Card -->
            <div class="card card-pastel mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-heart me-2"></i>
                                Dukungan Tersedia
                            </h6>
                            <p class="mb-0 small text-muted">
                                Tim konseling profesional kami siap memberikan dukungan kapan pun Anda membutuhkannya.
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-semibold text-primary">Hotline Crisis</div>
                            <div class="small text-muted">119 ext. 8</div>
                            <div class="small text-muted">24 jam</div>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-semibold text-primary">Konseling UM</div>
                            <div class="small text-muted">(0341) 551-312</div>
                            <div class="small text-muted">konseling@um.ac.id</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // DASS-21 Questions
    const questions = @json($dass21Questions);
    let currentQuestion = 0;
    let answers = {}; // Store answers by question index
    let startTime = null;
    let quizStarted = false;
    
    // Start button handler
    $('#startBtn').on('click', function() {
        startQuiz();
    });
    
    // Answer selection handler
    $('.answer-option').on('click', function() {
        selectAnswer($(this));
    });
    
    // Navigation handlers
    $('#nextBtn').on('click', nextQuestion);
    $('#prevBtn').on('click', prevQuestion);
    
    // Keyboard navigation
    $('.answer-option').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            selectAnswer($(this));
        }
    });
    
    function startQuiz() {
        $('#infoCard').fadeOut(300);
        $('#quizContainer').fadeIn(300);
        startTime = Date.now();
        quizStarted = true;
        
        displayCurrentQuestion();
        updateProgress();
        updateNavigation();
        updateTimeEstimate();
        
        // Start time estimation updates
        setInterval(updateTimeEstimate, 10000); // Update every 10 seconds
        
        trackUserInteraction('dass21_started');
    }
    
    function displayCurrentQuestion() {
        // Update question text
        $('#questionText').text(questions[currentQuestion]);
        $('#currentQuestionNumber').text(currentQuestion + 1);
        
        // Clear previous selections
        $('.answer-option').removeClass('selected');
        
        // Restore previous answer if exists
        if (answers[currentQuestion]) {
            $(`.answer-option[data-value="${answers[currentQuestion]}"]`).addClass('selected');
        }
        
        // Add entrance animation
        $('#questionContainer').addClass('fade-in');
        setTimeout(() => $('#questionContainer').removeClass('fade-in'), 600);
        
        // Track sensitive questions for additional support
        trackSensitiveQuestion();
    }
    
    function selectAnswer($option) {
        // Remove selection from siblings
        $('.answer-option').removeClass('selected');
        
        // Add selection to clicked option
        $option.addClass('selected');
        
        // Store answer
        const value = $option.data('value');
        answers[currentQuestion] = value;
        
        // Add pulse animation
        $option.addClass('pulse');
        setTimeout(() => $option.removeClass('pulse'), 600);
        
        // Enable next button
        updateNavigation();
        
        // Track high-risk responses
        trackQuestionResponse(value);
        
        // Auto-advance after 1.5 seconds for DASS-21 (slightly longer)
        setTimeout(() => {
            if (currentQuestion < questions.length - 1) {
                nextQuestion();
            } else {
                // Last question - enable submit
                $('#nextBtn').text('Selesaikan Skrining').removeClass('btn-pastel-secondary').addClass('btn-pastel-success');
            }
        }, 1500);
    }
    
    function nextQuestion() {
        if (currentQuestion < questions.length - 1) {
            currentQuestion++;
            displayCurrentQuestion();
            updateProgress();
            updateNavigation();
            updateTimeEstimate();
        } else {
            // Submit quiz
            submitQuiz();
        }
    }
    
    function prevQuestion() {
        if (currentQuestion > 0) {
            currentQuestion--;
            displayCurrentQuestion();
            updateProgress();
            updateNavigation();
        }
    }
    
    function updateProgress() {
        const progressPercent = ((currentQuestion + 1) / questions.length) * 100;
        $('#questionProgress').css('width', progressPercent + '%');
    }
    
    function updateNavigation() {
        // Update previous button
        if (currentQuestion > 0) {
            $('#prevBtn').css('visibility', 'visible');
        } else {
            $('#prevBtn').css('visibility', 'hidden');
        }
        
        // Update next button
        const hasAnswer = answers[currentQuestion] !== undefined;
        
        if (hasAnswer) {
            $('#nextBtn').prop('disabled', false);
            
            if (currentQuestion === questions.length - 1) {
                $('#nextBtn').text('Selesaikan Skrining').removeClass('btn-pastel-secondary').addClass('btn-pastel-success');
            } else {
                $('#nextBtn').text('Selanjutnya').removeClass('btn-pastel-success').addClass('btn-pastel-primary');
            }
        } else {
            $('#nextBtn').prop('disabled', true);
            $('#nextBtn').removeClass('btn-pastel-primary btn-pastel-success').addClass('btn-pastel-secondary');
            
            if (currentQuestion === questions.length - 1) {
                $('#nextBtn').text('Selesaikan Skrining');
            } else {
                $('#nextBtn').text('Selanjutnya');
            }
        }
    }
    
    function updateTimeEstimate() {
        if (!startTime || Object.keys(answers).length === 0) {
            $('#timeEstimate').text('-');
            return;
        }
        
        const elapsed = (Date.now() - startTime) / 1000 / 60; // minutes
        const answeredCount = Object.keys(answers).length;
        const avgTimePerQuestion = elapsed / answeredCount;
        const remainingQuestions = questions.length - (currentQuestion + 1);
        const estimatedTimeRemaining = Math.ceil(avgTimePerQuestion * remainingQuestions);
        
        $('#timeEstimate').text(estimatedTimeRemaining > 0 ? estimatedTimeRemaining : '< 1');
    }
    
    function trackSensitiveQuestion() {
        // Questions about self-harm, suicidal ideation, etc. (indices 18, 19, 29)
        const sensitiveQuestions = [18, 19, 29];
        
        if (sensitiveQuestions.includes(currentQuestion)) {
            trackUserInteraction('sensitive_question_viewed', {
                question_index: currentQuestion,
                question_type: 'high_sensitivity'
            });
        }
    }
    
    function trackQuestionResponse(value) {
        const sensitiveQuestions = [18, 19, 29]; // Self-harm, suicidal ideation questions
        
        if (sensitiveQuestions.includes(currentQuestion) && (value === 'Sering' || value === 'Sering Sekali')) {
            // Log for immediate follow-up (without storing actual response)
            trackUserInteraction('high_risk_response_detected', {
                question_category: 'critical',
                requires_immediate_followup: true,
                user_id: '{{ $quizResponse->id }}'
            });
        }
    }
    
    function submitQuiz() {
        // Check if all questions are answered
        const unansweredQuestions = [];
        for (let i = 0; i < questions.length; i++) {
            if (answers[i] === undefined) {
                unansweredQuestions.push(i + 1);
            }
        }
        
        if (unansweredQuestions.length > 0) {
            alert(`Mohon jawab pertanyaan nomor: ${unansweredQuestions.slice(0, 5).join(', ')}${unansweredQuestions.length > 5 ? ' dan lainnya' : ''}`);
            // Go to first unanswered question
            currentQuestion = unansweredQuestions[0] - 1;
            displayCurrentQuestion();
            updateProgress();
            updateNavigation();
            return;
        }
        
        // Confirmation for important assessment
        if (!confirm('Apakah Anda yakin semua jawaban sudah sesuai? Setelah mengirim, Anda tidak dapat mengubah jawaban.')) {
            return;
        }
        
        // Populate form with answers
        const $form = $('#dass21Form');
        $form.empty().append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        
        Object.keys(answers).forEach(function(questionIndex) {
            $form.append(`<input type="hidden" name="dass21[${questionIndex}]" value="${answers[questionIndex]}">`);
        });
        
        // Show loading and submit
        $('#nextBtn').html('<div class="spinner-border spinner-border-sm me-2"></div>Memproses...').prop('disabled', true);
        
        // Track completion
        trackUserInteraction('dass21_completed', {
            total_questions: questions.length,
            completion_time: startTime ? (Date.now() - startTime) / 1000 : 0
        });
        
        // Clear backup
        localStorage.removeItem('dass21_answers_backup');
        
        // Submit form
        $form.submit();
    }
    
    // Auto-backup answers every 10 seconds
    setInterval(function() {
        if (quizStarted) {
            localStorage.setItem('dass21_answers_backup', JSON.stringify({
                answers: answers,
                currentQuestion: currentQuestion,
                startTime: startTime,
                timestamp: Date.now()
            }));
        }
    }, 10000);
    
    // Try to restore from backup
    const backup = localStorage.getItem('dass21_answers_backup');
    if (backup) {
        try {
            const data = JSON.parse(backup);
            // Only restore if recent (within 2 hours)
            if (Date.now() - data.timestamp < 2 * 60 * 60 * 1000) {
                answers = data.answers || {};
                currentQuestion = data.currentQuestion || 0;
                startTime = data.startTime;
                
                if (Object.keys(answers).length > 0) {
                    // Auto-start if there's backup data
                    startQuiz();
                    showAlert('info', 'Progress sebelumnya telah dipulihkan. Lanjutkan dari pertanyaan terakhir.', 4000);
                }
            }
        } catch (e) {
            console.error('Error restoring backup:', e);
        }
    }
});

// Add required CSS
$('<style>')
.prop('type', 'text/css')
.html(`
    .answer-option {
        background: var(--calm-blue-light);
        border: 2px solid transparent;
        color: var(--text-primary);
        padding: 20px 24px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 500;
        font-size: 16px;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .answer-option[data-color="success"]:hover {
        background: var(--healing-green-light);
        transform: translateY(-3px);
    }
    
    .answer-option[data-color="info"]:hover {
        background: var(--calm-blue-light);
        transform: translateY(-3px);
    }
    
    .answer-option[data-color="warning"]:hover {
        background: var(--hopeful-yellow-light);
        transform: translateY(-3px);
    }
    
    .answer-option[data-color="danger"]:hover {
        background: #ffebee;
        transform: translateY(-3px);
    }
    
    .answer-option.selected {
        background: var(--primary-gradient);
        color: white;
        border-color: var(--compassion-purple);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(63, 81, 181, 0.4);
    }
    
    .pulse {
        animation: pulse 0.6s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: translateY(-3px) scale(1); }
        50% { transform: translateY(-3px) scale(1.05); }
        100% { transform: translateY(-3px) scale(1); }
    }
`)
.appendTo('head');
</script>
@endpush
@endsection