<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCreated extends Notification
{
    use Queueable;

    protected $reservation;
    protected $ticketUrl;

  
    public function __construct($reservation, $ticketUrl)
    {
        $this->reservation = $reservation;
        $this->ticketUrl = $ticketUrl; 
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Bonjour!')
            ->line('Votre réservation a été créée avec succès.')
            ->action('Voir votre réservation', url()) 
            ->line('Vous pouvez télécharger votre ticket ici :')
            ->action('Télécharger le ticket', $this->ticketUrl) // Utilisez $this->ticketUrl
            ->line('Merci d\'avoir choisi notre service!');
    }

    public function toArray($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'message' => 'Votre réservation a été créée avec succès.',
        ];
    }
}
