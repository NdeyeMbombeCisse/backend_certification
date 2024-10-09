<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrajetRequest extends FormRequest
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
            'date_depart' => 'required|date', // Doit être une date valide et est requis
            'date_arrivee' => 'required|date|after_or_equal:date_depart', // Doit être une date valide après ou égale à date_depart
            'lieu_depart' => 'required|string|max:255', // Requis, chaîne de caractères, max 255 caractères
            'lieu_arrive' => 'required|string|max:255', // Requis, chaîne de caractères, max 255 caractères
            'image' => 'required|string|max:255', // Requis, chaîne de caractères pour le chemin de l'image, max 255 caractères
            'statut' => 'boolean', // Optionnel, doit être un booléen
            'heure_embarquement' => 'required|string|max:5', // Requis, chaîne de caractères, format d'heure HH:MM
            'heure_depart' => 'required|string|max:5', // Requis, chaîne de caractères, format d'heure HH:MM
            'bateau_id' => 'required|exists:bateaus,id', // Requis, doit exister dans la table bateaus
        ];
    }

    public function messages()
    {
        return [
            'date_depart.required' => 'La date de départ est requise.',
            'date_depart.date' => 'La date de départ doit être une date valide.',
            'date_arrivee.required' => 'La date d\'arrivée est requise.',
            'date_arrivee.date' => 'La date d\'arrivée doit être une date valide.',
            'date_arrivee.after_or_equal' => 'La date d\'arrivée doit être postérieure ou égale à la date de départ.',
            'lieu_depart.required' => 'Le lieu de départ est requis.',
            'lieu_depart.string' => 'Le lieu de départ doit être une chaîne de caractères.',
            'lieu_depart.max' => 'Le lieu de départ ne peut pas dépasser 255 caractères.',
            'lieu_arrive.required' => 'Le lieu d\'arrivée est requis.',
            'lieu_arrive.string' => 'Le lieu d\'arrivée doit être une chaîne de caractères.',
            'lieu_arrive.max' => 'Le lieu d\'arrivée ne peut pas dépasser 255 caractères.',
            'image.required' => 'L\'image est requise.',
            'image.string' => 'L\'image doit être une chaîne de caractères.',
            'image.max' => 'L\'image ne peut pas dépasser 255 caractères.',
            'statut.boolean' => 'Le statut doit être vrai ou faux.',
            'heure_embarquement.required' => 'L\'heure d\'embarquement est requise.',
            'heure_embarquement.string' => 'L\'heure d\'embarquement doit être une chaîne de caractères.',
            'heure_embarquement.max' => 'L\'heure d\'embarquement doit être au format HH:MM.',
            'heure_depart.required' => 'L\'heure de départ est requise.',
            'heure_depart.string' => 'L\'heure de départ doit être une chaîne de caractères.',
            'heure_depart.max' => 'L\'heure de départ doit être au format HH:MM.',
            'bateau_id.required' => 'Le bateau est requis.',
            'bateau_id.exists' => 'Le bateau sélectionné n\'existe pas.',
        ];
    }
}



