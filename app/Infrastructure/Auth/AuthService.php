<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\AuthServiceInterface;
use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\Auth\Token;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService implements AuthServiceInterface
{
    public function login(Email $email, Password $password): Token
    {
        $credentials = [
            'email' => $email->getValue(),
            'password' => $password->getValue()
        ];

        if (!Auth::attempt($credentials)) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        $user = Auth::user();
        
        // Eliminar tokens anteriores del usuario
        $user->tokens()->delete();
        
        // Crear nuevo token
        $token = $user->createToken('auth-token', ['*'], now()->addHours(24));
        
        return new Token($token->plainTextToken, $token->plainTextToken);
    }

    public function logout(Token $token): void
    {
        $personalAccessToken = PersonalAccessToken::findToken($token->getValue());
        
        if ($personalAccessToken) {
            $personalAccessToken->delete();
        }
    }

    public function logoutAllTokens(Token $token): void
    {
        $personalAccessToken = PersonalAccessToken::findToken($token->getValue());
        
        if ($personalAccessToken && $personalAccessToken->tokenable) {
            $personalAccessToken->tokenable->tokens()->delete();
        }
    }

    public function refresh(Token $token): Token
    {
        $personalAccessToken = PersonalAccessToken::findToken($token->getValue());
        
        if (!$personalAccessToken) {
            throw new \InvalidArgumentException('Invalid token');
        }

        $user = $personalAccessToken->tokenable;
        
        if (!$user) {
            throw new \InvalidArgumentException('Token not associated with user');
        }

        // Eliminar token actual
        $personalAccessToken->delete();
        
        // Crear nuevo token
        $newToken = $user->createToken('auth-token', ['*'], now()->addHours(24));
        
        return new Token($newToken->plainTextToken, $newToken->plainTextToken);
    }

    public function validateToken(Token $token): bool
    {
        $personalAccessToken = PersonalAccessToken::findToken($token->getValue());
        
        if (!$personalAccessToken) {
            return false;
        }

        // Verificar si el token ha expirado
        if ($personalAccessToken->expires_at && $personalAccessToken->expires_at->isPast()) {
            return false;
        }

        return true;
    }
} 