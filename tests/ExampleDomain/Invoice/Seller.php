<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

final class Seller
{
    private $name;
    private $taxIdNumber;
    private $postalCode;
    private $street;
    private $city;
    private $country;
    private $bankName;
    private $bankNumber;
    private $bicCode;

    public function __construct($name, TaxIdNumber $taxIdNumber, Address $address, BankAccount $account)
    {
        if (! $name) {
            throw new \InvalidArgumentException('Name is required');
        }

        $this->name = $name;
        $this->taxIdNumber = (string) $taxIdNumber;
        $this->postalCode = $address->getPostalCode();
        $this->street = $address->getStreet();
        $this->city = $address->getCity();
        $this->country = $address->getCountry();
        $this->bankName = $account->getBankName();
        $this->bankNumber = $account->getBankNumber();
        $this->bicCode = $account->getBicCode();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTaxIdNumber(): TaxIdNumber
    {
        return new TaxIdNumber($this->taxIdNumber);
    }

    public function getAddress(): Address
    {
        return new Address(
            $this->street,
            $this->postalCode,
            $this->city,
            $this->country
        );
    }

    public function getBankAccount(): BankAccount
    {
        return new BankAccount(
            $this->bankName,
            $this->bankNumber,
            $this->bicCode
        );
    }
}

