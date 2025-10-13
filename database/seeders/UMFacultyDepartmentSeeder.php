<?php
// database/seeders/UMFacultyDepartmentSeeder.php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UMFacultyDepartmentSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Starting UM Faculty & Department import...');
        
        DB::beginTransaction();
        
        try {
            $data = $this->getData();
            $facultyCount = 0;
            $departmentCount = 0;

            foreach ($data as $facultyData) {
                // Match by CODE (code is unique)
                $faculty = Faculty::updateOrCreate(
                    ['code' => $facultyData['code']],
                    [
                        'name' => $facultyData['name'],
                        'code' => $facultyData['code']
                    ]
                );
                
                $facultyCount++;
                $this->command->info("✓ Faculty: {$faculty->name} ({$faculty->code})");

                // Create departments - MATCH BY CODE ONLY (globally unique)
                foreach ($facultyData['departments'] as $deptData) {
                    $department = Department::updateOrCreate(
                        ['code' => $deptData['code']], // Match by code only
                        [
                            'faculty_id' => $faculty->id,
                            'name' => $deptData['name'],
                            'level' => $deptData['level'],
                            'code' => $deptData['code']
                        ]
                    );
                    
                    $departmentCount++;
                    $this->command->line("  → {$department->name} ({$department->level}) [{$department->code}]");
                }
                
                $this->command->newLine();
            }

            DB::commit();
            
            $this->command->info("✅ Successfully imported:");
            $this->command->info("   - {$facultyCount} Faculties");
            $this->command->info("   - {$departmentCount} Departments");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Import failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function getData()
    {
        return [
            [
                'name' => 'FAKULTAS ILMU PENDIDIKAN (FIP)',
                'code' => 'FIP',
                'departments' => [
                    ['name' => 'BIMBINGAN DAN KONSELING', 'level' => 'S1,S2,S3', 'code' => 'BK'],
                    ['name' => 'TEKNOLOGI PENDIDIKAN', 'level' => 'S1', 'code' => 'TP'],
                    ['name' => 'ADMINISTRASI PENDIDIKAN', 'level' => 'S1', 'code' => 'AP'],
                    ['name' => 'PENDIDIKAN LUAR SEKOLAH', 'level' => 'S1,S2,S3', 'code' => 'PLS'],
                    ['name' => 'PENDIDIKAN GURU ANAK USIA DINI', 'level' => 'S1', 'code' => 'PGPAUD'],
                    ['name' => 'PENDIDIKAN LUAR BIASA', 'level' => 'S1', 'code' => 'PLB'],
                    ['name' => 'TEKNOLOGI PEMBELAJARAN', 'level' => 'S2,S3', 'code' => 'TEP'],
                    ['name' => 'MANAJEMEN PENDIDIKAN', 'level' => 'S2,S3', 'code' => 'MP-FIP'],
                    ['name' => 'PENDIDIKAN ANAK USIA DINI', 'level' => 'S2', 'code' => 'PAUD'],
                    ['name' => 'PENDIDIKAN KHUSUS', 'level' => 'S2', 'code' => 'PK'],
                ]
            ],
            [
                'name' => 'FAKULTAS SASTRA (FS)',
                'code' => 'FS',
                'departments' => [
                    ['name' => 'PENDIDIKAN BAHASA, SASTRA INDONESIA DAN DAERAH', 'level' => 'S1', 'code' => 'PBSID'],
                    ['name' => 'BAHASA DAN SASTRA INDONESIA', 'level' => 'S1', 'code' => 'BSI'],
                    ['name' => 'ILMU PERPUSTAKAAN', 'level' => 'S1', 'code' => 'IP-FS'],
                    ['name' => 'PENDIDIKAN BAHASA INGGRIS', 'level' => 'S1,S2,S3', 'code' => 'PBI'],
                    ['name' => 'PENDIDIKAN BAHASA ARAB', 'level' => 'S1', 'code' => 'PBA'],
                    ['name' => 'PENDIDIKAN BAHASA JERMAN', 'level' => 'S1', 'code' => 'PBJ'],
                    ['name' => 'PENDIDIKAN BAHASA MANDARIN', 'level' => 'S1', 'code' => 'PBM'],
                    ['name' => 'PENDIDIKAN SENI RUPA', 'level' => 'S1', 'code' => 'PSR'],
                    ['name' => 'PENDIDIKAN SENI TARI DAN MUSIK', 'level' => 'S1', 'code' => 'PSTM'],
                    ['name' => 'DESAIN KOMUNIKASI VISUAL', 'level' => 'S1', 'code' => 'DKV'],
                    ['name' => 'PENDIDIKAN BAHASA INDONESIA', 'level' => 'S2,S3', 'code' => 'PBIND'],
                    ['name' => 'KEGURUAN BAHASA', 'level' => 'S2', 'code' => 'KB'],
                    ['name' => 'KEGURUAN BAHASA ARAB', 'level' => 'S2', 'code' => 'KBA'],
                    ['name' => 'KEGURUAN SENI RUPA', 'level' => 'S2', 'code' => 'KSR'],
                ]
            ],
            [
                'name' => 'FAKULTAS MATEMATIKA DAN ILMU PENGETAHUAN ALAM (FMIPA)',
                'code' => 'FMIPA',
                'departments' => [
                    ['name' => 'PENDIDIKAN MATEMATIKA', 'level' => 'S1,S2,S3', 'code' => 'PMAT'],
                    ['name' => 'MATEMATIKA', 'level' => 'S1,S2', 'code' => 'MAT'],
                    ['name' => 'PENDIDIKAN FISIKA', 'level' => 'S1,S2,S3', 'code' => 'PFIS'],
                    ['name' => 'FISIKA', 'level' => 'S1,S2', 'code' => 'FIS'],
                    ['name' => 'PENDIDIKAN KIMIA', 'level' => 'S1,S2,S3', 'code' => 'PKIM'],
                    ['name' => 'KIMIA', 'level' => 'S1,S2', 'code' => 'KIM'],
                    ['name' => 'PENDIDIKAN BIOLOGI', 'level' => 'S1,S2,S3', 'code' => 'PBIO'],
                    ['name' => 'BIOLOGI', 'level' => 'S1,S2', 'code' => 'BIO'],
                    ['name' => 'BIOTEKNOLOGI', 'level' => 'S1', 'code' => 'BIOTEK'],
                    ['name' => 'PENDIDIKAN ILMU PENGETAHUAN ALAM', 'level' => 'S1', 'code' => 'PIPA'],
                    ['name' => 'GIZI', 'level' => 'S1', 'code' => 'GZ'],
                    ['name' => 'FARMASI', 'level' => 'S1', 'code' => 'FAR'],
                ]
            ],
            [
                'name' => 'FAKULTAS EKONOMI DAN BISNIS (FEB)',
                'code' => 'FEB',
                'departments' => [
                    ['name' => 'PENDIDIKAN TATA NIAGA', 'level' => 'S1', 'code' => 'PTN'],
                    ['name' => 'PENDIDIKAN ADMINISTRASI PERKANTORAN', 'level' => 'S1', 'code' => 'PAP'],
                    ['name' => 'MANAJEMEN', 'level' => 'S1,S2', 'code' => 'MNJ'],
                    ['name' => 'PENDIDIKAN AKUNTANSI', 'level' => 'S1', 'code' => 'PAK'],
                    ['name' => 'PENDIDIKAN BISNIS DAN MANAJEMEN', 'level' => 'S2', 'code' => 'PBMN'],
                    ['name' => 'PENDIDIKAN EKONOMI', 'level' => 'S1,S2,S3', 'code' => 'PE'],
                    ['name' => 'EKONOMI PEMBANGUNAN', 'level' => 'S1', 'code' => 'EP'],
                    ['name' => 'ILMU EKONOMI', 'level' => 'S2', 'code' => 'IE'],
                    ['name' => 'ILMU MANAJEMEN', 'level' => 'S3', 'code' => 'IM'],
                ]
            ],
            [
                'name' => 'FAKULTAS TEKNIK (FT)',
                'code' => 'FT',
                'departments' => [
                    ['name' => 'PENDIDIKAN TEKNIK MESIN', 'level' => 'S1', 'code' => 'PTM'],
                    ['name' => 'PENDIDIKAN TEKNIK OTOMOTIF', 'level' => 'S1', 'code' => 'PTO'],
                    ['name' => 'TEKNIK MESIN', 'level' => 'S1,S2', 'code' => 'TM'],
                    ['name' => 'TEKNIK INDUSTRI', 'level' => 'S1', 'code' => 'TI'],
                    ['name' => 'PENDIDIKAN TEKNIK BANGUNAN', 'level' => 'S1', 'code' => 'PTB'],
                    ['name' => 'TEKNIK SIPIL', 'level' => 'S1,S2', 'code' => 'TS'],
                    ['name' => 'PENDIDIKAN TEKNIK INFORMATIKA', 'level' => 'S1', 'code' => 'PTI'],
                    ['name' => 'PENDIDIKAN TEKNIK ELEKTRO', 'level' => 'S1', 'code' => 'PTE'],
                    ['name' => 'TEKNIK INFORMATIKA', 'level' => 'S1', 'code' => 'TIF'],
                    ['name' => 'TEKNIK ELEKTRO', 'level' => 'S1,S2', 'code' => 'TE'],
                    ['name' => 'PENDIDIKAN TATA BOGA', 'level' => 'S1', 'code' => 'PTBoga'],
                    ['name' => 'PENDIDIKAN TATA BUSANA', 'level' => 'S1', 'code' => 'PTBus'],
                    ['name' => 'TEKNIK ELEKTRO DAN INFORMATIKA', 'level' => 'S3', 'code' => 'TEI'],
                ]
            ],
            [
                'name' => 'FAKULTAS ILMU KEOLAHRAGAAN (FIK)',
                'code' => 'FIK',
                'departments' => [
                    ['name' => 'PENDIDIKAN JASMANI, KESEHATAN DAN REKREASI', 'level' => 'S1', 'code' => 'PJKR'],
                    ['name' => 'ILMU KESEHATAN MASYARAKAT', 'level' => 'S1', 'code' => 'IKM'],
                    ['name' => 'ILMU KEOLAHRAGAAN', 'level' => 'S1', 'code' => 'IKOR'],
                    ['name' => 'PENDIDIKAN KEPELATIHAN OLAHRAGA', 'level' => 'S1', 'code' => 'PKO'],
                    ['name' => 'PENDIDIKAN OLAHRAGA', 'level' => 'S2', 'code' => 'POR'],
                ]
            ],
            [
                'name' => 'FAKULTAS ILMU SOSIAL (FIS)',
                'code' => 'FIS',
                'departments' => [
                    ['name' => 'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN', 'level' => 'S1,S2', 'code' => 'PPKN'],
                    ['name' => 'PENDIDIKAN GEOGRAFI', 'level' => 'S1,S2,S3', 'code' => 'PGEO'],
                    ['name' => 'GEOGRAFI', 'level' => 'S1', 'code' => 'GEO'],
                    ['name' => 'PENDIDIKAN SEJARAH', 'level' => 'S1,S2', 'code' => 'PSej'],
                    ['name' => 'ILMU SEJARAH', 'level' => 'S1', 'code' => 'SEJ'],
                    ['name' => 'PENDIDIKAN ILMU PENGETAHUAN SOSIAL', 'level' => 'S1', 'code' => 'PIPS'],
                    ['name' => 'PENDIDIKAN SOSIOLOGI', 'level' => 'S1', 'code' => 'PSos'],
                    ['name' => 'ILMU KOMUNIKASI', 'level' => 'S1', 'code' => 'IKOM'],
                ]
            ],
            [
                'name' => 'FAKULTAS PSIKOLOGI (FPsi)',
                'code' => 'FPsi',
                'departments' => [
                    ['name' => 'PSIKOLOGI', 'level' => 'S1,S2', 'code' => 'PSI'],
                    ['name' => 'PSIKOLOGI PENDIDIKAN', 'level' => 'S3', 'code' => 'PSIP'],
                ]
            ],
            [
                'name' => 'SEKOLAH PASCASARJANA',
                'code' => 'SPs',
                'departments' => [
                    ['name' => 'PENDIDIKAN DASAR', 'level' => 'S2,S3', 'code' => 'PDas'],
                    ['name' => 'PENDIDIKAN KEJURUAN', 'level' => 'S2,S3', 'code' => 'PKej'],
                    ['name' => 'PENDIDIKAN PROFESI GURU', 'level' => 'PROFESI', 'code' => 'PPG'],
                ]
            ],
            [
                'name' => 'FAKULTAS KEDOKTERAN (FK)',
                'code' => 'FK',
                'departments' => [
                    ['name' => 'KEDOKTERAN', 'level' => 'S1', 'code' => 'KED'],
                    ['name' => 'PENDIDIKAN PROFESI DOKTER', 'level' => 'PROFESI', 'code' => 'PPD'],
                ]
            ],
            [
                'name' => 'FAKULTAS VOKASI (FV)',
                'code' => 'FV',
                'departments' => [
                    ['name' => 'PERPUSTAKAAN DIGITAL', 'level' => 'D IV', 'code' => 'PD'],
                    ['name' => 'ANIMASI', 'level' => 'D IV', 'code' => 'ANI'],
                    ['name' => 'MANAJEMEN PEMASARAN', 'level' => 'D IV', 'code' => 'MP-FV'],
                    ['name' => 'AKUNTANSI', 'level' => 'D IV', 'code' => 'AKT'],
                    ['name' => 'TEKNOLOGI REKAYASA OTOMOTIF', 'level' => 'D IV', 'code' => 'TRO'],
                    ['name' => 'TEKNOLOGI REKAYASA DAN PEMELIHARAAN BANGUNAN SIPIL', 'level' => 'D IV', 'code' => 'TRPBS'],
                    ['name' => 'TEKNOLOGI REKAYASA MANUFAKTUR', 'level' => 'D IV', 'code' => 'TRM'],
                    ['name' => 'TEKNOLOGI REKAYASA PEMBANGKIT ENERGI', 'level' => 'D IV', 'code' => 'TRPE'],
                    ['name' => 'TEKNOLOGI REKAYASA SISTEM INFORMATIKA', 'level' => 'D IV', 'code' => 'TRSI'],
                    ['name' => 'TATA BOGA', 'level' => 'D IV', 'code' => 'TBoga'],
                    ['name' => 'DESAIN MODE', 'level' => 'D IV', 'code' => 'DM'],
                ]
            ],
        ];
    }
}