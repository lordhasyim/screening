<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;

class FacultySeeder extends Seeder
{
    public function run()
    {
        $faculties = [
            [
                'name' => 'Fakultas Ilmu Pendidikan',
                'code' => 'FIP',
                'departments' => [
                    ['name' => 'Bimbingan dan Konseling', 'code' => 'BK'],
                    ['name' => 'Administrasi Pendidikan', 'code' => 'AP'],
                    ['name' => 'Teknologi Pendidikan', 'code' => 'TEP'],
                    ['name' => 'Pendidikan Luar Sekolah', 'code' => 'PLS'],
                    ['name' => 'Psikologi', 'code' => 'PSI'],
                    ['name' => 'Pendidikan Guru Pendidikan Anak Usia Dini', 'code' => 'PG-PAUD'],
                    ['name' => 'Pendidikan Guru Sekolah Dasar', 'code' => 'PGSD'],
                ]
            ],
            [
                'name' => 'Fakultas Sastra',
                'code' => 'FS',
                'departments' => [
                    ['name' => 'Sastra Indonesia', 'code' => 'SI'],
                    ['name' => 'Sastra Inggris', 'code' => 'SING'],
                    ['name' => 'Sastra Jerman', 'code' => 'SJ'],
                    ['name' => 'Sastra Arab', 'code' => 'SA'],
                    ['name' => 'Pendidikan Bahasa Indonesia', 'code' => 'PBI'],
                    ['name' => 'Pendidikan Bahasa Inggris', 'code' => 'PBI-ENG'],
                    ['name' => 'Pendidikan Bahasa Jerman', 'code' => 'PBJ'],
                    ['name' => 'Pendidikan Bahasa Arab', 'code' => 'PBA'],
                    ['name' => 'Pendidikan Seni Rupa', 'code' => 'PSR'],
                    ['name' => 'Seni dan Desain', 'code' => 'SD'],
                ]
            ],
            [
                'name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam',
                'code' => 'FMIPA',
                'departments' => [
                    ['name' => 'Matematika', 'code' => 'MAT'],
                    ['name' => 'Fisika', 'code' => 'FIS'],
                    ['name' => 'Kimia', 'code' => 'KIM'],
                    ['name' => 'Biologi', 'code' => 'BIO'],
                    ['name' => 'Pendidikan Matematika', 'code' => 'P-MAT'],
                    ['name' => 'Pendidikan Fisika', 'code' => 'P-FIS'],
                    ['name' => 'Pendidikan Kimia', 'code' => 'P-KIM'],
                    ['name' => 'Pendidikan Biologi', 'code' => 'P-BIO'],
                ]
            ],
            [
                'name' => 'Fakultas Ekonomi dan Bisnis',
                'code' => 'FEB',
                'departments' => [
                    ['name' => 'Ekonomi Pembangunan', 'code' => 'EP'],
                    ['name' => 'Manajemen', 'code' => 'MNJ'],
                    ['name' => 'Akuntansi', 'code' => 'AKT'],
                    ['name' => 'Pendidikan Ekonomi', 'code' => 'P-EKO'],
                    ['name' => 'Pendidikan Administrasi Perkantoran', 'code' => 'PAP'],
                    ['name' => 'Pendidikan Tata Niaga', 'code' => 'PTN'],
                    ['name' => 'Pendidikan Akuntansi', 'code' => 'P-AKT'],
                ]
            ],
            [
                'name' => 'Fakultas Ilmu Sosial',
                'code' => 'FIS',
                'departments' => [
                    ['name' => 'Ilmu Sejarah', 'code' => 'SEJ'],
                    ['name' => 'Pendidikan Sejarah', 'code' => 'P-SEJ'],
                    ['name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'code' => 'PPKn'],
                    ['name' => 'Pendidikan Geografi', 'code' => 'P-GEO'],
                    ['name' => 'Pendidikan Sosiologi', 'code' => 'P-SOS'],
                    ['name' => 'Ilmu Perpustakaan', 'code' => 'IP'],
                ]
            ],
            [
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'departments' => [
                    ['name' => 'Teknik Sipil', 'code' => 'TS'],
                    ['name' => 'Teknik Mesin', 'code' => 'TM'],
                    ['name' => 'Teknik Elektro', 'code' => 'TE'],
                    ['name' => 'Teknik Industri', 'code' => 'TI'],
                    ['name' => 'Pendidikan Teknik Bangunan', 'code' => 'PTB'],
                    ['name' => 'Pendidikan Teknik Mesin', 'code' => 'PTM'],
                    ['name' => 'Pendidikan Teknik Elektro', 'code' => 'PTE'],
                    ['name' => 'Pendidikan Teknik Informatika', 'code' => 'PTI'],
                    ['name' => 'Pendidikan Teknik Otomotif', 'code' => 'PTO'],
                ]
            ],
            [
                'name' => 'Fakultas Ilmu Keolahragaan',
                'code' => 'FIK',
                'departments' => [
                    ['name' => 'Pendidikan Jasmani, Kesehatan dan Rekreasi', 'code' => 'PJKR'],
                    ['name' => 'Pendidikan Kepelatihan Olahraga', 'code' => 'PKO'],
                    ['name' => 'Ilmu Keolahragaan', 'code' => 'IKOR'],
                    ['name' => 'Ilmu Kesehatan Masyarakat', 'code' => 'IKM'],
                ]
            ],
        ];

        foreach ($faculties as $facultyData) {
            $faculty = Faculty::create([
                'name' => $facultyData['name'],
                'code' => $facultyData['code'],
            ]);

            foreach ($facultyData['departments'] as $deptData) {
                Department::create([
                    'faculty_id' => $faculty->id,
                    'name' => $deptData['name'],
                    'code' => $deptData['code'],
                ]);
            }
        }
    }
}