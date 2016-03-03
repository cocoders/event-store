<?php declare(strict_types=1);

namespace Cocoders\EventStore\EventStream;

final class Name
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
