<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        // return [
        //     'email' => 'required|email|unique:users,email',
        //     'prenom' => 'required|string|max:255',
        //     'image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        //     'nom' => 'required|string|max:255',
        //     'telephone' => 'required|string|unique:users,telephone',
        //     'numero_identite' =>'required|string|size:13',
        //     'nationnalite' => 'required|in:senegalais,etranger resident,etranger non resident',
        //     'password' => 'required|string|min:8|confirmed',
        // ];
    }

    
}
