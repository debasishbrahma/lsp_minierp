<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = Product::all();
        $quotations = $user->isAdmin() ? Quotation::with('user')->get() : $user->quotations;
        $notifications = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', \App\Models\User::class)
            ->get()
            ->map(function ($notification) {
                $notification->data = json_decode($notification->data, true) ?? [];
                return $notification;
            })
            ->filter(function ($notification) {
                return isset($notification->data['message'], $notification->data['quotation_id']);
            });

        return view('dashboard', compact('products', 'quotations', 'notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->where('notifiable_type', \App\Models\User::class)
            ->first();

        if ($notification) {
            DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        return redirect()->route('dashboard')->with('success', 'Notification marked as read.');
    }
}
