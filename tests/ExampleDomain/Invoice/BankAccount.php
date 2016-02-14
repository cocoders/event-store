<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

final class BankAccount
{
    private $bankName;
    private $bankNumber;
    private $bicCode;

    public function __construct(string $bankName, string $bankNumber, string $bicCode)
    {
        $this->bankName = $bankName;
        $this->bankNumber = $bankNumber;
        $this->bicCode = $bicCode;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function getBankNumber(): string
    {
        return $this->bankNumber;
    }

    public function getBicCode(): string
    {
        return $this->bicCode;
    }
}

