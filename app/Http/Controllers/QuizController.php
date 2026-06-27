<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Province;
use App\Models\City;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    // Show welcome/landing page
    public function index()
    {
        $faculties = Faculty::with('departments')->orderBy('name')->get();

        return view('quiz.index', compact('faculties'));
    }

    // Show identity form (first step)
    public function identity()
    {
        // Clear previous quiz session so shared devices always start clean
        session()->forget('quiz_response_id');

        try {
            $faculties = Faculty::with('departments')->orderBy('name')->get();
            $provinces = Province::whereNull('removed_at')->orderBy('name')->get();

            return view('quiz.identity', compact('faculties', 'provinces'));
        } catch (\Exception $e) {
            Log::error('Error loading identity form: ' . $e->getMessage());
            return redirect()->route('quiz.index')
                ->with('error', 'Terjadi kesalahan saat memuat form. Silakan coba lagi.');
        }
    }

    // Process identity and redirect to PHQ-9
    public function submitIdentity(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
                'faculty_id' => 'required|exists:faculties,id',
                'department_id' => $request->department_id === 'other' ? ['nullable'] : [
                    'required',
                    'exists:departments,id',
                    Rule::exists('departments', 'id')->where(function ($query) use ($request) {
                        $query->where('faculty_id', $request->faculty_id);
                    })
                ],
                'department_name' => $request->department_id === 'other'
                    ? ['required', 'string', 'max:150']
                    : ['nullable'],
                'education_level' => $request->department_id === 'other'
                    ? ['nullable']
                    : ['required', Rule::in(['D4', 'S1', 'Pascasarjana'])],
                'nim' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[0-9]+$/'
                ],
                'full_name' => 'required|string|max:255|min:2',
                'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
                'birth_place' => 'required|string|max:100|min:2',
                'birth_date' => 'required|date|before:today|after:1950-01-01',
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^[0-9]+$/',
                ],
                'address' => 'required|string|min:10',
                'living_arrangement' => ['required', Rule::in(['Kos', 'Rumah orang tua', 'Rumah keluarga', 'Asrama', 'Kontrak'])],
                'origin_province_id' => 'required|exists:provinces,id',
                'origin_city_id' => 'required|exists:cities,id',
                'origin_area_type' => ['required', Rule::in(['perkotaan', 'pedesaan', 'pinggiran kota', 'daerah terpencil', 'daerah industri'])],
                'email' => 'nullable|email|max:255',
                'religion' => 'required|string|max:50',
                'parents_marital_status' => ['required', Rule::in(['menikah', 'cerai hidup', 'cerai mati', 'pisah tidak resmi', 'menikah lagi'])],
                'child_order' => 'required|integer|min:1|max:20',
                'siblings_count' => 'required|integer|min:1|max:20',
                'scholarship' => 'nullable|string|max:100',
                'admission_path' => 'required|string|max:50',
                'parents_education' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'])],
                'parents_income' => ['required', Rule::in(['<2000000', '2000000-5000000', '5000000-10000000', '>10000000'])],
                'family_members_count' => 'required|integer|min:1|max:20',

                // Medical History (optional fields)
                'has_chronic_disease' => 'nullable|boolean',
                'chronic_disease_details' => 'nullable|string|max:500',
                'current_medication' => 'nullable|boolean',
                'medication_details' => 'nullable|string|max:500',
                'head_injury_history' => 'nullable|boolean',
                'injury_details' => 'nullable|string|max:500',
                'substance_use' => ['required', Rule::in(['Tidak Pernah', 'Pernah', 'Masih aktif'])],
                'substance_details' => 'nullable|string|max:500',
                'psychological_treatment_history' => 'nullable|boolean',
                'treatment_details' => 'nullable|string|max:500',
                'family_mental_health_history' => 'nullable|boolean',
                'family_history_details' => 'nullable|string|max:500',
                'family_relationship_description' => 'nullable|string|max:1000',
            ], [
                // Custom error messages
                'education_level.required' => 'Jenjang pendidikan harus dipilih.',
                'education_level.in' => 'Jenjang pendidikan tidak valid.',
                'department_id.exists' => 'Jurusan tidak sesuai dengan fakultas yang dipilih.',
                'nim.regex' => 'NIM hanya boleh berisi angka.',
                'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx',
                'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
                'birth_date.after' => 'Tanggal lahir tidak valid.',
                'child_order.max' => 'Anak ke- maksimal 20.',
                'siblings_count.max' => 'Jumlah saudara maksimal 20.',
                'family_members_count.max' => 'Jumlah anggota keluarga maksimal 20.',
                'address.min' => 'Alamat minimal 10 karakter.',
                'full_name.min' => 'Nama lengkap minimal 2 karakter.',
                'birth_place.min' => 'Tempat lahir minimal 2 karakter.',
            ]);

            // Resolve 'other' department selection
            if ($request->department_id === 'other') {
                $validated['department_id'] = null;
                $validated['department_name'] = $request->department_name;
                $validated['education_level'] = null;
            } else {
                $validated['department_name'] = null;
            }

            // Check if selected level is available for the department
            $department = isset($validated['department_id'])
                ? Department::find($validated['department_id'])
                : null;
            if ($department && !$department->hasLevel($validated['education_level'])) {
                return back()->withErrors([
                    'education_level' => 'Jenjang pendidikan tidak tersedia untuk jurusan ini.'
                ])->withInput();
            }

            // Additional validation logic
            if ($validated['child_order'] > $validated['siblings_count']) {
                return back()->withErrors([
                    'child_order' => 'Anak ke- tidak boleh lebih besar dari jumlah saudara.'
                ])->withInput();
            }

            // Get province and city names for storing
            $province = Province::find($validated['origin_province_id']);
            $city = City::find($validated['origin_city_id']);

            if (!$province || !$city) {
                return back()->withErrors([
                    'origin_province_id' => 'Provinsi atau kota/kabupaten tidak valid.'
                ])->withInput();
            }

            // Verify city belongs to province
            if ($city->province_id != $province->id) {
                return back()->withErrors([
                    'origin_city_id' => 'Kota/kabupaten tidak sesuai dengan provinsi yang dipilih.'
                ])->withInput();
            }

            // Add the names to validated data
            $validated['origin_province'] = $province->name;

            // Remove IDs since we're not storing them in the main table
            unset($validated['origin_province_id'], $validated['origin_city_id']);

            // Convert boolean checkboxes to proper boolean values
            $booleanFields = [
                'has_chronic_disease',
                'current_medication',
                'head_injury_history',
                'psychological_treatment_history',
                'family_mental_health_history'
            ];

            foreach ($booleanFields as $field) {
                $validated[$field] = isset($validated[$field]) && $validated[$field] ? true : false;
            }

            // Begin transaction for data integrity
            DB::beginTransaction();

            try {
                $existingResponse = QuizResponse::where('nim', $validated['nim'])->first();

                if ($existingResponse) {
                    if ($existingResponse->quiz_status === 'completed') {
                        DB::rollBack();
                        return back()
                            ->withErrors(['nim' => 'NIM ini sudah menyelesaikan skrining kesehatan mental. Hubungi admin jika ada kesalahan.'])
                            ->withInput();
                    }
                    // Incomplete — overwrite identity data and reset quiz progress
                    $existingResponse->update([
                        ...$validated,
                        'quiz_status' => 'started',
                        'started_at' => now(),
                        'phq9_responses' => null,
                        'dass21_responses' => null,
                        'phq9_total_score' => null,
                        'phq9_category' => null,
                        'dass21_total_score' => null,
                        'dass21_category' => null,
                        'overall_risk_level' => null,
                        'phq9_passed_threshold' => null,
                        'needs_dass21' => null,
                        'phq9_completed_at' => null,
                        'dass21_completed_at' => null,
                        'completed_at' => null,
                    ]);
                    $quizResponse = $existingResponse;
                } else {
                    $quizResponse = QuizResponse::create([
                        ...$validated,
                        'quiz_status' => 'started',
                        'started_at' => now(),
                    ]);
                }

                // Store quiz ID in session and redirect to PHQ-9
                session(['quiz_response_id' => $quizResponse->id]);

                DB::commit();

                return redirect()->route('quiz.phq9')
                    ->with('success', 'Identitas berhasil disimpan. Lanjut ke pertanyaan skrining.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating quiz response: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error in submitIdentity: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')->withInput();
        }
    }

    // Show PHQ-9 questions
    public function phq9()
    {
        $quizResponseId = session('quiz_response_id');
        if (! $quizResponseId) {
            return redirect()->route('quiz.identity')
                ->with('error', 'Silakan isi identitas terlebih dahulu.');
        }

        try {
            $quizResponse = QuizResponse::findOrFail($quizResponseId);
            $phq9Questions = $this->getPhq9Questions();

            return view('quiz.phq9', compact('quizResponse', 'phq9Questions'));
        } catch (\Exception $e) {
            Log::error('Error loading PHQ-9: ' . $e->getMessage());
            session()->forget('quiz_response_id');
            return redirect()->route('quiz.identity')
                ->with('error', 'Sesi telah berakhir. Silakan mulai dari awal.');
        }
    }

    // Process PHQ-9 responses
    public function submitPhq9(Request $request)
    {
        $quizResponseId = session('quiz_response_id');
        if (!$quizResponseId) {
            return redirect()->route('quiz.identity')
                ->with('error', 'Sesi telah berakhir. Silakan mulai dari awal.');
        }

        try {
            $quizResponse = QuizResponse::findOrFail($quizResponseId);

            // Validate PHQ-9 responses
            $request->validate([
                'phq9' => 'required|array|size:9',
                'phq9.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
            ], [
                'phq9.required' => 'Semua pertanyaan PHQ-9 harus dijawab.',
                'phq9.size' => 'Harus ada tepat 9 jawaban PHQ-9.',
                'phq9.*.required' => 'Semua pertanyaan harus dijawab.',
                'phq9.*.in' => 'Pilihan jawaban tidak valid.',
            ]);

            // Update quiz response with PHQ-9 data
            $quizResponse->update([
                'phq9_responses' => $request->phq9,
                'quiz_status' => 'phq9_completed',
                'phq9_completed_at' => now(),
            ]);

            // Check if should continue to DASS-21
            if ($quizResponse->shouldContinueToDass21()) {
                return redirect()->route('quiz.dass21')
                    ->with('info', 'Berdasarkan hasil PHQ-9, Anda perlu melanjutkan ke tahap berikutnya.');
            }

            // Stop here - show result without DASS-21
            $quizResponse->update([
                'quiz_status' => 'completed',
                'completed_at' => now(),
            ]);

            return redirect()->route('quiz.result', $quizResponse->id)
                ->with('success', 'Skrining selesai!');
        } catch (\Exception $e) {
            Log::error('Error in submitPhq9: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.')->withInput();
        }
    }

    // Show DASS-21 questions
    public function dass21()
    {
        $quizResponseId = session('quiz_response_id');
        if (! $quizResponseId) {
            return redirect()->route('quiz.identity')
                ->with('error', 'Silakan mulai dari awal.');
        }

        try {
            $quizResponse = QuizResponse::findOrFail($quizResponseId);

            // Check if they should be here
            if (! $quizResponse->shouldContinueToDass21()) {
                return redirect()->route('quiz.result', $quizResponse->id)
                    ->with('info', 'Anda tidak perlu melanjutkan ke DASS-21.');
            }

            $dass21Questions = $this->getDass21Questions();

            return view('quiz.dass21', compact('quizResponse', 'dass21Questions'));
        } catch (\Exception $e) {
            Log::error('Error loading DASS-21: ' . $e->getMessage());
            session()->forget('quiz_response_id');
            return redirect()->route('quiz.identity')
                ->with('error', 'Sesi telah berakhir. Silakan mulai dari awal.');
        }
    }

    // Process DASS-21 responses
    public function submitDass21(Request $request)
    {
        $quizResponseId = session('quiz_response_id');
        if (!$quizResponseId) {
            return redirect()->route('quiz.identity')
                ->with('error', 'Sesi telah berakhir. Silakan mulai dari awal.');
        }

        try {
            $quizResponse = QuizResponse::findOrFail($quizResponseId);

            // Validate DASS-21 responses
            $request->validate([
                'dass21' => 'required|array|size:30',
                'dass21.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
            ], [
                'dass21.required' => 'Semua pertanyaan DASS-21 harus dijawab.',
                'dass21.size' => 'Harus ada tepat 30 jawaban DASS-21.',
                'dass21.*.required' => 'Semua pertanyaan harus dijawab.',
                'dass21.*.in' => 'Pilihan jawaban tidak valid.',
            ]);

            // Update quiz response with DASS-21 data and mark complete
            $quizResponse->update([
                'dass21_responses' => $request->dass21,
                'quiz_status' => 'completed',
                'dass21_completed_at' => now(),
                'completed_at' => now(),
            ]);

            return redirect()->route('quiz.result', $quizResponse->id)
                ->with('success', 'Skrining lengkap selesai!');
        } catch (\Exception $e) {
            Log::error('Error in submitDass21: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.')->withInput();
        }
    }

    // Show result
    public function result(QuizResponse $quizResponse)
    {
        // Clear session
        session()->forget('quiz_response_id');

        return view('quiz.result', compact('quizResponse'));
    }

    // Get PHQ-9 questions
    private function getPhq9Questions()
    {
        return [
            'Kurang tertarik atau bergairah dalam melakukan apapun',
            'Merasa sedih, murung, kesepian, atau putus asa',
            'Sulit tidur atau mudah terbangun, atau terlalu banyak tidur',
            'Merasa lelah atau kurang bertenaga',
            'Kurang nafsu makan atau terlalu banyak makan',
            'Mudah merasa cemas dan gelisah pada situasi tertentu',
            'Sulit mempertahankan konsentrasi saat berkegiatan',
            'Merasa hidup tidak berarti, tidak berharga, tidak layak, atau tidak berguna',
            'Merasa tidak mendapatkan dukungan sosial dari lingkungan (Orang tua, teman, pasangan atau lainnya)',
        ];
    }

    // Get DASS-21 Extended questions (30 questions)
    private function getDass21Questions()
    {
        return [
            'Saya merasa bahwa diri saya menjadi marah karena hal-hal sepele',
            'Saya sama sekali tidak dapat merasakan perasaan positif',
            'Saya mengalami kesulitan bernafas (misalnya: sering kali terengah-engah atau tidak dapat bernafas padahal tidak melakukan aktivitas fisik sebelumnya)',
            'Saya sepertinya tidak kuat lagi untuk melakukan suatu kegiatan.',
            'Saya cenderung bereaksi berlebihan terhadap suatu situasi.',
            'Saya merasa gemetar (misalnya: pada tangan)',
            'Saya merasa telah menghabiskan banyak energi disaat merasa cemas.',
            'Saya merasa khawatir dengan situasi dimana saya mungkin menjadi panik dan mempermalukan diri sendiri.',
            'Saya merasa tidak ada hal yang dapat diharapkan di masa depan',
            'Saya mudah merasa gelisah',
            'Saya merasa sulit untuk bersantai',
            'Saya tidak merasa antusias dalam hal apapun.',
            'Saya merasa bahwa saya tidak berharga sebagai seorang manusia',
            'Saya merasa bahwa saya mudah tersinggung',
            'Saya menyadari perubahan detak jantung, walaupun tidak sehabis melakukan aktivitas fisik (misalnya: merasa detak jantung meningkat atau melemah).',
            'Saya merasa takut tanpa alasan yang jelas',
            'Saya mengalami perubahan suasana hati secara tiba-tiba tanpa alasan yang jelas',
            'Saya masih merasa sangat sedih atau sulit menerima kenyataan setelah kehilangan orang yang saya cintai (karena kematian, perceraian, atau perpisahan)',
            'Saya memiliki keinginan untuk menyakiti diri sendiri saat merasa sangat sedih atau tertekan',
            'Saya pernah mengalami keluhan di tubuh yang muncul saat banyak pikiran',
            'Saya merasa sulit mengendalikan emosi, seperti mudah marah, tersinggung, atau menangis tanpa alasan yang jelas',
            'Saya merasa tidak stabil secara emosional dan sulit menenangkan diri saat menghadapi masalah kecil',
            'Saya pernah mengalami suatu kejadian traumatik di masa lalu yang membuat saya masih terbayang kejadian tersebut sampai saat ini (Misalnya pelecehan, kekerasan fisik, kekerasan verbal, dll.)',
            'Saya memiliki konflik berkepanjangan dengan teman, keluarga, ataupun pasangan',
            'Saya kesulitan dalam mempertahankan relasi dengan teman ataupun pasangan',
            'Saya memiliki perasaan hampa yang sudah lama dirasakan',
            'Saya sering merasa curiga dengan orang lain',
            'Saya melakukan banyak usaha agar tidak diabaikan oleh orang lain lain yang signifikan bagi saya',
            'Saya memiliki perilaku impulsif seperti perilaku seksual berbahaya, pemakaian zat adiktif, kebut-kebutan, atau makan dalam jumlah sangat banyak',
            'Saya pernah memiliki pemikiran untuk mengakhiri hidup',
        ];
    }

    // API endpoint to get departments by faculty
    public function getDepartments($facultyId)
    {
        try {
            $departments = Department::where('faculty_id', $facultyId)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($departments);
        } catch (\Exception $e) {
            Log::error('Error getting departments: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data jurusan'], 500);
        }
    }

    // API endpoint to get provinces
    public function getProvinces()
    {
        try {
            $provinces = Province::whereNull('removed_at')
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($provinces);
        } catch (\Exception $e) {
            Log::error('Error getting provinces: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data provinsi'], 500);
        }
    }

    // API endpoint to get cities by province
    public function getCities($provinceId)
    {
        try {
            $cities = City::where('province_id', $provinceId)
                ->whereNull('removed_at')
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Error getting cities: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data kota/kabupaten'], 500);
        }
    }

    // API endpoint to get all cities
    public function getAllCities()
    {
        try {
            $cities = City::whereNull('removed_at')
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Error getting all cities: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data kota/kabupaten'], 500);
        }
    }

    // API endpoint to get available education levels for a department
    public function getDepartmentLevels($departmentId)
    {
        try {
            $department = Department::findOrFail($departmentId);
            $levels = $department->getAvailableLevels();

            // Return levels with display names
            $levelsWithNames = array_map(function ($level) {
                return [
                    'value' => $level,
                    'label' => Department::getLevelDisplayName($level)
                ];
            }, $levels);

            return response()->json($levelsWithNames);
        } catch (\Exception $e) {
            Log::error('Error getting department levels: ' . $e->getMessage());
            return response()->json(['error' => 'Department not found'], 404);
        }
    }
}