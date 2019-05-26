<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;

use Auth;

class NotificationController extends Controller
{
    //devuelve solo las notificaciones que coincidan 
    //con el dia actual,  para el usuario autenticado
    public function get(){

        $unreadNotifications = Auth::user()->unreadNotifications;
        $currentDate = date('Y-m-d');
        foreach ($unreadNotifications as $notification) {
            // si esa notificaion no leida no coincide con el dia actual, se marca como leida
            if($currentDate != $notification->created_at->toDateString()){
                $notification->markAsRead();
            }
        }
        return Auth::user()->unreadNotifications;
    }
}
