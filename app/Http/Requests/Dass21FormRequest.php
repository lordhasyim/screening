<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Dass21FormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dass21' => 'required|array|size:30',
            'dass21.*' => ['required', Rule::in(['Tidak Pernah', 'Kadang-Kadang', 'Sering', 'Sering Sekali'])],
        ];
    }

    public function messages(): array
    {
        return [
            'dass21.required' => 'Semua pertanyaan DASS-21 harus dijawab.',
            'dass21.size' => 'Harus ada tepat 30 jawaban DASS-21.',
            'dass21.*.required' => 'Semua pertanyaan harus dijawab.',
            'dass21.*.in' => 'Pilihan jawaban tidak valid.',
        ];
    }
}
