<?php

namespace Tests\Unit\ValueObjects\Company;

use Tests\TestCase;
use App\ValueObjects\Company\CompanyName;

class CompanyNameTest extends TestCase
{
    public function test_can_create_company_name(): void
    {
        $name = 'Test Company';
        $companyName = new CompanyName($name);

        $this->assertEquals($name, $companyName->getValue());
    }

    public function test_throws_exception_for_empty_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company name cannot be empty');

        new CompanyName('');
    }

    public function test_throws_exception_for_whitespace_only_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company name cannot be empty');

        new CompanyName('   ');
    }

    public function test_throws_exception_for_name_too_long(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Company name cannot exceed 255 characters');

        $longName = str_repeat('a', 256);
        new CompanyName($longName);
    }

    public function test_trims_whitespace_from_name(): void
    {
        $name = '  Test Company  ';
        $companyName = new CompanyName($name);

        $this->assertEquals('Test Company', $companyName->getValue());
    }

    public function test_can_convert_to_string(): void
    {
        $name = 'Test Company';
        $companyName = new CompanyName($name);

        $this->assertEquals($name, (string) $companyName);
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $name = 'Test Company';
        $companyName1 = new CompanyName($name);
        $companyName2 = new CompanyName($name);

        $this->assertTrue($companyName1->equals($companyName2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $companyName1 = new CompanyName('Test Company 1');
        $companyName2 = new CompanyName('Test Company 2');

        $this->assertFalse($companyName1->equals($companyName2));
    }
} 