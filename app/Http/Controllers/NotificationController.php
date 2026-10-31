<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter') === 'belum' ? 'belum' : 'semua';

        $query = $filter === 'belum' ? $user->unreadNotifications() : $user->notifications();

        return view('notifications.index', [
            'notifs' => $query->paginate(15)->withQueryString(),
            'filter' => $filter,
        ]);
    }

    // Tandai dibaca lalu arahkan ke halaman tujuan
    public function open(Request $request, string $id)
    {
        $n = $request->user()->notifications()->findOrFail($id);
        $n->markAsRead();

        return redirect($n->data['url'] ?? route('home'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
