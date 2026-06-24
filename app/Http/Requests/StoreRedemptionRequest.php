<?php

namespace App\Http\Requests;

use App\Models\RedemptionRequest;
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

                    if (!$profile) {
                        $fail('Profil nasabah tidak ditemukan.');
                        return;
                    }

                    // Hitung total poin yang sedang dalam status pending
                    $pendingPoints = RedemptionRequest::where('user_id', auth()->id())
                        ->where('status', 'pending')
                        ->sum('points_used');

                    // Saldo tersedia = saldo asli - poin yang sedang pending
                    $availableBalance = $profile->points_balance - $pendingPoints;

                    if ($value > $availableBalance) {
                        $fail('Poin tidak mencukupi. Saldo tersedia Anda saat ini adalah ' . $availableBalance . ' poin (saldo ' . $profile->points_balance . ' poin dikurangi ' . $pendingPoints . ' poin yang sedang diproses).');
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

