<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;

class NotificationsDropdown extends Component
{
    public function getNotificationsProperty()
    {
        return auth()->check() 
            ? auth()->user()->unreadNotifications()->take(10)->get() 
            : collect();
    }

    public function markAsRead($id)
    {
        if (!auth()->check()) return;

        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        if (!auth()->check()) return;

        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown', [
            'notifications' => $this->notifications,
            'unreadCount' => auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
        ]);
    }
}
