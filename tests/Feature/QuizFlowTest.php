<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Province;
use App\Models\QuizResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    private Faculty $faculty;

    private Department $department;

    private Province $province;

    private City $city;

    protected function setUp(): void
    {
        parent::setUp();

        // No migration in this repo creates `provinces`/`cities` - they only
        // exist in the live production DB (loaded outside version control).
        // Scaffold minimal versions here so the identity form flow is testable.
        if (! Schema::hasTable('provinces')) {
            Schema::create('provinces', function ($table) {
                $table->id();
                $table->string('name');
                $table->timestamp('removed_at')->nullable();
            });
        }
        if (! Schema::hasTable('cities')) {
            Schema::create('cities', function ($table) {
                $table->id();
                $table->foreignId('province_id')->constrained();
                $table->string('name');
                $table->timestamp('removed_at')->nullable();
                $table->timestamps();
            });
        }

        $this->faculty = Faculty::create(['name' => 'Fakultas Teknik', 'code' => 'FT']);
        $this->department = Department::create([
            'faculty_id' => $this->faculty->id,
            'name' => 'Teknik Informatika',
            'code' => 'TI',
        ]);
        $this->province = Province::create(['name' => 'Jawa Timur']);
        $this->city = City::create(['province_id' => $this->province->id, 'name' => 'Malang']);
    }

    private function identityPayload(array $overrides = []): array
    {
        return array_merge([
            'student_year' => 2023,
            'faculty_id' => $this->faculty->id,
            'department_id' => $this->department->id,
            'education_level' => 'S1',
            'nim' => '1234567890',
            'full_name' => 'Budi Santoso',
            'gender' => 'Laki-laki',
            'birth_place' => 'Malang',
            'birth_date' => '2002-05-10',
            'phone' => '081234567890',
            'address' => 'Jl. Mahasiswa No. 1 Malang',
            'living_arrangement' => 'Kos',
            'origin_province_id' => $this->province->id,
            'origin_city_id' => $this->city->id,
            'origin_area_type' => 'perkotaan',
            'email' => 'budi@example.com',
            'religion' => 'Islam',
            'parents_marital_status' => 'menikah',
            'child_order' => 1,
            'siblings_count' => 2,
            'admission_path' => 'SNBP',
            'parents_education' => 'S1',
            'parents_income' => '2000000-5000000',
            'family_members_count' => 4,
            'substance_use' => 'Tidak Pernah',
        ], $overrides);
    }

    public function test_quiz_index_loads(): void
    {
        $this->get(route('quiz.index'))->assertOk();
    }

    public function test_identity_form_loads(): void
    {
        $this->get(route('quiz.identity'))->assertOk();
    }

    public function test_low_risk_quiz_flow_completes_without_dass21(): void
    {
        $this->post(route('quiz.identity'), $this->identityPayload())
            ->assertRedirect(route('quiz.phq9'));

        $quizResponse = QuizResponse::where('nim', '1234567890')->firstOrFail();
        $this->assertSame('started', $quizResponse->quiz_status);

        $this->get(route('quiz.phq9'))->assertOk();

        // All "Tidak Pernah" -> lowest possible score (9), well under the
        // "Tinggi"/"Sangat tinggi" threshold that would require DASS-21.
        $response = $this->post(route('quiz.phq9'), [
            'phq9' => array_fill(0, 9, 'Tidak Pernah'),
        ]);

        $quizResponse->refresh();
        $this->assertSame('completed', $quizResponse->quiz_status);
        $this->assertSame(9, $quizResponse->phq9_total_score);
        $this->assertSame('Sangat rendah', $quizResponse->phq9_category);
        $this->assertNull($quizResponse->dass21_responses);
        $this->assertSame('Low', $quizResponse->overall_risk_level);

        $response->assertRedirect(route('quiz.result', $quizResponse->id));
        $this->get(route('quiz.result', $quizResponse->id))->assertOk();
    }

    public function test_high_risk_phq9_continues_to_dass21_and_completes(): void
    {
        $this->post(route('quiz.identity'), $this->identityPayload(['nim' => '9876543210']))
            ->assertRedirect(route('quiz.phq9'));

        $quizResponse = QuizResponse::where('nim', '9876543210')->firstOrFail();

        // All "Sering Sekali" -> max score (36) -> "Sangat tinggi" -> must continue to DASS-21.
        $response = $this->post(route('quiz.phq9'), [
            'phq9' => array_fill(0, 9, 'Sering Sekali'),
        ]);

        $quizResponse->refresh();
        $this->assertSame('phq9_completed', $quizResponse->quiz_status);
        $this->assertSame(36, $quizResponse->phq9_total_score);
        $this->assertSame('Sangat tinggi', $quizResponse->phq9_category);
        $this->assertTrue((bool) $quizResponse->needs_dass21);
        $response->assertRedirect(route('quiz.dass21'));

        $this->get(route('quiz.dass21'))->assertOk();

        $response = $this->post(route('quiz.dass21'), [
            'dass21' => array_fill(0, 30, 'Tidak Pernah'),
        ]);

        $quizResponse->refresh();
        $this->assertSame('completed', $quizResponse->quiz_status);
        $this->assertSame(30, $quizResponse->dass21_total_score);
        $this->assertSame('Sangat rendah', $quizResponse->dass21_category);
        // PHQ-9 high + DASS-21 low -> overall "High" (not "Critical", which needs both high).
        $this->assertSame('High', $quizResponse->overall_risk_level);

        $response->assertRedirect(route('quiz.result', $quizResponse->id));
        $this->get(route('quiz.result', $quizResponse->id))->assertOk();
    }

    public function test_both_high_phq9_and_dass21_result_in_critical_risk(): void
    {
        $this->post(route('quiz.identity'), $this->identityPayload(['nim' => '5551112222']));
        $quizResponse = QuizResponse::where('nim', '5551112222')->firstOrFail();

        $this->post(route('quiz.phq9'), ['phq9' => array_fill(0, 9, 'Sering Sekali')]);
        $this->post(route('quiz.dass21'), ['dass21' => array_fill(0, 30, 'Sering Sekali')]);

        $quizResponse->refresh();
        $this->assertSame('Sangat tinggi', $quizResponse->dass21_category);
        $this->assertSame('Critical', $quizResponse->overall_risk_level);
    }

    public function test_identity_rejects_duplicate_completed_nim(): void
    {
        QuizResponse::factory()->create([
            'nim' => '1111111111',
            'faculty_id' => $this->faculty->id,
            'department_id' => $this->department->id,
            'quiz_status' => 'completed',
        ]);

        $this->post(route('quiz.identity'), $this->identityPayload(['nim' => '1111111111']))
            ->assertSessionHasErrors('nim');
    }

    public function test_identity_rejects_non_numeric_nim(): void
    {
        $this->post(route('quiz.identity'), $this->identityPayload(['nim' => 'abc123']))
            ->assertSessionHasErrors('nim');
    }

    public function test_phq9_requires_all_nine_answers(): void
    {
        $this->post(route('quiz.identity'), $this->identityPayload());

        $this->post(route('quiz.phq9'), ['phq9' => array_fill(0, 5, 'Tidak Pernah')])
            ->assertSessionHasErrors('phq9');
    }

    public function test_phq9_without_session_redirects_to_identity(): void
    {
        $this->get(route('quiz.phq9'))->assertRedirect(route('quiz.identity'));
    }
}
