<?php

namespace App\Domain\Auth;

use App\ValueObjects\User\Email;
use App\ValueObjects\User\Password;
use App\ValueObjects\Auth\Token;

interface AuthServiceInterface
{
    public function login(Email $email, Password $password): Token;
    
    public function logout(Token $token): void;
    
    public function logoutAllTokens(Token $token): void;
    
    public function refresh(Token $token): Token;
    
    public function validateToken(Token $token): bool;
} 