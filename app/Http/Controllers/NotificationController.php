<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function index()
    
    {
        // Récupérez les notifications de l'utilisateur authentifié
        return response()->json(Auth::user()->notifications); // Assurez-vous que votre modèle User a une relation 'notifications'

        // $notifications = Notification::all();
        // return response()->json($places);
    }
}
