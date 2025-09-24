<?php

namespace App\Http\Controllers;

use App\Models\QuizResponse;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PublicStatsController extends Controller
{
    public function index()
    {
        // Get all completed responses
        $completedResponses = QuizResponse::completed()->get();
        $totalResponses = $completedResponses->count();
        
        if ($totalResponses === 0) {
            return view('stats.index', $this->getEmptyStats());
        }
        
        // Calculate basic statistics
        $stats = [
            'totalResponses' => $totalResponses,
            'facultyCount' => Faculty::count(),
            'monthsActive' => $this->getMonthsActive(),
            'completionRate' => $this->getCompletionRate(),
            'dass21Responses' => $completedResponses->whereNotNull('dass21_responses')->count(),
            
            // Distribution data for charts
            'phq9Distribution' => $this->getPhq9Distribution($completedResponses),
            'dass21Distribution' => $this->getDass21Distribution($completedResponses),
            'facultyDistribution' => $this->getFacultyDistribution($completedResponses),
            'monthlyTrends' => $this->getMonthlyTrends(),
            'riskDistribution' => $this->getRiskDistribution($completedResponses),
            
            // Additional insights
            'positiveOutcomes' => $this->getPositiveOutcomes($completedResponses),
            'avgAge' => $this->getAverageAge($completedResponses),
            'avgCompletionTime' => $this->getAverageCompletionTime($completedResponses),
            'dataRange' => $this->getDataRange($completedResponses),
        ];
        
        return view('stats.index', $stats);
    }
    
    private function getEmptyStats()
    {
        return [
            'totalResponses' => 0,
            'facultyCount' => Faculty::count(),
            'monthsActive' => 0,
            'completionRate' => 0,
            'dass21Responses' => 0,
            'phq9Distribution' => [],
            'dass21Distribution' => [],
            'facultyDistribution' => [],
            'monthlyTrends' => [],
            'riskDistribution' => $this->getEmptyRiskDistribution(),
            'positiveOutcomes' => 0,
            'avgAge' => 0,
            'avgCompletionTime' => '-',
            'dataRange' => 'Belum ada data',
        ];
    }
    
    private function getMonthsActive()
    {
        $firstResponse = QuizResponse::completed()->oldest('completed_at')->first();
        if (!$firstResponse) return 0;
        
        return $firstResponse->completed_at->diffInMonths(now()) + 1;
    }
    
    private function getCompletionRate()
    {
        $started = QuizResponse::count();
        $completed = QuizResponse::completed()->count();
        
        return $started > 0 ? ($completed / $started) * 100 : 0;
    }
    
    private function getPhq9Distribution($responses)
    {
        return $responses->groupBy('phq9_category')
                        ->map(function ($group) {
                            return $group->count();
                        })
                        ->sortKeys()
                        ->toArray();
    }
    
    private function getDass21Distribution($responses)
    {
        $dass21Responses = $responses->whereNotNull('dass21_category');
        
        if ($dass21Responses->isEmpty()) {
            return ['Tidak ada data' => 0];
        }
        
        return $dass21Responses->groupBy('dass21_category')
                              ->map(function ($group) {
                                  return $group->count();
                              })
                              ->sortKeys()
                              ->toArray();
    }
    
    private function getFacultyDistribution($responses)
    {
        return $responses->groupBy('faculty.name')
                        ->map(function ($group) {
                            return $group->count();
                        })
                        ->sortDesc()
                        ->toArray();
    }
    
    private function getMonthlyTrends()
    {
        $trends = QuizResponse::completed()
            ->selectRaw('DATE_FORMAT(completed_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
            
        // Format month names
        $formattedTrends = [];
        foreach ($trends as $month => $count) {
            $monthName = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $formattedTrends[$monthName] = $count;
        }
        
        return $formattedTrends;
    }
    
    private function getRiskDistribution($responses)
    {
        $distribution = $responses->groupBy('overall_risk_level')
                                 ->map(function ($group) use ($responses) {
                                     $count = $group->count();
                                     $percentage = ($count / $responses->count()) * 100;
                                     
                                     return [
                                         'count' => $count,
                                         'percentage' => $percentage
                                     ];
                                 });
        
        // Add styling for each risk level
        $styledDistribution = [];
        foreach (['Low', 'Moderate', 'High', 'Critical'] as $risk) {
            $data = $distribution->get($risk, ['count' => 0, 'percentage' => 0]);
            
            $styledDistribution[$risk] = array_merge($data, $this->getRiskStyling($risk));
        }
        
        return $styledDistribution;
    }
    
    private function getRiskStyling($risk)
    {
        return match($risk) {
            'Low' => [
                'color' => '#4CAF50',
                'background' => '#E8F5E8',
                'border' => '#4CAF50'
            ],
            'Moderate' => [
                'color' => '#2196F3',
                'background' => '#E8EAF6',
                'border' => '#2196F3'
            ],
            'High' => [
                'color' => '#FF9800',
                'background' => '#FFF3E0',
                'border' => '#FF9800'
            ],
            'Critical' => [
                'color' => '#F44336',
                'background' => '#FFEBEE',
                'border' => '#F44336'
            ],
            default => [
                'color' => '#9E9E9E',
                'background' => '#F5F5F5',
                'border' => '#9E9E9E'
            ]
        };
    }
    
    private function getEmptyRiskDistribution()
    {
        $empty = [];
        foreach (['Low', 'Moderate', 'High', 'Critical'] as $risk) {
            $empty[$risk] = array_merge([
                'count' => 0,
                'percentage' => 0
            ], $this->getRiskStyling($risk));
        }
        return $empty;
    }
    
    private function getPositiveOutcomes($responses)
    {
        $lowRisk = $responses->where('overall_risk_level', 'Low')->count();
        return $responses->count() > 0 ? ($lowRisk / $responses->count()) * 100 : 0;
    }
    
    private function getAverageAge($responses)
    {
        $ages = $responses->filter(function ($response) {
            return $response->birth_date && $response->birth_date->age;
        })->pluck('age');
        
        return $ages->count() > 0 ? $ages->avg() : 0;
    }
    
    private function getAverageCompletionTime($responses)
    {
        $completionTimes = $responses->filter(function ($response) {
            return $response->started_at && $response->completed_at;
        })->map(function ($response) {
            return $response->started_at->diffInMinutes($response->completed_at);
        });
        
        if ($completionTimes->isEmpty()) {
            return '-';
        }
        
        $avgMinutes = $completionTimes->avg();
        
        if ($avgMinutes < 60) {
            return round($avgMinutes) . ' menit';
        }
        
        $hours = floor($avgMinutes / 60);
        $minutes = round($avgMinutes % 60);
        
        return $hours . ' jam ' . $minutes . ' menit';
    }
    
    private function getDataRange($responses)
    {
        if ($responses->isEmpty()) {
            return 'Belum ada data';
        }
        
        $oldest = $responses->min('completed_at');
        $newest = $responses->max('completed_at');
        
        if (!$oldest || !$newest) {
            return 'Data tidak lengkap';
        }
        
        $oldestDate = Carbon::parse($oldest)->format('M Y');
        $newestDate = Carbon::parse($newest)->format('M Y');
        
        return $oldestDate === $newestDate ? $oldestDate : $oldestDate . ' - ' . $newestDate;
    }
}