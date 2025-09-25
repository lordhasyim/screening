<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\QuizResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:admin');
    }

    public function index()
    {
        $stats = $this->getDashboardStats();
        $charts = $this->getChartData();
        $recentActivity = $this->getRecentActivity();
        $alerts = $this->getSystemAlerts();

        return view('admin.dashboard.index', compact('stats', 'charts', 'recentActivity', 'alerts'));
    }

    private function getDashboardStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            // Total Statistics
            'total_responses' => QuizResponse::count(),
            'completed_responses' => QuizResponse::completed()->count(),
            'completion_rate' => $this->getCompletionRate(),

            // Time-based Statistics
            'today_responses' => QuizResponse::whereDate('created_at', $today)->count(),
            'week_responses' => QuizResponse::where('created_at', '>=', $thisWeek)->count(),
            'month_responses' => QuizResponse::where('created_at', '>=', $thisMonth)->count(),

            // Risk Level Statistics
            'high_risk_count' => QuizResponse::whereIn('overall_risk_level', ['High', 'Critical'])->count(),
            'moderate_risk_count' => QuizResponse::where('overall_risk_level', 'Moderate')->count(),
            'low_risk_count' => QuizResponse::where('overall_risk_level', 'Low')->count(),

            // Assessment Statistics
            'phq9_completed' => QuizResponse::whereNotNull('phq9_responses')->count(),
            'dass21_completed' => QuizResponse::whereNotNull('dass21_responses')->count(),
            'dass21_percentage' => $this->getDass21Percentage(),

            // Faculty Coverage
            'active_faculties' => Faculty::whereHas('quizResponses')->count(),
            'total_faculties' => Faculty::count(),

            // Average Scores
            'avg_phq9_score' => QuizResponse::whereNotNull('phq9_total_score')->avg('phq9_total_score'),
            'avg_dass21_score' => QuizResponse::whereNotNull('dass21_total_score')->avg('dass21_total_score'),
        ];
    }

    private function getChartData()
    {
        return [
            'phq9_distribution' => $this->getPhq9Distribution(),
            'dass21_distribution' => $this->getDass21Distribution(),
            'risk_distribution' => $this->getRiskDistribution(),
            'monthly_trends' => $this->getMonthlyTrends(),
            'faculty_distribution' => $this->getFacultyDistribution(),
        ];
    }

    private function getRecentActivity()
    {
        $recentResponses = QuizResponse::with(['faculty', 'department'])
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(function ($response) {
                return [
                    'id' => $response->id,
                    'name' => $response->full_name,
                    'faculty' => $response->faculty->name ?? 'N/A',
                    'risk_level' => $response->overall_risk_level,
                    'completed_at' => $response->completed_at,
                    'phq9_category' => $response->phq9_category,
                    'dass21_category' => $response->dass21_category,
                ];
            });

        return $recentResponses;
    }

    private function getSystemAlerts()
    {
        $alerts = [];

        // High Risk Alerts
        $criticalCount = QuizResponse::where('overall_risk_level', 'Critical')->count();
        if ($criticalCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Responden Risiko Kritis',
                'message' => "{$criticalCount} responden memerlukan tindak lanjut segera",
                'action_url' => route('admin.responses', ['filter' => 'critical']),
                'action_text' => 'Lihat Detail',
            ];
        }

        $highRiskCount = QuizResponse::where('overall_risk_level', 'High')->count();
        if ($highRiskCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-circle',
                'title' => 'Responden Risiko Tinggi',
                'message' => "{$highRiskCount} responden memerlukan perhatian",
                'action_url' => route('admin.responses', ['filter' => 'high']),
                'action_text' => 'Tinjau',
            ];
        }

        // Daily Target Alert
        $todayTarget = 50; // Configurable target
        $todayCount = QuizResponse::whereDate('created_at', Carbon::today())->count();
        if ($todayCount >= $todayTarget) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'fas fa-check-circle',
                'title' => 'Target Harian Tercapai',
                'message' => "Target {$todayTarget} responden hari ini telah tercapai ({$todayCount})",
                'action_url' => null,
                'action_text' => null,
            ];
        }

        return $alerts;
    }

    private function getCompletionRate()
    {
        $started = QuizResponse::count();
        $completed = QuizResponse::completed()->count();

        return $started > 0 ? round(($completed / $started) * 100, 1) : 0;
    }

    private function getDass21Percentage()
    {
        $phq9Completed = QuizResponse::whereNotNull('phq9_responses')->count();
        $dass21Completed = QuizResponse::whereNotNull('dass21_responses')->count();

        return $phq9Completed > 0 ? round(($dass21Completed / $phq9Completed) * 100, 1) : 0;
    }

    private function getPhq9Distribution()
    {
        return QuizResponse::whereNotNull('phq9_category')
            ->groupBy('phq9_category')
            ->select('phq9_category', DB::raw('count(*) as count'))
            ->pluck('count', 'phq9_category')
            ->toArray();
    }

    private function getDass21Distribution()
    {
        return QuizResponse::whereNotNull('dass21_category')
            ->groupBy('dass21_category')
            ->select('dass21_category', DB::raw('count(*) as count'))
            ->pluck('count', 'dass21_category')
            ->toArray();
    }

    private function getRiskDistribution()
    {
        return QuizResponse::whereNotNull('overall_risk_level')
            ->groupBy('overall_risk_level')
            ->select('overall_risk_level', DB::raw('count(*) as count'))
            ->pluck('count', 'overall_risk_level')
            ->toArray();
    }

    private function getMonthlyTrends()
    {
        return QuizResponse::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->mapWithKeys(function ($count, $month) {
                $monthName = Carbon::createFromFormat('Y-m', $month)->format('M Y');

                return [$monthName => $count];
            })
            ->toArray();
    }

    private function getFacultyDistribution()
    {
        return Faculty::withCount('quizResponses')
            ->having('quiz_responses_count', '>', 0)
            ->orderBy('quiz_responses_count', 'desc')
            ->limit(10)
            ->pluck('quiz_responses_count', 'name')
            ->toArray();
    }

    // API Endpoints for AJAX requests
    public function getStats()
    {
        return response()->json($this->getDashboardStats());
    }

    // public function getChartData()
    // {
    //     return response()->json($this->getChartData());
    // }

    public function checkHighRisk()
    {
        $highRiskCount = QuizResponse::whereIn('overall_risk_level', ['High', 'Critical'])->count();

        return response()->json([
            'highRiskCount' => $highRiskCount,
            'hasHighRisk' => $highRiskCount > 0,
        ]);
    }

    public function checkNewResponses()
    {
        $lastCheck = session('last_response_check', Carbon::now()->subMinutes(5));
        $newCount = QuizResponse::where('created_at', '>', $lastCheck)->count();

        session(['last_response_check' => Carbon::now()]);

        return response()->json([
            'newCount' => $newCount,
            'hasNew' => $newCount > 0,
        ]);
    }

    public function responses(Request $request)
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
            $query->where('quiz_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $responses = $query->paginate(20);
        $faculties = Faculty::orderBy('name')->get();

        return view('admin.dashboard.responses', compact('responses', 'faculties'));
    }

    public function showResponse(QuizResponse $quizResponse)
    {
        $quizResponse->load(['faculty', 'department']);

        return view('admin.dashboard.response-detail', compact('quizResponse'));
    }

    public function deleteResponse(QuizResponse $quizResponse)
    {
        $quizResponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Respons berhasil dihapus',
        ]);
    }

    public function analytics()
    {
        $stats = $this->getDashboardStats();
        $charts = $this->getChartData();

        // Additional analytics data
        $analyticsData = [
            'demographic_breakdown' => $this->getDemographicBreakdown(),
            'risk_trends' => $this->getRiskTrends(),
            'completion_funnel' => $this->getCompletionFunnel(),
            'faculty_performance' => $this->getFacultyPerformance(),
        ];

        return view('admin.dashboard.analytics', compact('stats', 'charts', 'analyticsData'));
    }

    public function reports()
    {
        $reportData = [
            'summary_stats' => $this->getDashboardStats(),
            'risk_distribution' => $this->getRiskDistribution(),
            'faculty_breakdown' => $this->getFacultyDistribution(),
            'monthly_trends' => $this->getMonthlyTrends(),
        ];

        return view('admin.dashboard.reports', compact('reportData'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $query = QuizResponse::with(['faculty', 'department']);

        // Apply filters if provided
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('risk_level')) {
            $query->where('overall_risk_level', $request->risk_level);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $responses = $query->get();

        if ($format === 'excel') {
            return $this->exportToExcel($responses);
        } elseif ($format === 'csv') {
            return $this->exportToCsv($responses);
        }

        return redirect()->back()->with('error', 'Format ekspor tidak didukung');
    }

    public function settings()
    {
        // For now, we'll just show basic settings
        $settings = [
            'daily_target' => 50,
            'risk_threshold_high' => 10,
            'risk_threshold_critical' => 5,
            'auto_notifications' => true,
        ];

        return view('admin.dashboard.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'daily_target' => 'required|integer|min:1',
            'risk_threshold_high' => 'required|integer|min:1',
            'risk_threshold_critical' => 'required|integer|min:1',
            'auto_notifications' => 'boolean',
        ]);

        // For now, we'll just store in session/cache
        // In a real app, you'd store in a settings table
        session()->put('app_settings', $request->only([
            'daily_target',
            'risk_threshold_high',
            'risk_threshold_critical',
            'auto_notifications',
        ]));

        return redirect()->route('admin.settings')
            ->with('success', 'Pengaturan berhasil disimpan');
    }

    // Additional helper methods for analytics
    private function getDemographicBreakdown()
    {
        return [
            'by_gender' => QuizResponse::select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray(),

            'by_year' => QuizResponse::select('student_year', DB::raw('count(*) as count'))
                ->groupBy('student_year')
                ->orderBy('student_year')
                ->pluck('count', 'student_year')
                ->toArray(),

            'by_living_arrangement' => QuizResponse::select('living_arrangement', DB::raw('count(*) as count'))
                ->groupBy('living_arrangement')
                ->pluck('count', 'living_arrangement')
                ->toArray(),
        ];
    }

    private function getRiskTrends()
    {
        return QuizResponse::selectRaw('DATE(created_at) as date, overall_risk_level, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date', 'overall_risk_level')
            ->orderBy('date')
            ->get()
            ->groupBy('date');
    }

    private function getCompletionFunnel()
    {
        return [
            'started' => QuizResponse::count(),
            'phq9_completed' => QuizResponse::whereNotNull('phq9_completed_at')->count(),
            'dass21_completed' => QuizResponse::whereNotNull('dass21_completed_at')->count(),
            'fully_completed' => QuizResponse::where('quiz_status', 'completed')->count(),
        ];
    }

    private function getFacultyPerformance()
    {
        return Faculty::withCount([
            'quizResponses',
            'quizResponses as high_risk_count' => function ($query) {
                $query->whereIn('overall_risk_level', ['High', 'Critical']);
            },
            'quizResponses as completed_count' => function ($query) {
                $query->where('quiz_status', 'completed');
            },
        ])->having('quiz_responses_count', '>', 0)
            ->orderBy('quiz_responses_count', 'desc')
            ->get();
    }

    private function exportToExcel($responses)
    {
        $filename = 'quiz_responses_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($responses) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID', 'NIM', 'Nama', 'Gender', 'Fakultas', 'Jurusan', 'Tahun',
                'PHQ9 Score', 'PHQ9 Kategori', 'DASS21 Score', 'DASS21 Kategori',
                'Risk Level', 'Status', 'Tanggal Mulai', 'Tanggal Selesai',
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

    private function exportToCsv($responses)
    {
        return $this->exportToExcel($responses); // Same implementation for now
    }
}
