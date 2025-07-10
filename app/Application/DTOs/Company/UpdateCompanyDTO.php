<?php

namespace App\Application\DTOs\Company;

use App\ValueObjects\Company\CompanyId;
use App\ValueObjects\Company\CompanyName;
use App\ValueObjects\Company\CompanyEmail;
use App\ValueObjects\Company\CompanyPhone;
use App\ValueObjects\Company\CompanyAddress;
use App\ValueObjects\Company\CompanyStatus;

class UpdateCompanyDTO
{
    public function __construct(
        public readonly CompanyId $id,
        public readonly ?CompanyName $name = null,
        public readonly ?CompanyEmail $email = null,
        public readonly ?CompanyPhone $phone = null,
        public readonly ?CompanyAddress $address = null,
        public readonly ?CompanyStatus $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: new CompanyId($data['id']),
            name: isset($data['name']) ? new CompanyName($data['name']) : null,
            email: isset($data['email']) ? new CompanyEmail($data['email']) : null,
            phone: isset($data['phone']) ? new CompanyPhone($data['phone']) : null,
            address: isset($data['address']) ? new CompanyAddress($data['address']) : null,
            status: isset($data['is_active']) ? new CompanyStatus($data['is_active']) : null
        );
    }

    public function toArray(): array
    {
        $data = ['id' => $this->id->getValue()];

        if ($this->name) {
            $data['name'] = $this->name->getValue();
        }

        if ($this->email) {
            $data['email'] = $this->email->getValue();
        }

        if ($this->phone) {
            $data['phone'] = $this->phone->getValue();
        }

        if ($this->address) {
            $data['address'] = $this->address->getValue();
        }

        if ($this->status) {
            $data['is_active'] = $this->status->getValue();
        }

        return $data;
    }
} 