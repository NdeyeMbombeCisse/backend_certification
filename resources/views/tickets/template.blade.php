<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .ticket-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .qr-code {
            display: block;
            margin: 20px auto;
            max-width: 300px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <h1>Votre Réservation</h1>
        <p><strong>ID de Réservation :</strong> {{ $ticketContent['reservation_id'] }}</p>
        <p><strong>Place :</strong> {{ $ticketContent['place'] }}</p>
        <p><strong>Utilisateur :</strong> {{ $ticketContent['user'] }}</p>
        <p><strong>Trajet :</strong> {{ $ticketContent['trajet'] }}</p>
        <p><strong>EMail:</strong> {{ $ticketContent['email'] }}</p>
        <p><strong>Telephone:</strong> {{ $ticketContent['telephone'] }}</p>
        <p><strong>CNI:</strong> {{ $ticketContent['CNI'] }}</p>
        <p><strong>Heure Embarquement:</strong> {{ $ticketContent['embarquement'] }}</p>
        <p><strong>Heure Depart:</strong> {{ $ticketContent['depart'] }}</p>

        <h2>QR Code</h2>
        <img class="qr-code" src="{{ $ticketContent['qr_code'] }}" alt="QR Code" />

        <div class="footer">
            <p>Merci d'avoir réservé avec nous!</p>
        </div>
    </div>
</body>
</html>
