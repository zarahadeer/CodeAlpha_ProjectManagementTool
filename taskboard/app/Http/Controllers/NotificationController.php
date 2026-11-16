<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Full list page.
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    // Mark one as read, then go to the task/project it is about.
    public function open(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}