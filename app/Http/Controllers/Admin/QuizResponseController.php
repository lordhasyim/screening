<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuizResponseController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = QuizResponse::with(['faculty', 'department'])->latest('created_at');

        // Apply filters
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('risk_level')) {
            $query->where('overall_risk_level', $request->risk_level);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('quiz_status', 'completed');
            } elseif ($request->status === 'incomplete') {
                $query->where('quiz_status', '!=', 'completed');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $responses = $query->paginate(20);
        $faculties = Faculty::orderBy('name')->get();
        $stats = $this->getResponseStats();

        return view('admin.quiz-response.index', compact('responses', 'faculties', 'stats'));
    }

    public function show(QuizResponse $quizResponse)
    {
        $quizResponse->load(['faculty', 'department']);

        $scoring = [
            'phq9' => $this->getPhq9Scoring($quizResponse),
            'dass21' => $this->getDass21Scoring($quizResponse),
        ];

        return view('admin.quiz-response.show', compact('quizResponse', 'scoring'));
    }

    public function create()
    {
        $faculties = Faculty::with('departments')->orderBy('name')->get();
        return view('admin.quiz-response.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        // This would typically be used for manually creating quiz responses
        // But since this is an automated screening system, this might not be needed
        // Keeping it here for potential future use
        return redirect()->route('admin.quiz-response.index')
                        ->with('success', 'Quiz response created successfully');
    }

    public function edit(QuizResponse $quizResponse)
    {
        $quizResponse->load(['faculty', 'department']);
        $faculties = Faculty::with('departments')->orderBy('name')->get();

        return view('admin.quiz-response.edit', compact('quizResponse', 'faculties'));
    }

    public function update(Request $request, QuizResponse $quizResponse)
    {
        // Validate and update basic information only
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $quizResponse->update($validated);

        return redirect()->route('admin.quiz-response.show', $quizResponse)
                        ->with('success', 'Quiz response updated successfully');
    }

    private function getResponseStats()
    {
        return [
            'total' => QuizResponse::count(),
            'completed' => QuizResponse::completed()->count(),
            'high_risk' => QuizResponse::whereIn('overall_risk_level', ['High', 'Critical'])->count(),
            'today' => QuizResponse::whereDate('created_at', today())->count(),
        ];
    }

    private function getPhq9Scoring(QuizResponse $response)
    {
        if (! $response->phq9_responses) {
            return null;
        }

        $responses = $response->phq9_responses;
        $questions = [
            'Kurang tertarik atau bergairah dalam melakukan apapun',
            'Merasa sedih, murung, kesepian, atau putus asa',
            'Sulit tidur atau mudah terbangun, atau terlalu banyak tidur',
            'Merasa lelah atau kurang bertenaga',
            'Kurang nafsu makan atau terlalu banyak makan',
            'Mudah merasa cemas dan gelisah pada situasi tertentu',
            'Sulit mempertahankan konsentrasi saat berkegiatan',
            'Merasa hidup tidak berarti, tidak berharga, tidak layak, atau tidak berguna',
            'Merasa tidak mendapatkan dukungan sosial dari lingkungan',
        ];

        $scoring = [];
        $totalScore = 0;

        foreach ($responses as $index => $answer) {
            $score = $this->convertAnswerToScore($answer);
            $totalScore += $score;

            $scoring[] = [
                'question' => $questions[$index] ?? 'Pertanyaan '.($index + 1),
                'answer' => $answer,
                'score' => $score,
            ];
        }

        return [
            'questions' => $scoring,
            'total_score' => $totalScore,
            'max_score' => 27,
            'category' => $response->phq9_category,
            'interpretation' => getPhq9InterpretationText($response->phq9_category),
        ];
    }

    private function getDass21Scoring(QuizResponse $response)
    {
        if (! $response->dass21_responses) {
            return null;
        }

        $responses = $response->dass21_responses;
        $questions = [
            'Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele',
            'Saya sama sekali tidak dapat merasakan perasaan positif',
            'Saya mengalami kesulitan bernafas',
            'Saya sepertinya tidak kuat lagi untuk melakukan suatu kegiatan',
            'Saya cenderung bereaksi berlebihan terhadap suatu situasi',
            'Saya merasa gemetar (misalnya: pada tangan)',
            'Saya merasa telah menghabiskan banyak energi disaat merasa cemas',
            'Saya merasa khawatir dengan situasi dimana saya mungkin menjadi panik',
            'Saya merasa tidak ada hal yang dapat diharapkan di masa depan',
            'Saya mudah merasa gelisah',
            'Saya merasa sulit untuk bersantai',
            'Saya tidak merasa antusias dalam hal apapun',
            'Saya merasa bahwa saya tidak berharga sebagai seorang manusia',
            'Saya merasa bahwa saya mudah tersinggung',
            'Saya menyadari perubahan detak jantung tanpa melakukan aktivitas fisik',
            'Saya merasa takut tanpa alasan yang jelas',
            'Saya mengalami perubahan suasana hati secara tiba-tiba',
            'Saya masih merasa sangat sedih setelah kehilangan orang yang saya cintai',
            'Saya memiliki keinginan untuk menyakiti diri sendiri',
            'Saya pernah mengalami keluhan di tubuh yang muncul saat banyak pikiran',
            'Saya merasa sulit mengendalikan emosi',
            'Saya merasa tidak stabil secara emosional',
            'Saya pernah mengalami kejadian traumatik di masa lalu',
            'Saya memiliki konflik berkepanjangan dengan orang lain',
            'Saya kesulitan mempertahankan relasi dengan teman atau pasangan',
            'Saya memiliki perasaan hampa yang sudah lama dirasakan',
            'Saya sering merasa curiga dengan orang lain',
            'Saya melakukan banyak usaha agar tidak diabaikan oleh orang lain',
            'Saya memiliki perilaku impulsif yang berbahaya',
            'Saya pernah memiliki pemikiran untuk mengakhiri hidup',
        ];

        $scoring = [];
        $totalScore = 0;

        foreach ($responses as $index => $answer) {
            $score = $this->convertAnswerToScore($answer);
            $totalScore += $score;

            $scoring[] = [
                'question' => $questions[$index] ?? 'Pertanyaan '.($index + 1),
                'answer' => $answer,
                'score' => $score,
            ];
        }

        return [
            'questions' => $scoring,
            'total_score' => $totalScore,
            'max_score' => 90,
            'category' => $response->dass21_category,
            'interpretation' => getDass21InterpretationText($response->dass21_category),
        ];
    }

    private function convertAnswerToScore($answer)
    {
        $scoreMap = [
            'Tidak Pernah' => 0,
            'Kadang-Kadang' => 1,
            'Sering' => 2,
            'Sering Sekali' => 3,
        ];

        return $scoreMap[$answer] ?? 0;
    }

    private function getCategoryBadgeColor($category)
    {
        return match (strtolower($category)) {
            'sangat rendah' => 'success',
            'rendah' => 'info',
            'sedang' => 'warning',
            'tinggi' => 'danger',
            'sangat tinggi' => 'dark',
            default => 'secondary'
        };
    }

    public function destroy(QuizResponse $quizResponse)
    {
        $quizResponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:quiz_responses,id'
        ]);

        $ids = $request->input('ids');
        $count = QuizResponse::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' data berhasil dihapus',
        ]);
    }

    public function export(Request $request)
    {
        $query = QuizResponse::with(['faculty', 'department']);

        // Apply same filters as index
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('risk_level')) {
            $query->where('overall_risk_level', $request->risk_level);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->where('quiz_status', 'completed');
            } elseif ($request->status === 'incomplete') {
                $query->where('quiz_status', '!=', 'completed');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $responses = $query->get();

        return $this->exportToCsv($responses);
    }

    public function flagForFollowup(QuizResponse $quizResponse)
    {
        // Add a flag or note for follow-up actions
        // This could be implemented by adding a 'flagged_for_followup' column to the quiz_responses table

        return response()->json([
            'success' => true,
            'message' => 'Response flagged for follow-up',
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total' => QuizResponse::count(),
            'completed' => QuizResponse::where('quiz_status', 'completed')->count(),
            'high_risk' => QuizResponse::whereIn('overall_risk_level', ['High', 'Critical'])->count(),
            'today' => QuizResponse::whereDate('created_at', today())->count(),
            'this_week' => QuizResponse::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'this_month' => QuizResponse::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }

    private function exportToCsv($responses)
    {
        $filename = 'quiz_responses_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($responses) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID', 'NIM', 'Nama', 'Gender', 'Fakultas', 'Jurusan', 'Tahun',
                'PHQ9 Score', 'PHQ9 Kategori', 'DASS21 Score', 'DASS21 Kategori',
                'Risk Level', 'Status', 'Tanggal Mulai', 'Tanggal Selesai'
            ]);

            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->id,
                    $response->nim,
                    $response->full_name,
                    $response->gender,
                    $response->faculty->name ?? 'N/A',
                    $response->department->name ?? 'N/A',
                    $response->student_year,
                    $response->phq9_total_score ?? 'N/A',
                    $response->phq9_category ?? 'N/A',
                    $response->dass21_total_score ?? 'N/A',
                    $response->dass21_category ?? 'N/A',
                    $response->overall_risk_level ?? 'N/A',
                    $response->quiz_status,
                    $response->started_at ? $response->started_at->format('Y-m-d H:i:s') : 'N/A',
                    $response->completed_at ? $response->completed_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
