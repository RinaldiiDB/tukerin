<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBottleTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Ignore the current bottle type's own barcode when checking uniqueness
        $bottleTypeId = $this->route('bottle_type');

        return [
            'name'         => 'required|string|max:255',
            'barcode'      => 'required|string|max:255|unique:bottle_types,barcode,' . $bottleTypeId,
            'description'  => 'nullable|string',
            'points_value' => 'required|integer|min:1',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'Nama jenis botol wajib diisi.',
            'barcode.required'      => 'Kode barcode wajib diisi.',
            'barcode.unique'        => 'Barcode ini sudah terdaftar di sistem.',
            'points_value.required' => 'Nilai poin wajib diisi.',
            'points_value.integer'  => 'Nilai poin harus berupa angka.',
            'points_value.min'      => 'Nilai poin minimal adalah 1.',
        ];
    }
}
