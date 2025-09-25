<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizResponse;
use App\Models\Faculty;
use App\Models\Department;

class QuizResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have faculties and departments
        if (Faculty::count() == 0) {
            $this->call(FacultySeeder::class);
        }

        if (Department::count() == 0) {
            $this->call(DepartmentSeeder::class);
        }

        $this->command->info('Creating quiz responses...');

        // Create a variety of quiz responses for realistic dashboard testing
        
        // 1. Low Risk Students (40% - majority)
        QuizResponse::factory()
            ->count(40)
            ->lowRisk()
            ->create();
        
        $this->command->info('Created 40 low-risk quiz responses');

        // 2. High Risk Students (15% - needs attention)
        QuizResponse::factory()
            ->count(15)
            ->highRisk()
            ->create();
            
        $this->command->info('Created 15 high-risk quiz responses');

        // 3. Mixed Risk Students (45% - normal distribution)
        QuizResponse::factory()
            ->count(45)
            ->create();
            
        $this->command->info('Created 45 mixed-risk quiz responses');

        // 4. Recent submissions (for time-based analytics)
        QuizResponse::factory()
            ->count(20)
            ->state([
                'started_at' => now()->subDays(rand(1, 7)),
                'completed_at' => now()->subDays(rand(1, 7))
            ])
            ->create();
            
        $this->command->info('Created 20 recent quiz responses');

        // 5. Students from different years (for year-based analytics)
        foreach ([2020, 2021, 2022, 2023, 2024, 2025] as $year) {
            QuizResponse::factory()
                ->count(rand(5, 15))
                ->state(['student_year' => $year])
                ->create();
        }
        
        $this->command->info('Created quiz responses for different student years');

        // 6. Incomplete responses (for testing incomplete states)
        QuizResponse::factory()
            ->count(5)
            ->state([
                'quiz_status' => 'started',
                'phq9_responses' => null,
                'dass21_responses' => null,
                'phq9_total_score' => null,
                'dass21_total_score' => null,
                'phq9_category' => null,
                'dass21_category' => null,
                'overall_risk_level' => null,
                'phq9_completed_at' => null,
                'dass21_completed_at' => null,
                'completed_at' => null,
            ])
            ->create();
            
        $this->command->info('Created 5 incomplete quiz responses');

        // 7. PHQ-9 only responses (didn't need DASS-21)
        QuizResponse::factory()
            ->count(10)
            ->state([
                'quiz_status' => 'completed',
                'dass21_responses' => null,
                'dass21_total_score' => 0,
                'dass21_category' => null,
                'phq9_passed_threshold' => false,
                'needs_dass21' => false,
                'dass21_completed_at' => null,
            ])
            ->create();
            
        $this->command->info('Created 10 PHQ-9 only responses');

        $total = QuizResponse::count();
        $this->command->info("Total quiz responses created: {$total}");

        // Show some statistics
        $this->showStatistics();
    }

    /**
     * Show seeded data statistics
     */
    private function showStatistics(): void
    {
        $this->command->info("\n--- Quiz Response Statistics ---");
        
        // Risk level distribution
        $riskLevels = QuizResponse::selectRaw('overall_risk_level, COUNT(*) as count')
            ->whereNotNull('overall_risk_level')
            ->groupBy('overall_risk_level')
            ->pluck('count', 'overall_risk_level')
            ->toArray();
            
        $this->command->info("Risk Level Distribution:");
        foreach ($riskLevels as $level => $count) {
            $this->command->info("  - {$level}: {$count}");
        }

        // Quiz status distribution
        $statuses = QuizResponse::selectRaw('quiz_status, COUNT(*) as count')
            ->groupBy('quiz_status')
            ->pluck('count', 'quiz_status')
            ->toArray();
            
        $this->command->info("\nQuiz Status Distribution:");
        foreach ($statuses as $status => $count) {
            $this->command->info("  - {$status}: {$count}");
        }

        // Year distribution
        $years = QuizResponse::selectRaw('student_year, COUNT(*) as count')
            ->groupBy('student_year')
            ->orderBy('student_year')
            ->pluck('count', 'student_year')
            ->toArray();
            
        $this->command->info("\nStudent Year Distribution:");
        foreach ($years as $year => $count) {
            $this->command->info("  - {$year}: {$count}");
        }

        // Gender distribution
        $genders = QuizResponse::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();
            
        $this->command->info("\nGender Distribution:");
        foreach ($genders as $gender => $count) {
            $this->command->info("  - {$gender}: {$count}");
        }

        // Average scores
        $avgPhq9 = QuizResponse::whereNotNull('phq9_total_score')->avg('phq9_total_score');
        $avgDass21 = QuizResponse::whereNotNull('dass21_total_score')->where('dass21_total_score', '>', 0)->avg('dass21_total_score');
        
        $this->command->info("\nAverage Scores:");
        $this->command->info("  - PHQ-9: " . round($avgPhq9, 2));
        $this->command->info("  - DASS-21: " . round($avgDass21, 2));

        $this->command->info("\n--- End Statistics ---\n");
    }
}