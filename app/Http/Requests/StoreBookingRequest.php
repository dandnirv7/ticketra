<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kursi_ids') && is_string($this->input('kursi_ids'))) {
            $decoded = json_decode($this->input('kursi_ids'), true);

            if (is_array($decoded)) {
                $this->merge([
                    'kursi_ids' => $decoded
                ]);
            }
        }

        if ($this->has('snack_ids') && is_string($this->input('snack_ids'))) {
            $decoded = json_decode($this->input('snack_ids'), true);

            if (is_array($decoded)) {
                $this->merge([
                    'snack_ids' => $decoded
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kursi_ids' => ['required', 'array', 'min:1', 'max:6'],
            'kursi_ids.*' => ['required', 'uuid', 'exists:kursis,id'],
            'snack_ids' => ['sometimes', 'array', 'max:10'],
            'snack_ids.*' => ['uuid', 'exists:snacks,id'],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'kursi_ids.required' => 'Silakan pilih minimal 1 kursi.',
            'kursi_ids.array' => 'Format data kursi tidak valid.',
            'kursi_ids.min' => 'Silakan pilih minimal 1 kursi.',
            'kursi_ids.max' => 'Anda hanya dapat memesan maksimal 6 kursi dalam satu transaksi.',
            'kursi_ids.*.exists' => 'Terjadi kesalahan: Salah satu kursi tidak valid atau tidak ditemukan.',
        ];
    }
}
