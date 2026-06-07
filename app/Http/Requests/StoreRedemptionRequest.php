<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRedemptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isUser();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'points_used' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $profile = auth()->user()->profile;
                    if (!$profile || $value > $profile->points_balance) {
                        $fail('Poin tidak mencukupi. Saldo poin Anda saat ini adalah ' . ($profile ? $profile->points_balance : 0) . ' poin.');
                    }
                },
            ],
            'method' => 'required|string|in:cash,ewallet',
            'bank_name' => 'required|string|max:100',
            'recipient_account' => 'required|string|max:100',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'points_used.required' => 'Poin yang ingin dicairkan wajib diisi.',
            'points_used.integer' => 'Poin harus berupa angka.',
            'points_used.min' => 'Minimal pencairan adalah 1 poin.',
            'method.required' => 'Metode pencairan wajib dipilih.',
            'method.in' => 'Metode pencairan tidak valid.',
            'bank_name.required' => 'Nama bank/e-wallet wajib diisi.',
            'recipient_account.required' => 'Nomor rekening/nomor e-wallet wajib diisi.',
        ];
    }
}
