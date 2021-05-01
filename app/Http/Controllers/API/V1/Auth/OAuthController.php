<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Exceptions\EmailTakenException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class OAuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest');
        config([
            'services.vkontakte.redirect' => route('oauth.callback', 'vkontakte'),
        ]);
    }

    public function redirect($provider)
    {
        return [
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ];
    }

    public function handleCallback($provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        $user = $this->findOrCreateUser($provider, $socialiteUser);
        
        $token = JWTAuth::fromUser($user);

        return view('oauth/callback', [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    protected function findOrCreateUser($provider, $user)
    {
        $oauthProvider = SocialAccount::where('provider', $provider)
            ->where('provider_id', $user->getId())
            ->first();

        if ($oauthProvider) {
            $oauthProvider->update([
                'token' => $user->token
            ]);

            return $oauthProvider->user;
        }

        if (User::where('email', $user->getEmail())->exists()) {
            throw new EmailTakenException;
        }

        return $this->createUser($provider, $user);
    }

    protected function createUser($provider, $sUser)
    {
        $user = User::create([
            'name' => $sUser->getName(),
            'email' => $sUser->getEmail(),
            'email_verified_at' => now(),
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $sUser->getId(),
            'token' => $sUser->token,
        ]);

        return $user;
    }
}
