<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->latest()->take(5)->get();
        return view('shop.account.index', compact('user', 'orders'));
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('shop.account.orders', compact('orders'));
    }

    public function orderShow(\App\Models\Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load('items.product.mainImage');
        return view('shop.account.order-show', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('shop.account.profile', compact('user'));
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'phone'));
        return back()->with('success', 'Profil mis à jour');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mot de passe actuel incorrect');
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Mot de passe modifié');
    }

    public function wishlist()
    {
        $wishlist = Auth::user()->wishlist()->with('product.mainImage')->get();
        return view('shop.account.wishlist', compact('wishlist'));
    }

    public function toggleWishlist(Request $request, int $productId)
    {
        $user = Auth::user();
        $existing = $user->wishlist()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
        } else {
            $user->wishlist()->create(['product_id' => $productId]);
            $inWishlist = true;
        }

        if ($request->ajax()) {
            return response()->json(['in_wishlist' => $inWishlist]);
        }

        return back();
    }

    public function notifications()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        Auth::user()->unreadNotifications->markAsRead();

        return view('shop.account.notifications', compact('notifications'));
    }

    public function markNotificationRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
