<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $throttleKey = 'verify-resend:' . ($request->user()?->id ?: $request->input('email', 'guest'));
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['message' => "Terlalu banyak percobaan. Coba lagi dalam $seconds detik."], 429);
            }
            return back()->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam $seconds detik."]);
        }
        RateLimiter::hit($throttleKey, 60);

        if ($request->user()) {
            if ($request->user()->hasVerifiedEmail()) {
                return $request->wantsJson() || $request->expectsJson()
                    ? response()->json(['ok' => true, 'message' => 'Email sudah terverifikasi.'])
                    : redirect()->intended(route('dashboard', absolute: false));
            }
            $request->user()->sendEmailVerificationNotification();
        } else {
            $email = (string) $request->input('email', '');
            if (! $email) {
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['message' => 'Email wajib diisi.'], 422);
                }
                return back()->withErrors(['email' => 'Email wajib diisi.']);
            }
            $user = User::where('email', $email)->first();
            if ($user && ! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Tautan verifikasi baru telah dikirim ke emailmu.',
            ]);
        }
        return back()->with('status', 'verification-link-sent');
    }
}
