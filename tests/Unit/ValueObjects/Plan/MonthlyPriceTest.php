<?php

namespace Tests\Unit\ValueObjects\Plan;

use Tests\TestCase;
use App\ValueObjects\Plan\MonthlyPrice;
use InvalidArgumentException;

class MonthlyPriceTest extends TestCase
{
    public function test_can_create_valid_monthly_price(): void
    {
        $price = new MonthlyPrice(29.99);
        
        $this->assertEquals(29.99, $price->getValue());
        $this->assertEquals('29.99', (string) $price);
    }

    public function test_throws_exception_for_negative_price(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Monthly price cannot be negative');
        
        new MonthlyPrice(-10.00);
    }

    public function test_throws_exception_for_price_too_high(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Monthly price cannot exceed 999,999.99');
        
        new MonthlyPrice(1000000.00);
    }

    public function test_rounds_price_to_two_decimals(): void
    {
        $price = new MonthlyPrice(29.999);
        
        $this->assertEquals(30.00, $price->getValue());
    }

    public function test_equals_method_works_correctly(): void
    {
        $price1 = new MonthlyPrice(29.99);
        $price2 = new MonthlyPrice(29.99);
        $price3 = new MonthlyPrice(49.99);
        
        $this->assertTrue($price1->equals($price2));
        $this->assertFalse($price1->equals($price3));
    }

    public function test_can_add_prices(): void
    {
        $price1 = new MonthlyPrice(29.99);
        $price2 = new MonthlyPrice(20.01);
        
        $result = $price1->add($price2);
        
        $this->assertEquals(50.00, $result->getValue());
    }

    public function test_can_subtract_prices(): void
    {
        $price1 = new MonthlyPrice(50.00);
        $price2 = new MonthlyPrice(20.01);
        
        $result = $price1->subtract($price2);
        
        $this->assertEquals(29.99, $result->getValue());
    }

    public function test_throws_exception_when_subtraction_results_in_negative(): void
    {
        $price1 = new MonthlyPrice(20.00);
        $price2 = new MonthlyPrice(30.00);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Result cannot be negative');
        
        $price1->subtract($price2);
    }

    public function test_can_multiply_price(): void
    {
        $price = new MonthlyPrice(29.99);
        $result = $price->multiply(12);
        
        $this->assertEquals(359.88, $result->getValue());
    }

    public function test_throws_exception_for_negative_multiplier(): void
    {
        $price = new MonthlyPrice(29.99);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Factor must be positive');
        
        $price->multiply(-1);
    }
} 