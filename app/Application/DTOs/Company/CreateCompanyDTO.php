<?php

namespace App\Application\DTOs\Company;

use App\ValueObjects\Company\CompanyName;
use App\ValueObjects\Company\CompanyEmail;
use App\ValueObjects\Company\CompanyPhone;
use App\ValueObjects\Company\CompanyAddress;
use App\ValueObjects\Company\CompanyStatus;
use App\ValueObjects\Plan\PlanId;

class CreateCompanyDTO
{
    public function __construct(
        public readonly CompanyName $name,
        public readonly CompanyEmail $email,
        public readonly CompanyPhone $phone,
        public readonly CompanyAddress $address,
        public readonly CompanyStatus $status,
        public readonly PlanId $planId,
        public readonly string $adminEmail,
        public readonly string $adminPassword,
        public readonly ?string $adminName = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: new CompanyName($data['name']),
            email: new CompanyEmail($data['email']),
            phone: new CompanyPhone($data['phone']),
            address: new CompanyAddress($data['address']),
            status: new CompanyStatus($data['is_active'] ?? true),
            planId: new PlanId($data['plan_id']),
            adminEmail: $data['admin_email'],
            adminPassword: $data['admin_password'],
            adminName: $data['admin_name'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->getValue(),
            'email' => $this->email->getValue(),
            'phone' => $this->phone->getValue(),
            'address' => $this->address->getValue(),
            'is_active' => $this->status->getValue(),
            'plan_id' => $this->planId->getValue(),
            'admin_email' => $this->adminEmail,
            'admin_password' => $this->adminPassword,
            'admin_name' => $this->adminName,
        ];
    }
} 