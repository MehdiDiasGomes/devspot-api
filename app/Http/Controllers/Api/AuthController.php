<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

final class AuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['github', 'google'];

    /** Redirects the browser to the OAuth provider consent page. */
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handles the OAuth callback, finds or creates the user,
     * issues a Sanctum token, and redirects to the frontend.
     */
    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        $socialUser = Socialite::driver($provider)->user();

        $user = User::firstOrNew(['email' => $socialUser->getEmail()]);
        $user->name        = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User';
        $user->provider    = $provider;
        $user->provider_id = $socialUser->getId();
        $user->avatar      = $socialUser->getAvatar();
        $user->save();

        Auth::login($user);

        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        
        return redirect("{$frontendUrl}/auth/callback");
    }

    /** Revokes the current Sanctum token (logout). */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /** Returns the authenticated user. */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $user->avatar,
        ]);
    }
}
