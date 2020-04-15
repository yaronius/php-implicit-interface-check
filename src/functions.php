<?php

namespace yaronius\ImplicitInterface;

use ReflectionException;

/**
 * @param object|string $class
 * @param string $interface
 * @return bool
 * @throws ReflectionException
 */
function class_complies_with($class, string $interface): bool {
    return (new Contract($class))->compliesWith($interface);
}
