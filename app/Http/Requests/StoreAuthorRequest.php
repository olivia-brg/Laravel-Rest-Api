<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // $this->route('author') pour récupérer l'id de l'auteur
            // permet d'update l'auteur avec le même nom, sans que la validation bloque
            'name' => [ Rule::unique('authors', 'name')->ignore($this->route('author')),'required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'biography' => 'nullable|string',
            'nationality' => 'nullable|string',
        ];
    }
}
