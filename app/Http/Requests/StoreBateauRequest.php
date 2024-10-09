<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBateauRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules()
    {
        return [
            'libelle' => 'required|string|max:255', // Champ requis, chaîne de caractères, max 255 caractères
            'description' => 'required|string', // Champ requis, doit être du texte
            'statut' => 'boolean', // Champ optionnel, doit être un booléen
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'statut.boolean' => 'Le statut doit être vrai ou faux.',
        ];
    }
}
