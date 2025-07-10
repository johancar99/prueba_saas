<?php

namespace Tests\Unit\ValueObjects\User;

use Tests\TestCase;
use App\ValueObjects\User\Email;
use InvalidArgumentException;

class EmailTest extends TestCase
{
    public function test_can_create_valid_email(): void
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', $email->getValue());
        $this->assertEquals('example.com', $email->getDomain());
        $this->assertEquals('test', $email->getLocalPart());
    }

    public function test_email_is_normalized_to_lowercase(): void
    {
        $email = new Email('TEST@EXAMPLE.COM');
        
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function test_throws_exception_for_empty_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');
        
        new Email('');
    }

    public function test_throws_exception_for_invalid_email_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        new Email('invalid-email');
    }

    public function test_throws_exception_for_email_too_long(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        $longLocalPart = str_repeat('a', 245); // 245 + 13 = 258
        $email = $longLocalPart . '@example.com';
        new Email($email);
    }

    public function test_equals_method_works_correctly(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $email3 = new Email('other@example.com');
        
        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function test_to_string_returns_email_value(): void
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', (string) $email);
    }
} 