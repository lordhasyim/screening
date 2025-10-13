<?php
// database/seeders/CleanupDuplicatesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Faculty;
use App\Models\Department;

class CleanupDuplicatesSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🧹 Cleaning up duplicate faculties and departments...');
        
        DB::beginTransaction();
        
        try {
            // Valid department codes from UMFacultyDepartmentSeeder
            $validDeptCodes = [
                'BK', 'TP', 'AP', 'PLS', 'PGPAUD', 'PLB', 'TEP', 'MP-FIP', 'PAUD', 'PK',
                'PBSID', 'BSI', 'IP-FS', 'PBI', 'PBA', 'PBJ', 'PBM', 'PSR', 'PSTM', 'DKV', 
                'PBIND', 'KB', 'KBA', 'KSR', 'PMAT', 'MAT', 'PFIS', 'FIS', 'PKIM', 'KIM', 
                'PBIO', 'BIO', 'BIOTEK', 'PIPA', 'GZ', 'FAR', 'PTN', 'PAP', 'MNJ', 'PAK', 
                'PBMN', 'PE', 'EP', 'IE', 'IM', 'PTM', 'PTO', 'TM', 'TI', 'PTB', 'TS', 'PTI', 
                'PTE', 'TIF', 'TE', 'PTBoga', 'PTBus', 'TEI', 'PJKR', 'IKM', 'IKOR', 'PKO', 
                'POR', 'PPKN', 'PGEO', 'GEO', 'PSej', 'SEJ', 'PIPS', 'PSos', 'IKOM', 'PSI', 
                'PSIP', 'PDas', 'PKej', 'PPG', 'KED', 'PPD', 'PD', 'ANI', 'MP-FV', 'AKT', 
                'TRO', 'TRPBS', 'TRM', 'TRPE', 'TRSI', 'TBoga', 'DM'
            ];
            
            $validFacultyCodes = [
                'FIP', 'FS', 'FMIPA', 'FEB', 'FT', 'FIK', 
                'FIS', 'FPsi', 'SPs', 'FK', 'FV'
            ];
            
            // Department name mapping (old name pattern -> new department code)
            $deptMapping = [
                'PENDIDIKAN GURU PENDIDIKAN ANAK USIA DINI' => 'PGPAUD',
                'PENDIDIKAN GURU SEKOLAH DASAR' => 'PGPAUD', // No exact match, closest
                'SASTRA INDONESIA' => 'BSI',
                'SASTRA INGGRIS' => 'PBI',
                'SASTRA JERMAN' => 'PBJ',
                'SASTRA ARAB' => 'PBA',
                'PENDIDIKAN BAHASA INGGRIS' => 'PBI',
                'SENI DAN DESAIN' => 'DKV',
                'PENDIDIKAN MATEMATIKA' => 'PMAT',
                'PENDIDIKAN FISIKA' => 'PFIS',
                'PENDIDIKAN KIMIA' => 'PKIM',
                'PENDIDIKAN BIOLOGI' => 'PBIO',
                'PENDIDIKAN EKONOMI' => 'PE',
                'PENDIDIKAN AKUNTANSI' => 'PAK',
                'PENDIDIKAN SEJARAH' => 'PSej',
                'PENDIDIKAN GEOGRAFI' => 'PGEO',
                'PENDIDIKAN SOSIOLOGI' => 'PSos',
                'ILMU PERPUSTAKAAN' => 'IP-FS',
            ];
            
            // 1. Get old departments with quiz responses
            $oldDepts = DB::table('departments')
                ->whereNotIn('code', $validDeptCodes)
                ->get(['id', 'name', 'code', 'faculty_id']);
            
            if ($oldDepts->count() > 0) {
                $this->command->warn("\n📦 Found {$oldDepts->count()} old/duplicate departments:");
                
                $migratedCount = 0;
                $skippedCount = 0;
                
                foreach ($oldDepts as $oldDept) {
                    // Check if this department has quiz responses
                    $responseCount = DB::table('quiz_responses')
                        ->where('department_id', $oldDept->id)
                        ->count();
                    
                    $this->command->line("  - [ID: {$oldDept->id}] [{$oldDept->code}] {$oldDept->name}");
                    
                    if ($responseCount > 0) {
                        $this->command->warn("    ⚠️  Has {$responseCount} quiz responses - attempting to migrate...");
                        
                        // Find matching new department
                        $newDeptCode = null;
                        $upperOldName = strtoupper(trim($oldDept->name));
                        
                        // Try exact match first
                        if (isset($deptMapping[$upperOldName])) {
                            $newDeptCode = $deptMapping[$upperOldName];
                        } else {
                            // Try fuzzy match by searching for keywords
                            foreach ($deptMapping as $pattern => $code) {
                                if (stripos($upperOldName, $pattern) !== false) {
                                    $newDeptCode = $code;
                                    break;
                                }
                            }
                        }
                        
                        if ($newDeptCode) {
                            // Find the new department
                            $newDept = DB::table('departments')
                                ->where('code', $newDeptCode)
                                ->first();
                            
                            if ($newDept) {
                                // Migrate quiz responses
                                DB::table('quiz_responses')
                                    ->where('department_id', $oldDept->id)
                                    ->update(['department_id' => $newDept->id]);
                                
                                $this->command->info("    ✅ Migrated {$responseCount} responses to [{$newDeptCode}] {$newDept->name}");
                                $migratedCount++;
                            } else {
                                $this->command->error("    ❌ Could not find new department with code: {$newDeptCode}");
                                $skippedCount++;
                            }
                        } else {
                            $this->command->error("    ❌ No matching new department found - KEEPING THIS DEPARTMENT");
                            $skippedCount++;
                        }
                    }
                }
                
                $this->command->newLine();
                $this->command->info("📊 Migration Summary:");
                $this->command->info("   - Departments migrated: {$migratedCount}");
                $this->command->info("   - Departments skipped (no match): {$skippedCount}");
                $this->command->newLine();
                
                // Now delete old departments that have no quiz responses
                $deletedDepts = DB::table('departments')
                    ->whereNotIn('code', $validDeptCodes)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('quiz_responses')
                            ->whereColumn('quiz_responses.department_id', 'departments.id');
                    })
                    ->delete();
                
                $this->command->info("✅ Deleted {$deletedDepts} old departments (with no quiz responses)\n");
            } else {
                $this->command->info("✅ No duplicate departments found\n");
            }
            
            // 2. Clean up faculties (only those with no departments)
            $facToDelete = DB::table('faculties')
                ->whereNotIn('code', $validFacultyCodes)
                ->get(['id', 'name', 'code']);
            
            if ($facToDelete->count() > 0) {
                $this->command->warn("🏛️  Found {$facToDelete->count()} old/duplicate faculties:");
                foreach ($facToDelete as $fac) {
                    $deptCount = DB::table('departments')->where('faculty_id', $fac->id)->count();
                    $this->command->line("  - [ID: {$fac->id}] [{$fac->code}] {$fac->name} ({$deptCount} departments)");
                }
                
                // Delete faculties with no departments
                $deletedFacs = DB::table('faculties')
                    ->whereNotIn('code', $validFacultyCodes)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('departments')
                            ->whereColumn('departments.faculty_id', 'faculties.id');
                    })
                    ->delete();
                
                $this->command->info("✅ Deleted {$deletedFacs} old faculties (with no departments)\n");
            } else {
                $this->command->info("✅ No duplicate faculties found\n");
            }
            
            DB::commit();
            
            // Show final stats
            $finalFaculties = Faculty::count();
            $finalDepartments = Department::count();
            
            $this->command->info("📊 Final Statistics:");
            $this->command->info("   - Total Faculties: {$finalFaculties}");
            $this->command->info("   - Total Departments: {$finalDepartments}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Cleanup failed: " . $e->getMessage());
            throw $e;
        }
    }
}