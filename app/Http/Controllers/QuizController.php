<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    // Show welcome/landing page
    public function index()
    {
        $faculties = Faculty::with('departments')->orderBy('name')->get();
        return view('quiz.index', compact('faculties'));
    }

    // Show identity form (first step)
    public function identity()
    {
        $faculties = Faculty::with('departments')->orderBy('name')->get();
        return view('quiz.identity', compact('faculties'));
    }

    // Process identity and redirect to PHQ-9
    public function submitIdentity(Request $request)
    {
        $validated = $request->validate([
            'student_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'nim' => 'required|string|max:50|unique:quiz_responses,nim',
            'full_name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'living_arrangement' => ['required', Rule::in(['Kos', 'Rumah orang tua', 'Rumah keluarga', 'Asrama', 'Kontrak'])],
            'origin_province' => 'required|string',
            'origin_area_type' => ['required', Rule::in(['perkotaan', 'pedesaan', 'pinggiran kota', 'daerah terpencil', 'daerah industri'])],
            'email' => 'nullable|email|max:255',
            'religion' => 'required|string',
            'parents_marital_status' => ['required', Rule::in(['menikah', 'cerai hidup', 'cerai mati', 'pisah tidak resmi', 'menikah lagi'])],
            'child_order' => 'required|integer|min:1',
            'siblings_count' => 'required|integer|min:1',
            'scholarship' => 'nullable|string',
            'admission_path' => 'required|string',
            'parents_education' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'])],
            'parents_income' => ['required', Rule::in(['<2000000', '2000000-5000000', '5000000-10000000', '>10000000'])],
            'family_members_count' => 'required|integer|min:1',

            // Medical History
            'has_chronic_disease' => 'boolean',
            'chronic_disease_details' => 'nullable|string',
            'current_medication' => 'boolean', 
            'medication_details' => 'nullable|string',
            'head_injury_history' => 'boolean',
            'injury_details' => 'nullable|string',
            'substance_use' => ['required', Rule::in(['Tidak Pernah', 'Pernah', 'Masih aktif'])],
            'substance_details' => 'nullable|string',
            'psychological_treatment_history' => 'boolean',
            'treatment_details' => 'nullable|string',
            'family_mental_health_history' => 'boolean',
            'family_history_details' => 'nullable|string',
            'family_relationship_description' => 'nullable|string',
        ]);

        // Create quiz response with identity data
        $quizResponse = QuizResponse::create([
            ...$validated,
            'quiz_status' => 'started',
            'started_at' => now(),
        ]);

        // Store quiz ID in session and redirect to PHQ-9
        session(['quiz_response_id' => $quizResponse->id]);
        
        return redirect()->route('quiz.phq9')
                         ->with('success', 'Identitas berhasil disimpan. Lanjut ke pertanyaan skrining.');
    }

    // Show PHQ-9 questions
    public function phq9()
    {
        $quizResponseId = session('quiz_response_id');
        if (!$quizResponseId) {
            return redirect()->route('quiz.identity')
                           ->with('error', 'Silakan isi identitas terlebih dahulu.');
        }

        $quizResponse = QuizResponse::findOrFail($quizResponseId);
        $phq9Questions = $this->getPhq9Questions();

        return view('quiz.phq9', compact('quizResponse', 'phq9Questions'));
    }

    // Process PHQ-9 responses
    public function submitPhq9(Request $request)
    {
        $quizResponseId = session('quiz_response_id');
        $quizResponse = QuizResponse::findOrFail($quizResponseId);

        // Validate PHQ-9 responses
        $request->validate([
            'phq9' => 'required|array|size:9',
            'phq9.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
        ], [
            'phq9.required' => 'Semua pertanyaan PHQ-9 harus dijawab.',
            'phq9.size' => 'Harus ada tepat 9 jawaban PHQ-9.',
        ]);

        // Update quiz response with PHQ-9 data
        $quizResponse->update([
            'phq9_responses' => $request->phq9,
            'quiz_status' => 'phq9_completed',
            'phq9_completed_at' => now(),
        ]);

        // Check if should continue to DASS-21
        if ($quizResponse->shouldContinueToDass21()) {
            return redirect()->route('quiz.dass21')
                           ->with('info', 'Berdasarkan hasil PHQ-9, Anda perlu melanjutkan ke tahap berikutnya.');
        }

        // Stop here - show result without DASS-21
        $quizResponse->update([
            'quiz_status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('quiz.result', $quizResponse->id)
                         ->with('success', 'Skrining selesai!');
    }

    // Show DASS-21 questions
    public function dass21()
    {
        $quizResponseId = session('quiz_response_id');
        if (!$quizResponseId) {
            return redirect()->route('quiz.identity')
                           ->with('error', 'Silakan mulai dari awal.');
        }

        $quizResponse = QuizResponse::findOrFail($quizResponseId);
        
        // Check if they should be here
        if (!$quizResponse->shouldContinueToDass21()) {
            return redirect()->route('quiz.result', $quizResponse->id)
                           ->with('info', 'Anda tidak perlu melanjutkan ke DASS-21.');
        }

        $dass21Questions = $this->getDass21Questions();

        return view('quiz.dass21', compact('quizResponse', 'dass21Questions'));
    }

    // Process DASS-21 responses
    public function submitDass21(Request $request)
    {
        $quizResponseId = session('quiz_response_id');
        $quizResponse = QuizResponse::findOrFail($quizResponseId);

        // Validate DASS-21 responses
        $request->validate([
            'dass21' => 'required|array|size:30',
            'dass21.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
        ], [
            'dass21.required' => 'Semua pertanyaan DASS-21 harus dijawab.',
            'dass21.size' => 'Harus ada tepat 30 jawaban DASS-21.',
        ]);

        // Update quiz response with DASS-21 data and mark complete
        $quizResponse->update([
            'dass21_responses' => $request->dass21,
            'quiz_status' => 'completed',
            'dass21_completed_at' => now(),
            'completed_at' => now(),
        ]);

        return redirect()->route('quiz.result', $quizResponse->id)
                         ->with('success', 'Skrining lengkap selesai!');
    }

    // Show result
    public function result(QuizResponse $quizResponse)
    {
        // Clear session
        session()->forget('quiz_response_id');

        return view('quiz.result', compact('quizResponse'));
    }

    // Get PHQ-9 questions
    private function getPhq9Questions()
    {
        return [
            "Kurang tertarik atau bergairah dalam melakukan apapun",
            "Merasa sedih, murung, kesepian, atau putus asa",
            "Sulit tidur atau mudah terbangun, atau terlalu banyak tidur",
            "Merasa lelah atau kurang bertenaga",
            "Kurang nafsu makan atau terlalu banyak makan",
            "Mudah merasa cemas dan gelisah pada situasi tertentu",
            "Sulit mempertahankan konsentrasi saat berkegiatan",
            "Merasa hidup tidak berarti, tidak berharga, tidak layak, atau tidak berguna",
            "Merasa tidak mendapatkan dukungan sosial dari lingkungan (Orang tua, teman, pasangan atau lainnya)"
        ];
    }

    // Get DASS-21 Extended questions (30 questions)
    private function getDass21Questions()
    {
        return [
            "Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele",
            "Saya sama sekali tidak dapat merasakan perasaan positif",
            "Saya mengalami kesulitan bernafas (misalnya: sering kali terengah-engah atau tidak dapat bernafas padahal tidak melakukan aktivitas fisik sebelumnya)",
            "Saya sepertinya tidak kuat lagi untuk melakukan suatu kegiatan.",
            "Saya cenderung bereaksi berlebihan terhadap suatu situasi.",
            "Saya merasa gemetar (misalnya: pada tangan)",
            "Saya merasa telah menghabiskan banyak energi disaat merasa cemas.",
            "Saya merasa khawatir dengan situasi dimana saya mungkin menjadi panik dan mempermalukan diri sendiri.",
            "Saya merasa tidak ada hal yang dapat diharapkan di masa depan",
            "Saya mudah merasa gelisah",
            "Saya merasa sulit untuk bersantai",
            "Saya tidak merasa antusias dalam hal apapun.",
            "Saya merasa bahwa saya tidak berharga sebagai seorang manusia",
            "Saya merasa bahwa saya mudah tersinggung",
            "Saya menyadari perubahan detak jantung, walaupun tidak sehabis melakukan aktivitas fisik (misalnya: merasa detak jantung meningkat atau melemah).",
            "Saya merasa takut tanpa alasan yang jelas",
            "Saya mengalami perubahan suasana hati secara tiba-tiba tanpa alasan yang jelas",
            "Saya masih merasa sangat sedih atau sulit menerima kenyataan setelah kehilangan orang yang saya cintai (karena kematian, perceraian, atau perpisahan)",
            "Saya memiliki keinginan untuk menyakiti diri sendiri saat merasa sangat sedih atau tertekan",
            "Saya pernah mengalami keluhan di tubuh yang muncul saat banyak pikiran",
            "Saya merasa sulit mengendalikan emosi, seperti mudah marah, tersinggung, atau menangis tanpa alasan yang jelas",
            "Saya merasa tidak stabil secara emosional dan sulit menenangkan diri saat menghadapi masalah kecil",
            "Saya pernah mengalami suatu kejadian traumatik di masa lalu yang membuat saya masih terbayang kejadian tersebut sampai saat ini (Misalnya pelecehan, kekerasan fisik, kekerasan verbal, dll.)",
            "Saya memiliki konflik berkepanjangan dengan teman, keluarga, ataupun pasangan",
            "Saya kesulitan dalam mempertahankan relasi dengan teman ataupun pasangan",
            "Saya memiliki perasaan hampa yang sudah lama dirasakan",
            "Saya sering merasa curiga dengan orang lain",
            "Saya melakukan banyak usaha agar tidak diabaikan oleh orang lain lain yang signifikan bagi saya",
            "Saya memiliki perilaku impulsif seperti perilaku seksual berbahaya, pemakaian zat adiktif, kebut-kebutan, atau makan dalam jumlah sangat banyak",
            "Saya pernah memiliki pemikiran untuk mengakhiri hidup"
        ];
    }

    // API endpoint to get departments by faculty
    public function getDepartments($facultyId)
    {
        $departments = Department::where('faculty_id', $facultyId)
                                ->orderBy('name')
                                ->get(['id', 'name']);
        
        return response()->json($departments);
    }
}