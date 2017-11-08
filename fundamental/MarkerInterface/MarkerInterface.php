<?php

declare(strict_types = 1);

namespace MarkerInterface;

use Exception;

interface ComponentNotFoundExceptionInterface
{
}

class ComponentNotFoundException extends Exception implements ComponentNotFoundExceptionInterface
{
}

try {
    if (!isset($critical_component)) {
        throw new ComponentNotFoundException('Component not found');
    }
} catch (ComponentNotFoundExceptionInterface $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
