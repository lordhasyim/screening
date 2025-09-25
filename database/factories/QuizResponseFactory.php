<?php

namespace Database\Factories;

use App\Models\QuizResponse;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizResponse>
 */
class QuizResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $phq9CompletedAt = (clone $startedAt)->modify('+' . rand(5, 30) . ' minutes');
        $dass21CompletedAt = rand(0, 1) ? (clone $phq9CompletedAt)->modify('+' . rand(5, 20) . ' minutes') : null;
        $completedAt = $dass21CompletedAt ?: $phq9CompletedAt;

        // Generate realistic PHQ-9 responses
        $phq9Responses = $this->generatePhq9Responses();
        $phq9Score = $this->calculateScore($phq9Responses);
        $phq9Category = $this->getPhq9Category($phq9Score);
        $needsDass21 = in_array($phq9Category, ['Sedang', 'Tinggi', 'Sangat tinggi']);
        
        // Generate DASS-21 responses only if needed
        $dass21Responses = $needsDass21 ? $this->generateDass21Responses() : null;
        $dass21Score = $dass21Responses ? $this->calculateScore($dass21Responses) : 0;
        $dass21Category = $dass21Responses ? $this->getDass21Category($dass21Score) : null;
        
        // Calculate overall risk level
        $overallRiskLevel = $this->calculateOverallRiskLevel($phq9Category, $dass21Category);

        return [
            'student_year' => $this->faker->numberBetween(2020, 2025),
            'faculty_id' => Faculty::inRandomOrder()->first()?->id ?? 1,
            'department_id' => Department::inRandomOrder()->first()?->id ?? 1,
            'nim' => $this->faker->unique()->numerify('##########'),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->dateTimeBetween('-25 years', '-17 years'),
            'phone' => '08' . $this->faker->numerify('##########'),
            'address' => $this->faker->address(),
            'living_arrangement' => $this->faker->randomElement(['Kos', 'Rumah orang tua', 'Rumah keluarga', 'Asrama', 'Kontrak']),
            'origin_province' => $this->faker->randomElement([
                'Jawa Timur', 'Jawa Barat', 'Jawa Tengah', 'DKI Jakarta', 'Sumatera Utara',
                'Sumatera Barat', 'Sulawesi Selatan', 'Bali', 'Kalimantan Timur', 'Papua'
            ]),
            'origin_area_type' => $this->faker->randomElement(['perkotaan', 'pedesaan', 'pinggiran kota', 'daerah terpencil', 'daerah industri']),
            'email' => $this->faker->optional(0.8)->email(),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'parents_marital_status' => $this->faker->randomElement(['menikah', 'cerai hidup', 'cerai mati', 'pisah tidak resmi', 'menikah lagi']),
            'child_order' => $this->faker->numberBetween(1, 5),
            'siblings_count' => $this->faker->numberBetween(1, 8),
            'scholarship' => $this->faker->optional(0.3)->randomElement(['KIP', 'Beasiswa Prestasi', 'BIDIKMISI', 'Beasiswa Yayasan']),
            'admission_path' => $this->faker->randomElement(['SNBP', 'SNBT', 'Mandiri', 'Lainnya']),
            'parents_education' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']),
            'parents_income' => $this->faker->randomElement(['<2000000', '2000000-5000000', '5000000-10000000', '>10000000']),
            'family_members_count' => $this->faker->numberBetween(2, 8),
            
            // Medical History
            'has_chronic_disease' => $this->faker->boolean(20), // 20% chance
            'chronic_disease_details' => $this->faker->optional(0.2)->sentence(),
            'current_medication' => $this->faker->boolean(15), // 15% chance
            'medication_details' => $this->faker->optional(0.15)->sentence(),
            'head_injury_history' => $this->faker->boolean(10), // 10% chance
            'injury_details' => $this->faker->optional(0.1)->sentence(),
            'substance_use' => $this->faker->randomElement(['Tidak Pernah', 'Pernah', 'Masih aktif']),
            'substance_details' => $this->faker->optional(0.3)->sentence(),
            'psychological_treatment_history' => $this->faker->boolean(15), // 15% chance
            'treatment_details' => $this->faker->optional(0.15)->sentence(),
            'family_mental_health_history' => $this->faker->boolean(25), // 25% chance
            'family_history_details' => $this->faker->optional(0.25)->sentence(),
            'family_relationship_description' => $this->faker->optional(0.7)->paragraph(1),
            
            // Quiz Data
            'quiz_status' => 'completed',
            'phq9_responses' => $phq9Responses,
            'dass21_responses' => $dass21Responses,
            'phq9_total_score' => $phq9Score,
            'phq9_category' => $phq9Category,
            'dass21_total_score' => $dass21Score,
            'dass21_category' => $dass21Category,
            'overall_risk_level' => $overallRiskLevel,
            'phq9_passed_threshold' => $needsDass21,
            'needs_dass21' => $needsDass21,
            'started_at' => $startedAt,
            'phq9_completed_at' => $phq9CompletedAt,
            'dass21_completed_at' => $dass21CompletedAt,
            'completed_at' => $completedAt,
        ];
    }

    /**
     * Generate realistic PHQ-9 responses
     */
    private function generatePhq9Responses(): array
    {
        $responses = [];
        $options = ['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'];
        
        // Create some correlation - if someone has high scores on early questions,
        // they're more likely to have higher scores on later ones
        $baseLevel = $this->faker->randomElement([0, 1, 2, 3]); // 0=low risk, 3=high risk
        
        for ($i = 0; $i < 9; $i++) {
            // Add some randomness but bias toward the base level
            $variation = $this->faker->numberBetween(-1, 1);
            $score = max(0, min(3, $baseLevel + $variation));
            $responses[] = $options[$score];
        }
        
        return $responses;
    }

    /**
     * Generate realistic DASS-21 responses (30 questions)
     */
    private function generateDass21Responses(): array
    {
        $responses = [];
        $options = ['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'];
        
        // Similar approach - create correlated responses
        $baseLevel = $this->faker->randomElement([1, 2, 2, 3]); // Slightly higher since these are people who needed DASS-21
        
        for ($i = 0; $i < 30; $i++) {
            $variation = $this->faker->numberBetween(-1, 1);
            $score = max(0, min(3, $baseLevel + $variation));
            $responses[] = $options[$score];
        }
        
        return $responses;
    }

    /**
     * Calculate total score from responses
     */
    private function calculateScore(array $responses): int
    {
        $scoreMap = [
            'Tidak Pernah' => 0,
            'Kadang-Kadang' => 1,
            'Sering' => 2,
            'Sering Sekali' => 3
        ];

        $total = 0;
        foreach ($responses as $response) {
            $total += $scoreMap[$response] ?? 0;
        }
        
        return $total;
    }

    /**
     * Get PHQ-9 category from score
     */
    private function getPhq9Category(int $score): string
    {
        if ($score <= 15.75) return 'Sangat rendah';
        if ($score <= 20.25) return 'Rendah';
        if ($score <= 24.75) return 'Sedang';
        if ($score <= 29.25) return 'Tinggi';
        return 'Sangat tinggi';
    }

    /**
     * Get DASS-21 category from score
     */
    private function getDass21Category(int $score): string
    {
        if ($score <= 51) return 'Sangat rendah';
        if ($score <= 65) return 'Rendah';
        if ($score <= 79) return 'Sedang';
        if ($score <= 93) return 'Tinggi';
        return 'Sangat tinggi';
    }

    /**
     * Calculate overall risk level
     */
    private function calculateOverallRiskLevel(?string $phq9Category, ?string $dass21Category): string
    {
        $phq9High = in_array($phq9Category, ['Tinggi', 'Sangat tinggi']);
        $dass21High = $dass21Category && in_array($dass21Category, ['Tinggi', 'Sangat tinggi']);
        
        if ($phq9High && $dass21High) return 'Critical';
        if ($phq9High || $dass21High) return 'High';
        
        $phq9Moderate = $phq9Category === 'Sedang';
        $dass21Moderate = $dass21Category === 'Sedang';
        
        if ($phq9Moderate || $dass21Moderate) return 'Moderate';
        
        return 'Low';
    }

    /**
     * State for high risk responses
     */
    public function highRisk(): static
    {
        return $this->state(function (array $attributes) {
            // Generate high-risk PHQ-9 responses
            $phq9Responses = [];
            $options = ['Kadang-Kadang', 'Sering', 'Sering Sekali']; // Only higher options
            
            for ($i = 0; $i < 9; $i++) {
                $phq9Responses[] = $this->faker->randomElement($options);
            }
            
            // Generate high-risk DASS-21 responses
            $dass21Responses = [];
            for ($i = 0; $i < 30; $i++) {
                $dass21Responses[] = $this->faker->randomElement($options);
            }
            
            $phq9Score = $this->calculateScore($phq9Responses);
            $dass21Score = $this->calculateScore($dass21Responses);
            
            return [
                'phq9_responses' => $phq9Responses,
                'dass21_responses' => $dass21Responses,
                'phq9_total_score' => $phq9Score,
                'phq9_category' => $this->getPhq9Category($phq9Score),
                'dass21_total_score' => $dass21Score,
                'dass21_category' => $this->getDass21Category($dass21Score),
                'overall_risk_level' => 'Critical',
                'phq9_passed_threshold' => true,
                'needs_dass21' => true,
            ];
        });
    }

    /**
     * State for low risk responses
     */
    public function lowRisk(): static
    {
        return $this->state(function (array $attributes) {
            // Generate low-risk PHQ-9 responses
            $phq9Responses = [];
            $options = ['Tidak Pernah', 'Kadang-Kadang']; // Only lower options
            
            for ($i = 0; $i < 9; $i++) {
                $phq9Responses[] = $this->faker->randomElement($options);
            }
            
            $phq9Score = $this->calculateScore($phq9Responses);
            
            return [
                'phq9_responses' => $phq9Responses,
                'dass21_responses' => null,
                'phq9_total_score' => $phq9Score,
                'phq9_category' => $this->getPhq9Category($phq9Score),
                'dass21_total_score' => 0,
                'dass21_category' => null,
                'overall_risk_level' => 'Low',
                'phq9_passed_threshold' => false,
                'needs_dass21' => false,
                'dass21_completed_at' => null,
            ];
        });
    }
}