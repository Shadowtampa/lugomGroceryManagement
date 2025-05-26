<?php

namespace App\Http\Requests\Inventory;

use App\Enums\UnidadeMedida;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Family;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Garante que apenas usuários autenticados possam criar Products
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stock' => 'required|numeric|min:0',
            'desirable_stock' => 'required|numeric|min:0',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->route('id')
        ]);
    }
}
