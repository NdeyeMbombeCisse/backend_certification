<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{public function rules()
    {
        return [
            'titre' => 'required|string|max:255', // Requis, chaîne de caractères, max 255 caractères
            'description' => 'required|string', // Requis, doit être du texte
        ];
    }

    public function messages()
    {
        return [
            'titre.required' => 'Le titre est requis.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'description.string' => 'La description doit être une chaîne de caractères.',
        ];
    }

    
}
