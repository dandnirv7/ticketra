<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('auth.page', ['view' => 'forgot']);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return $request->wantsJson()
                ? response()->json(['status' => __($status), 'ok' => true], 200)
                : back()->with('status', __($status));
        }

        return $request->wantsJson()
            ? response()->json(['message' => __($status), 'errors' => ['email' => [__($status)]]], 422)
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
