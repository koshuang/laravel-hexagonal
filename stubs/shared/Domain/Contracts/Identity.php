<?php

namespace Modules\Shared\Domain\Contracts;

use Stringable;

abstract class Identity extends ValueObject implements Nullable, Stringable
{
    public private(set) int|string $value;

    public function __construct(int|string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function isNull(): bool
    {
        return false;
    }
}
