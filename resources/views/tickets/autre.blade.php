<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .ticket {
            width: 100%;
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .ticket-header h1 {
            font-size: 24px;
            margin: 0;
        }
        .ticket-details {
            margin-bottom: 20px;
        }
        .ticket-details p {
            margin: 5px 0;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .qr-code img {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1>Ticket de Réservation</h1>
            <p>Réservation numero: {{ $reservation->id }}</p>
        </div>

        <div class="ticket-details">
            <p><strong>Nom du passager:</strong> {{ $reservation->no_connect->prenom }} {{ $reservation->no_connect->nom }}</p>
            <p><strong>Trajet:</strong> {{ $reservation->trajet->lieu_depart }} - {{ $reservation->trajet->lieu_arrive }}</p>
            <p><strong>date_depart:</strong> {{ $reservation->trajet->date_depart }} </p>
            <p><strong>Embarquement:</strong> {{ $reservation->trajet->heure_embarquement }} </p>
            <p><strong>Date de réservation:</strong> {{ $reservation->created_at->format('d-m-Y H:i:s') }}</p>

            <p><strong>heure_depart:</strong> {{ $reservation->trajet->heure_depart}} </p>
            <p><strong>Place:</strong> {{ $reservation->place->libelle }}</p>
            <p><strong>nationalite:</strong> {{ $reservation->no_connect->nationnalite }}</p>
            <p><strong>age:</strong> {{ $reservation->no_connect->age}} ans</p>
            <p><strong>CNI:</strong> 
    {{ $reservation->no_connect ? $reservation->no_connect->CNI : 'Non renseigné' }}
</p>
            <p><strong>Numero registre:</strong> 
    {{ $reservation->no_connect ? $reservation->no_connect->numero_registre : 'Non renseigné' }}
</p>            <p><strong>Tarif:</strong> {{ $reservation->tarif }}</p>
            <p><strong>Statut:</strong> {{ $reservation->statut ? 'confirmeé' : 'Non confirmeé' }}</p>

        </div>

        <div class="qr-code">
            <img src="{{ public_path($qrCodePath) }}" alt="QR Code">
        </div>
    </div>
</body>
</html>
