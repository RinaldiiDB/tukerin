<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isEmployee();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.bottle_type_id' => 'required|integer|exists:bottle_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Nasabah wajib diidentifikasi terlebih dahulu.',
            'user_id.exists' => 'Nasabah tidak valid.',
            'items.required' => 'Minimal harus ada 1 jenis botol yang ditambahkan.',
            'items.array' => 'Format data transaksi tidak valid.',
            'items.min' => 'Minimal harus ada 1 jenis botol yang ditambahkan.',
            'items.*.bottle_type_id.required' => 'Jenis botol wajib dipilih.',
            'items.*.bottle_type_id.exists' => 'Jenis botol tidak terdaftar.',
            'items.*.quantity.required' => 'Jumlah botol wajib diisi.',
            'items.*.quantity.min' => 'Jumlah botol minimal adalah 1.',
        ];
    }
}
