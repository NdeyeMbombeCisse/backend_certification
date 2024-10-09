<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
    
            return [
                'statut' => 'required|boolean',
                'trajet_id' => 'required|exists:trajets,id', // Vérifie que l'ID du trajet existe dans la table trajets
                'user_id' => 'required|exists:users,id', // Vérifie que l'ID de l'utilisateur existe dans la table users
                'place_id' => 'required|exists:places,id', // Vérifie que l'ID de la place existe dans la table places
            ];
        }
    
        public function messages()
        {
            return [
                'statut.required' => 'Le statut est requis.',
                'statut.boolean' => 'Le statut doit être vrai ou faux.',
                'trajet_id.required' => 'Le trajet est requis.',
                'trajet_id.exists' => 'Le trajet sélectionné n\'existe pas.',
                'user_id.required' => 'L\'utilisateur est requis.',
                'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
                'place_id.required' => 'La place est requise.',
                'place_id.exists' => 'La place sélectionnée n\'existe pas.',
            ];
        }
    }



