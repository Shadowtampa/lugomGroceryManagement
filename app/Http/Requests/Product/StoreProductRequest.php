<?php

namespace App\Http\Requests\Product;

use App\Enums\UnidadeMedida;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Family;

class StoreProductRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'preco' => 'nullable|numeric|min:0',
            'quantidade_estoque' => 'required|integer|min:0',
            'foto' => 'nullable|string|max:255',
            'local_compra' => 'nullable|string|max:255',
            'local_casa' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'unidade_medida' => ['required', 'string', 'in:' . implode(',', array_column(UnidadeMedida::cases(), 'value'))]
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $family = Family::where('user_id', auth()->id())->firstOrFail();

        $this->merge([
            'families_id' => $family->id
        ]);
    }
}
