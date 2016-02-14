<?php declare(strict_types=1);

namespace ExampleDomain\Invoice;

final class Address
{
    private $street;
    private $postalCode;
    private $city;
    private $country = 'pl';

    public function __construct(string $street, string $postalCode, string $city, string $country = 'pl')
    {
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
    }

    public function __toString(): string
    {
        return sprintf('%s %s %s %s', $this->street, $this->postalCode, $this->city, $this->country);
    }
    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
