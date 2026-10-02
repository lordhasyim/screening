<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\QuizResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    private Faculty $facultyA;

    private Faculty $facultyB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'Test Admin',
            'nip' => '00000001',
            'email' => 'admin@test.local',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->facultyA = Faculty::create(['name' => 'Fakultas Teknik', 'code' => 'FT']);
        $this->facultyB = Faculty::create(['name' => 'Fakultas Kedokteran', 'code' => 'FK']);

        $deptA = Department::create(['faculty_id' => $this->facultyA->id, 'name' => 'Teknik Informatika', 'code' => 'TI']);
        $deptB = Department::create(['faculty_id' => $this->facultyB->id, 'name' => 'Pendidikan Dokter', 'code' => 'PD']);

        QuizResponse::factory()->count(5)->create(['faculty_id' => $this->facultyA->id, 'department_id' => $deptA->id]);
        QuizResponse::factory()->count(3)->create(['faculty_id' => $this->facultyB->id, 'department_id' => $deptB->id]);
    }

    public function test_guest_is_redirected_away_from_reports(): void
    {
        $this->get(route('admin.reports'))->assertRedirect();
    }

    public function test_admin_can_view_reports_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.reports'));

        $response->assertOk();
        $response->assertSee('Laporan Skrining');
        $response->assertSee('Distribusi Tingkat Risiko');
        $response->assertSee('Partisipasi per Fakultas');
        $response->assertSee('Export Detail Jawaban');
        $response->assertSee($this->facultyA->name);
    }

    public function test_reports_data_endpoint_returns_json_and_filters_by_faculty(): void
    {
        $this->actingAs($this->admin, 'admin');

        $unfiltered = $this->get(route('admin.reports.data'));
        $unfiltered->assertOk();
        $unfiltered->assertJsonStructure(['risk_distribution', 'monthly_trends']);

        $totalUnfiltered = array_sum($unfiltered->json('risk_distribution'));
        $this->assertSame(8, $totalUnfiltered);

        $filtered = $this->get(route('admin.reports.data', ['faculty_id' => $this->facultyA->id]));
        $filtered->assertOk();
        $totalFiltered = array_sum($filtered->json('risk_distribution'));
        $this->assertSame(5, $totalFiltered);
    }

    public function test_reports_page_honors_faculty_filter_on_initial_load(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports', ['faculty_id' => $this->facultyB->id]));

        $response->assertOk();
        $response->assertSee($this->facultyB->name);
    }

    public function test_summary_export_downloads_csv(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.export', ['format' => 'csv']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_detailed_export_downloads_valid_xlsx_with_full_headers(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.export-detailed'));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $tmpFile = $response->getFile()->getPathname();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpFile)->load($tmpFile);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('Timestamp', $sheet->getCell('A1')->getValue());
        $this->assertSame('NIM', $sheet->getCell('F1')->getValue());
        $this->assertSame(
            config('quiz_questions.phq9')[0],
            $sheet->getCell('AM1')->getValue()
        );
        $this->assertSame('Status Pengisian', $sheet->getCell($sheet->getHighestColumn().'1')->getValue());

        // Row 2 is the first data row - NIM must stay a literal string (no
        // scientific notation / leading-zero loss).
        $this->assertSame('s', $sheet->getCell('F2')->getDataType());
        $this->assertNotEmpty($sheet->getCell('F2')->getValue());

        unlink($tmpFile);
    }

    public function test_detailed_export_respects_faculty_filter(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.export-detailed', ['faculty_id' => $this->facultyB->id]));

        $tmpFile = $response->getFile()->getPathname();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpFile)->load($tmpFile);
        $sheet = $spreadsheet->getActiveSheet();

        // facultyB has 3 seeded responses -> rows 2..4, row 5 should be empty.
        $this->assertSame($this->facultyB->name, $sheet->getCell('C2')->getValue());
        $this->assertSame($this->facultyB->name, $sheet->getCell('C4')->getValue());
        $this->assertNull($sheet->getCell('C5')->getValue());

        unlink($tmpFile);
    }

    public function test_inactive_admin_cannot_log_in(): void
    {
        $inactive = AdminUser::create([
            'name' => 'Inactive Admin',
            'nip' => '00000002',
            'email' => 'inactive@test.local',
            'password' => bcrypt('secret123'),
            'role' => 'viewer',
            'is_active' => false,
        ]);

        $attempt = \Illuminate\Support\Facades\Auth::guard('admin')->attempt([
            'email' => 'inactive@test.local',
            'password' => 'secret123',
        ]);

        $this->assertFalse($attempt);
    }
}
