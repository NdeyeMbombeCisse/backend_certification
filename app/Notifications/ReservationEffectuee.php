<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationEffectuee extends Notification
{
    use Queueable;

    protected $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['database']; // Utiliser le canal de base de données
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Une nouvelle réservation a été effectuée.',
            'reservation_id' => $this->reservation->id,
            'details' => $this->reservation->details, // Assurez-vous que 'details' est une propriété valide
            // Ajoutez d'autres détails pertinents ici si nécessaire
        ];
    }
}
