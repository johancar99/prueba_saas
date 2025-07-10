<?php

namespace Tests\Unit\ValueObjects\Company;

use Tests\TestCase;
use App\ValueObjects\Company\CompanyEmail;

class CompanyEmailTest extends TestCase
{
    public function test_can_create_company_email(): void
    {
        $email = 'test@company.com';
        $companyEmail = new CompanyEmail($email);

        $this->assertEquals($email, $companyEmail->getValue());
    }

    public function test_throws_exception_for_empty_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company email cannot be empty');

        new CompanyEmail('');
    }

    public function test_throws_exception_for_whitespace_only_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company email cannot be empty');

        new CompanyEmail('   ');
    }

    public function test_throws_exception_for_invalid_email_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new CompanyEmail('invalid-email');
    }

    public function test_throws_exception_for_email_too_long(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company email cannot exceed 255 characters');

        $longEmail = str_repeat('a', 250) . '@company.com';
        new CompanyEmail($longEmail);
    }

    public function test_converts_email_to_lowercase(): void
    {
        $email = 'TEST@COMPANY.COM';
        $companyEmail = new CompanyEmail($email);

        $this->assertEquals('test@company.com', $companyEmail->getValue());
    }

    public function test_trims_whitespace_from_email(): void
    {
        $email = '  test@company.com  ';
        $companyEmail = new CompanyEmail($email);

        $this->assertEquals('test@company.com', $companyEmail->getValue());
    }

    public function test_can_convert_to_string(): void
    {
        $email = 'test@company.com';
        $companyEmail = new CompanyEmail($email);

        $this->assertEquals($email, (string) $companyEmail);
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $email = 'test@company.com';
        $companyEmail1 = new CompanyEmail($email);
        $companyEmail2 = new CompanyEmail($email);

        $this->assertTrue($companyEmail1->equals($companyEmail2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $companyEmail1 = new CompanyEmail('test1@company.com');
        $companyEmail2 = new CompanyEmail('test2@company.com');

        $this->assertFalse($companyEmail1->equals($companyEmail2));
    }

    public function test_accepts_valid_email_formats(): void
    {
        $validEmails = [
            'test@company.com',
            'test.name@company.com',
            'test+tag@company.com',
            'test@subdomain.company.com'
        ];

        foreach ($validEmails as $email) {
            $companyEmail = new CompanyEmail($email);
            $this->assertEquals(strtolower($email), $companyEmail->getValue());
        }
    }
} 