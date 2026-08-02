<?php

namespace Modules\Shared\Domain\Contracts;

/**
 * @template T
 */
abstract class ValueObject implements DomainObject
{
    /**
     * @param T $valueObject
     */
    public function equals(mixed $valueObject): bool
    {
        if (! $valueObject instanceof static) {
            return false;
        }

        return json_encode($this) === json_encode($valueObject);
    }
}
