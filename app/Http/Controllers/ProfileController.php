<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        $bookingsCount = \App\Models\Booking::where('user_id', $user->id)->count();
        $confirmedCount = \App\Models\Booking::where('user_id', $user->id)->where('status', 'confirmed')->count();
        $recentBookings = \App\Models\Booking::where('user_id', $user->id)
            ->with(['jadwalTayang.film', 'jadwalTayang.studio.bioskop'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        if ($confirmedCount >= 4) {
            $membership = 'Gold Ticketra Member';
            $membershipBadge = 'bg-[#FFFBEA] text-accent-yellow';
        } elseif ($confirmedCount >= 1) {
            $membership = 'Silver Ticketra Member';
            $membershipBadge = 'bg-[#E6F3FF] text-blue-600';
        } else {
            $membership = 'Bronze Ticketra Member';
            $membershipBadge = 'bg-pastel-peach/20 text-[#FF8A65]';
        }
        
        $loyaltyPoints = $confirmedCount * 100;

        return view('profile.edit', [
            'user' => $user,
            'bookingsCount' => $bookingsCount,
            'confirmedCount' => $confirmedCount,
            'recentBookings' => $recentBookings,
            'membership' => $membership,
            'membershipBadge' => $membershipBadge,
            'loyaltyPoints' => $loyaltyPoints,
        ]);
    }

    
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
