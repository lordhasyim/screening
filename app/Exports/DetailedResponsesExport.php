<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DetailedResponsesExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithColumnWidths, WithEvents
{
    // Timestamp + demographic/intake fields (everything before the question columns)
    private const DEMOGRAPHIC_COLUMN_COUNT = 38;

    // PHQ-9 (9) + DASS-21 (30) raw answer columns
    private const QUESTION_COLUMN_COUNT = 39;

    public function __construct(private Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return array_merge(
            [
                'Timestamp',
                'Tahun Mahasiswa',
                'Fakultas',
                'Jurusan',
                'Jenjang Pendidikan',
                'NIM',
                'Nama Lengkap',
                'Jenis Kelamin',
                'Tempat Lahir',
                'Tanggal Lahir',
                'No. Telepon',
                'Alamat Domisili',
                'Status Tempat Tinggal',
                'Asal Provinsi',
                'Tipe Daerah Asal',
                'Email',
                'Agama/Kepercayaan',
                'Status Pernikahan Orang Tua',
                'Anak ke-',
                'Dari Berapa Bersaudara',
                'Beasiswa',
                'Jalur Masuk Mahasiswa',
                'Pendidikan Terakhir Orang Tua',
                'Penghasilan Orang Tua',
                'Jumlah Anggota Keluarga di Rumah',
                'Penyakit Kronis',
                'Detail Penyakit Kronis',
                'Sedang Mengonsumsi Obat',
                'Detail Obat',
                'Riwayat Cedera Kepala',
                'Detail Cedera Kepala',
                'Pola Konsumsi Zat',
                'Detail Konsumsi Zat',
                'Riwayat Perawatan Psikologi',
                'Detail Perawatan Psikologi',
                'Riwayat Kesehatan Mental Keluarga',
                'Detail Riwayat Keluarga',
                'Deskripsi Hubungan Keluarga',
            ],
            config('quiz_questions.phq9'),
            config('quiz_questions.dass21'),
            [
                'Skor PHQ-9',
                'Kategori PHQ-9',
                'Skor DASS-21',
                'Kategori DASS-21',
                'Tingkat Risiko',
                'Status Pengisian',
            ]
        );
    }

    public function map($response): array
    {
        $row = [
            optional($response->completed_at ?? $response->created_at)->format('Y-m-d H:i:s'),
            $response->student_year,
            $response->faculty->name ?? 'N/A',
            $response->department->name ?? $response->department_name ?? 'N/A',
            $response->education_level,
            $response->nim,
            $response->full_name,
            $response->gender,
            $response->birth_place,
            optional($response->birth_date)->format('d/m/Y'),
            $response->phone,
            $response->address,
            $response->living_arrangement,
            $response->origin_province,
            $response->origin_area_type,
            $response->email,
            $response->religion,
            $response->parents_marital_status,
            $response->child_order,
            $response->siblings_count,
            $response->scholarship,
            $response->admission_path,
            $response->parents_education,
            $response->parents_income,
            $response->family_members_count,
            $response->has_chronic_disease ? 'Ya' : 'Tidak',
            $response->chronic_disease_details,
            $response->current_medication ? 'Ya' : 'Tidak',
            $response->medication_details,
            $response->head_injury_history ? 'Ya' : 'Tidak',
            $response->injury_details,
            $response->substance_use,
            $response->substance_details,
            $response->psychological_treatment_history ? 'Ya' : 'Tidak',
            $response->treatment_details,
            $response->family_mental_health_history ? 'Ya' : 'Tidak',
            $response->family_history_details,
            $response->family_relationship_description,
        ];

        foreach (array_keys(config('quiz_questions.phq9')) as $index) {
            $row[] = $response->phq9_responses[$index] ?? '';
        }

        foreach (array_keys(config('quiz_questions.dass21')) as $index) {
            $row[] = $response->dass21_responses[$index] ?? '';
        }

        $row[] = $response->phq9_total_score ?? 'N/A';
        $row[] = $response->phq9_category ?? 'N/A';
        $row[] = $response->dass21_total_score ?? 'N/A';
        $row[] = $response->dass21_category ?? 'N/A';
        $row[] = $response->overall_risk_level ?? 'N/A';
        $row[] = $response->quiz_status;

        return $row;
    }

    /**
     * Long digit strings (NIM, phone numbers) are otherwise auto-detected as
     * numeric by PhpSpreadsheet and rendered in scientific notation / lose
     * leading zeros. Force them to stay literal text.
     */
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && preg_match('/^\d{5,}$/', $value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnWidths(): array
    {
        $widths = [];

        foreach ($this->headings() as $index => $heading) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $isQuestionColumn = $index >= self::DEMOGRAPHIC_COLUMN_COUNT
                && $index < self::DEMOGRAPHIC_COLUMN_COUNT + self::QUESTION_COLUMN_COUNT;

            $widths[$column] = $isQuestionColumn ? 30 : min(max(mb_strlen($heading) + 2, 10), 35);
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();

                $headerRange = "A1:{$lastColumn}1";
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('DCE6F1');

                $questionStartColumn = Coordinate::stringFromColumnIndex(self::DEMOGRAPHIC_COLUMN_COUNT + 1);
                $questionEndColumn = Coordinate::stringFromColumnIndex(self::DEMOGRAPHIC_COLUMN_COUNT + self::QUESTION_COLUMN_COUNT);
                $sheet->getStyle("{$questionStartColumn}1:{$questionEndColumn}1")->getAlignment()->setWrapText(true);

                $sheet->freezePane('A2');
                $sheet->setAutoFilter($headerRange);
            },
        ];
    }
}
