<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Phq9FormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phq9' => 'required|array|size:9',
            'phq9.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
        ];
    }

    public function messages(): array
    {
        return [
            'phq9.required' => 'Semua pertanyaan PHQ-9 harus dijawab.',
            'phq9.size' => 'Harus ada tepat 9 jawaban PHQ-9.',
            'phq9.*.required' => 'Semua pertanyaan harus dijawab.',
            'phq9.*.in' => 'Pilihan jawaban tidak valid.',
        ];
    }
}
