<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdentityFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_year' => 'required|integer|min:2020|max:'.(date('Y') + 1),
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'nim' => 'required|string|max:50|unique:quiz_responses,nim',
            'full_name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'living_arrangement' => ['required', Rule::in(['Kos', 'Rumah orang tua', 'Rumah keluarga', 'Asrama', 'Kontrak'])],
            'origin_province' => 'required|string',
            'origin_area_type' => ['required', Rule::in(['perkotaan', 'pedesaan', 'pinggiran kota', 'daerah terpencil', 'daerah industri'])],
            'email' => 'nullable|email|max:255',
            'religion' => 'required|string',
            'parents_marital_status' => ['required', Rule::in(['menikah', 'cerai hidup', 'cerai mati', 'pisah tidak resmi', 'menikah lagi'])],
            'child_order' => 'required|integer|min:1',
            'siblings_count' => 'required|integer|min:1',
            'scholarship' => 'nullable|string',
            'admission_path' => 'required|string',
            'parents_education' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'])],
            'parents_income' => ['required', Rule::in(['<2000000', '2000000-5000000', '5000000-10000000', '>10000000'])],
            'family_members_count' => 'required|integer|min:1',

            // Medical History
            'has_chronic_disease' => 'boolean',
            'chronic_disease_details' => 'nullable|string',
            'current_medication' => 'boolean',
            'medication_details' => 'nullable|string',
            'head_injury_history' => 'boolean',
            'injury_details' => 'nullable|string',
            'substance_use' => ['required', Rule::in(['Tidak Pernah', 'Pernah', 'Masih aktif'])],
            'substance_details' => 'nullable|string',
            'psychological_treatment_history' => 'boolean',
            'treatment_details' => 'nullable|string',
            'family_mental_health_history' => 'boolean',
            'family_history_details' => 'nullable|string',
            'family_relationship_description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'student_year.required' => 'Tahun mahasiswa wajib diisi.',
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'department_id.required' => 'Jurusan wajib dipilih.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar dalam sistem.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'living_arrangement.required' => 'Tempat tinggal wajib dipilih.',
            'origin_province.required' => 'Asal provinsi wajib diisi.',
            'origin_area_type.required' => 'Tipe daerah asal wajib dipilih.',
            'religion.required' => 'Agama wajib diisi.',
            'parents_marital_status.required' => 'Status pernikahan orang tua wajib dipilih.',
            'child_order.required' => 'Anak ke berapa wajib diisi.',
            'siblings_count.required' => 'Jumlah saudara kandung wajib diisi.',
            'admission_path.required' => 'Jalur masuk wajib diisi.',
            'parents_education.required' => 'Pendidikan orang tua wajib dipilih.',
            'parents_income.required' => 'Penghasilan orang tua wajib dipilih.',
            'family_members_count.required' => 'Jumlah anggota keluarga wajib diisi.',
            'substance_use.required' => 'Riwayat penggunaan zat wajib dipilih.',
        ];
    }
}
