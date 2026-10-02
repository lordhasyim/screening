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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummaryResponsesExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithColumnWidths, WithEvents
{
    public function __construct(private Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'NIM',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Fakultas',
            'Jurusan',
            'Tahun Mahasiswa',
            'Skor PHQ-9',
            'Kategori PHQ-9',
            'Skor DASS-21',
            'Kategori DASS-21',
            'Tingkat Risiko',
            'Status Pengisian',
            'Tanggal Mulai',
            'Tanggal Selesai',
        ];
    }

    public function map($response): array
    {
        return [
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
        ];
    }

    /**
     * Same reasoning as DetailedResponsesExport: a long all-digit NIM would
     * otherwise be auto-detected as numeric and rendered in scientific
     * notation / lose leading zeros.
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
        return [
            'A' => 8,
            'B' => 16,
            'C' => 22,
            'D' => 16,
            'E' => 32,
            'F' => 32,
            'G' => 18,
            'H' => 14,
            'I' => 18,
            'J' => 16,
            'K' => 20,
            'L' => 18,
            'M' => 20,
            'N' => 20,
            'O' => 20,
        ];
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

                $sheet->freezePane('A2');
                $sheet->setAutoFilter($headerRange);
            },
        ];
    }
}
