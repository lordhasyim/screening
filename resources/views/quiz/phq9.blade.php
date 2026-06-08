@extends('layouts.app')

@section('title', 'Skrining PHQ-9 - Kesehatan Mental ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Step Navigation -->
            <div class="step-nav fade-in">
                <div class="step-indicator">
                    <div class="d-flex align-items-center">
                        <div class="step-number">2</div>
                        <div class="ms-3">
                            <div class="step-title">Skrining Kesehatan Mental - Tahap I</div>
                            <small class="text-muted">Jawab setiap pertanyaan sesuai kondisi Anda dalam 30 hari terakhir</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Langkah 2 dari 4</small>
                    </div>
                </div>
                <div class="progress progress-pastel mt-3">
                    <div class="progress-bar progress-bar-pastel" style="width: 50%;"></div>
                </div>
            </div>

            <!-- Quiz Container -->
            <div class="card card-pastel slide-up" style="min-height: 500px;">
                <div class="card-body d-flex flex-column justify-content-center p-3 p-md-5">
                    
                    <!-- Question Header -->
                    <div class="text-center mb-4">
                        <h5 class="text-primary mb-2">Silahkan Jawab Pertanyaan ini</h5>
                        <div class="small text-muted">
                            Pertanyaan <span id="currentQuestionNumber">1</span> dari {{ count($phq9Questions) }}
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
                            <div class="answer-option" data-value="Tidak Pernah" role="button" tabindex="0">
                                😐 Tidak Pernah
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Kadang-Kadang" role="button" tabindex="0">
                                😊 Kadang-Kadang
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Sering" role="button" tabindex="0">
                                😔 Sering
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-value="Sering Sekali" role="button" tabindex="0">
                                😰 Sering Sekali
                            </div>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="progress progress-pastel mb-4">
                        <div id="questionProgress" class="progress-bar progress-bar-pastel" style="width: 11%;"></div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="prevBtn" class="btn btn-pastel-secondary flex-shrink-0" style="visibility: hidden;">
                            <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                        </button>

                        <div class="text-center d-none d-sm-block mx-2">
                            <small class="text-muted">Pilih jawaban untuk melanjutkan</small>
                        </div>

                        <button type="button" id="nextBtn" class="btn btn-pastel-secondary flex-shrink-0" disabled>
                            Selanjutnya<i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form for Final Submission -->
            <form id="phq9Form" method="POST" action="{{ route('quiz.phq9') }}" style="display: none;">
                @csrf
                <!-- Hidden inputs will be populated by JavaScript -->
            </form>

            <!-- Help Card -->
            <div class="card card-pastel mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-question-circle me-2"></i>
                                Butuh bantuan?
                            </h6>
                            <p class="mb-0 small text-muted">
                                Jika Anda merasa kesulitan dengan pertanyaan-pertanyaan ini, 
                                tim konseling kami siap membantu.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="fw-bold text-primary">Hotline 24/7</div>
                            <div class="small text-muted">(0341) 551-312</div>
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
    // PHQ-9 Questions
    const questions = @json($phq9Questions);
    let currentQuestion = 0;
    let answers = {}; // Store answers by question index
    
    // Initialize quiz
    initializeQuiz();
    
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
    
    function initializeQuiz() {
        displayCurrentQuestion();
        updateProgress();
        updateNavigation();
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
        
        // Auto-advance after 1 second (optional)
        setTimeout(() => {
            if (currentQuestion < questions.length - 1) {
                nextQuestion();
            } else {
                // Last question - enable submit
                $('#nextBtn').text('Selesai').removeClass('btn-pastel-secondary').addClass('btn-pastel-success');
            }
        }, 1000);
    }
    
    function nextQuestion() {
        if (currentQuestion < questions.length - 1) {
            currentQuestion++;
            displayCurrentQuestion();
            updateProgress();
            updateNavigation();
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
                $('#nextBtn').text('Selesai').removeClass('btn-pastel-secondary').addClass('btn-pastel-success');
            } else {
                $('#nextBtn').text('Selanjutnya').removeClass('btn-pastel-success').addClass('btn-pastel-primary');
            }
        } else {
            $('#nextBtn').prop('disabled', true);
            $('#nextBtn').removeClass('btn-pastel-primary btn-pastel-success').addClass('btn-pastel-secondary');
            
            if (currentQuestion === questions.length - 1) {
                $('#nextBtn').text('Selesai');
            } else {
                $('#nextBtn').text('Selanjutnya');
            }
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
            alert(`Mohon jawab pertanyaan nomor: ${unansweredQuestions.join(', ')}`);
            // Go to first unanswered question
            currentQuestion = unansweredQuestions[0] - 1;
            displayCurrentQuestion();
            updateProgress();
            updateNavigation();
            return;
        }
        
        // Populate form with answers
        const $form = $('#phq9Form');
        $form.empty().append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        
        Object.keys(answers).forEach(function(questionIndex) {
            $form.append(`<input type="hidden" name="phq9[${questionIndex}]" value="${answers[questionIndex]}">`);
        });
        
        // Show loading and submit
        $('#nextBtn').html('<div class="spinner-border spinner-border-sm me-2"></div>Memproses...').prop('disabled', true);
        
        // Track completion
        trackUserInteraction('phq9_completed', {
            total_questions: questions.length,
            completion_time: getCompletionTime()
        });
        
        // Submit form
        $form.submit();
    }
    
    function getCompletionTime() {
        const startTime = sessionStorage.getItem('phq9_start_time');
        if (startTime) {
            return (Date.now() - parseInt(startTime)) / 1000;
        }
        return 0;
    }
    
    // Track start time
    if (!sessionStorage.getItem('phq9_start_time')) {
        sessionStorage.setItem('phq9_start_time', Date.now().toString());
    }
    
    // Auto-backup answers
    setInterval(function() {
        localStorage.setItem('phq9_answers_backup', JSON.stringify({
            answers: answers,
            currentQuestion: currentQuestion,
            timestamp: Date.now()
        }));
    }, 5000);
    
    // Try to restore from backup
    const backup = localStorage.getItem('phq9_answers_backup');
    if (backup) {
        try {
            const data = JSON.parse(backup);
            // Only restore if recent (within 1 hour)
            if (Date.now() - data.timestamp < 60 * 60 * 1000) {
                answers = data.answers || {};
                currentQuestion = data.currentQuestion || 0;
                displayCurrentQuestion();
                updateProgress();
                updateNavigation();
                
                if (Object.keys(answers).length > 0) {
                    showAlert('info', 'Progress sebelumnya telah dipulihkan', 3000);
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
    
    .answer-option:hover {
        background: var(--compassion-purple-light);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(156, 39, 176, 0.2);
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