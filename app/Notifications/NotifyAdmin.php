<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyAdmin extends Notification
{
    use Queueable;
    public $Global_Data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Array $data)
    {
        $this->Global_Data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    // FUncion necesaria para el envio de notificaciones via base de datos
    public function toDatabase($notifiable){
         
        return ['data' => $this->Global_Data
        ];
    }

    //Funcion para enviar las notificaciones en broadcast a todos los usuarios:
    public function toBroadcast($notifiable){
        /* Aqui, los datos se envian en el segundo objeto, para poder mostrar un unico
        resutlado en la vista, Notification.vue, cuando los datos se obtienen de la BD,
        la tabla notifications, almacena un objeto llamado "data", dentro de una columna
        llamada "data" tambien, en la vista, se tendria que mostrar: data.data...
        
        en este caso en broadcast no es asi, por ende tiene que enviarse un doble arreglo
        para que en la vista se pueda acceder a el como data.data de igual forma*/
        return [
            'data' => [
                'data' => $this->Global_Data
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
