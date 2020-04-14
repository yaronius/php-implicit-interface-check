<?php

namespace yaronius\ImplicitInterface;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionException;
use ReflectionNamedType;

/**
 * @param object|string $class
 * @param string $interface
 * @return bool
 * @throws ReflectionException
 */
function class_provides($class, string $interface): bool {
    // an object may be provided, so we handle this case here
    if (is_object($class)) {
        if ($class instanceof $interface) {
            return true;
        }
        $class = get_class($class);
    }

    try {
        $classReflect = new ReflectionClass($class);
    } catch (ReflectionException $e) {
        //todo throw dedicated exception
        return false;
    }

    // check if the class explicitly implements the interface
    if ($classReflect->implementsInterface($interface)) {
        return true;
    }

    try {
        $interfaceReflect = new ReflectionClass($interface);
    } catch (ReflectionException $e) {
        //todo throw dedicated exception
        return false;
    }

    if (!$interfaceReflect->isInterface()) {
        throw new InvalidArgumentException('Second argument must be an interface');
    }

    foreach ($interfaceReflect->getMethods() as $interfaceMethod) {
        // check if class has same method name
        if (!$classReflect->hasMethod($interfaceMethod->getName())) {
            return false;
        }
        $classMethod = $classReflect->getMethod($interfaceMethod->getName());
        if (!reflection_methods_same($classMethod, $interfaceMethod)) {
            return false;
        }
    }

    return true;
}

/**
 * @param ReflectionMethod $method1
 * @param ReflectionMethod $method2
 * @return bool
 */
function reflection_methods_same(ReflectionMethod $method1, ReflectionMethod $method2): bool
{
    $firstMethodModifiers = $method1->getModifiers();
    $secondMethodModifiers = $method1->getModifiers();
    if ($firstMethodModifiers != $secondMethodModifiers) {
        // interface methods are always abstract, so if it is the only difference, we ignore it
        if ($firstMethodModifiers ^ $secondMethodModifiers != ReflectionMethod::IS_ABSTRACT) {
            return false;
        }
    }
    if ($method1->getNumberOfParameters() != $method2->getNumberOfParameters()) {
        return false;
    }
    if ($method1->getNumberOfRequiredParameters() != $method2->getNumberOfRequiredParameters()) {
        return false;
    }
    $firstParams = $method1->getParameters();
    $secondParams = $method2->getParameters();
    /**
     * @var ReflectionParameter $interfaceParam
     * @var ReflectionParameter $classParam
     */
    foreach ($secondParams as $i => $interfaceParam) {
        $classParam = $firstParams[$i];
        if (!reflection_params_same($interfaceParam, $classParam)) {
            return false;
        }
    }
    return true;
}

/**
 * @param ReflectionParameter $param1
 * @param ReflectionParameter $param2
 * @return bool
 */
function reflection_params_same(ReflectionParameter $param1, ReflectionParameter $param2): bool
{
    if ($param1->isPassedByReference() != $param2->isPassedByReference()) {
        return false;
    }
    if ($param1->isArray() != $param2->isArray()) {
        return false;
    }
    if ($param1->isCallable() != $param2->isCallable()) {
        return false;
    }
    if ($param1->isOptional() != $param2->isOptional()) {
        return false;
    }
    if ($param1->isVariadic() != $param2->isVariadic()) {
        return false;
    }
    $firstParamType = $param1->getType();
    $secondParamType = $param2->getType();

    if (is_null($firstParamType) || is_null($secondParamType)) {
        return $firstParamType === $secondParamType;
    }

    if (get_class($firstParamType) != get_class($secondParamType)) {
        return false;
    }
    if ($firstParamType instanceof ReflectionNamedType && $secondParamType instanceof ReflectionNamedType) {
        if ($firstParamType->getName() != $secondParamType->getName()) {
            return false;
        }
    }
    if ($firstParamType->isBuiltin() != $secondParamType->isBuiltin()) {
        return false;
    }
    if ($firstParamType->allowsNull() != $secondParamType->allowsNull()) {
        return false;
    }

    return true;
}
