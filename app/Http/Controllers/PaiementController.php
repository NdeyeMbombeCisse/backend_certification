<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use App\Models\Paiement;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;

class PaymentController extends Controller
{
    public function payment()
    {
        $client = new Client();
        $url = 'https://api.naboopay.com/api/v1/account/';

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer naboo-5b3c904b-0dcc-4095-bee2-f1f76ee76696.d5d2ab5a-0adb-4859-8dd7-e7c83b163c9b',
        ];

        try {
            $response = $client->request('GET', $url, [
                'headers' => $headers,
            ]);

            // Récupérer le corps de la réponse
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Passer les données à la vue
            return view('welcome', compact('responseBody'));

        } catch (RequestException $e) {
            // Gérer l'erreur
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    public function createPayment(Request $request)
    {
        $client = new Client();
        $url = 'https://api.naboopay.com/api/v1/transaction/create-transaction';
    
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer naboo-5b3c904b-0dcc-4095-bee2-f1f76ee76696.d5d2ab5a-0adb-4859-8dd7-e7c83b163c9b',
        ];
    
        // Préparer le corps de la requête
        $body = [
            "method_of_payment" => [$request->input('method_of_payment')], // Forcer à tableau
            "products" => $request->input('products'), // Assurez-vous que ça inclut category
            "is_escrow" => $request->input('is_escrow'),
            "success_url" => $request->input('success_url'),
            "error_url" => $request->input('error_url'),
        ];
    
        try {
            $response = $client->request('PUT', $url, [
                'headers' => $headers,
                'json' => $body,
            ]);
    
            return $response->getBody()->getContents();
    
        } catch (RequestException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    

}

