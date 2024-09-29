<?php



namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentNotifcation extends Notification
{
    use Queueable;

    protected $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail']; // Vous pouvez ajouter d'autres canaux comme 'database' ou 'broadcast'
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Bonjour!')
            ->line('Votre réservation a été créée avec succès.')
            ->action('Voir votre réservation', url('/reservations/'.$this->reservation->id))
            ->line('Merci d\'avoir choisi notre service!');
    }

    // Si vous voulez stocker cette notification dans la base de données
    public function toArray($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'message' => 'Votre réservation a été créée avec succès.',
        ];
    }
}

