<?php

namespace Tests\Unit\ValueObjects\Auth;

use Tests\TestCase;
use App\ValueObjects\Auth\Token;
use InvalidArgumentException;

class TokenTest extends TestCase
{
    public function test_can_create_valid_token(): void
    {
        $tokenValue = 'valid-token-string';
        $token = new Token($tokenValue);
        
        $this->assertEquals($tokenValue, $token->getValue());
        $this->assertNull($token->getPlainTextValue());
    }

    public function test_can_create_token_with_plain_text(): void
    {
        $tokenValue = 'hashed-token';
        $plainTextValue = 'plain-text-token';
        $token = new Token($tokenValue, $plainTextValue);
        
        $this->assertEquals($tokenValue, $token->getValue());
        $this->assertEquals($plainTextValue, $token->getPlainTextValue());
    }

    public function test_throws_exception_for_empty_token(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token cannot be empty');
        
        new Token('');
    }

    public function test_equals_method_works_correctly(): void
    {
        $token1 = new Token('same-token');
        $token2 = new Token('same-token');
        $token3 = new Token('different-token');
        
        $this->assertTrue($token1->equals($token2));
        $this->assertFalse($token1->equals($token3));
    }

    public function test_to_string_returns_token_value(): void
    {
        $tokenValue = 'test-token';
        $token = new Token($tokenValue);
        
        $this->assertEquals($tokenValue, (string) $token);
    }
} 