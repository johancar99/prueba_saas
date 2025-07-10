<?php

namespace Tests\Unit\ValueObjects\Company;

use Tests\TestCase;
use App\ValueObjects\Company\CompanyId;

class CompanyIdTest extends TestCase
{
    public function test_can_create_company_id(): void
    {
        $id = 1;
        $companyId = new CompanyId($id);

        $this->assertEquals($id, $companyId->getValue());
    }

    public function test_can_convert_to_string(): void
    {
        $id = 1;
        $companyId = new CompanyId($id);

        $this->assertEquals('1', (string) $companyId);
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $id = 1;
        $companyId1 = new CompanyId($id);
        $companyId2 = new CompanyId($id);

        $this->assertTrue($companyId1->equals($companyId2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $companyId1 = new CompanyId(1);
        $companyId2 = new CompanyId(2);

        $this->assertFalse($companyId1->equals($companyId2));
    }
} 