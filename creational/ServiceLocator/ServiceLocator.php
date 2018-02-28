<?php

declare(strict_types = 1);

namespace ServiceLocator;

interface ServiceLocatorInterface
{
    /**
     * Check if ServiceLocator has a named service
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Retrieves a service by the given name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name);
}

/**
 * Create instances with using ServiceLocatorInterface $serviceLocator
 * for resolving dependencies
 */
interface FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator);
}

abstract class ServiceLocatorAbstract implements ServiceLocatorInterface
{
    /**
     * @param string $name Service name
     * @param object $instance Any instance
     *
     * @return ServiceLocatorAbstract
     */
    abstract public function setService(string $name, $instance): ServiceLocatorAbstract;

    /**
     * @param string $name
     * @param FactoryInterface $factory
     * @param bool $shared
     *
     * @return ServiceLocatorAbstract
     */
    abstract public function setFactory(string $name, FactoryInterface $factory, bool $shared = false): ServiceLocatorAbstract;
}

class ServiceLocator extends ServiceLocatorAbstract
{
    protected $services = [];
    protected $factories = [];
    protected $shared = [];

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->services);
    }

    public function isShared(string $name): bool
    {
        if (!array_key_exists($name, $this->shared)) {
            return false;
        }
        $is = (bool)$this->shared[$name];

        return $is;
    }

    public function get(string $name)
    {
        $is_shared = $this->isShared($name);
        if ($this->has($name) && $is_shared) {
            return $this->services[$name];
        }

        /** @var FactoryInterface $factory */
        $factory = $this->factories[$name];
        $service = $factory->createService($this);

        if ($is_shared) {
            $this->shared[$name] = true;
            $this->services[$name] = true;
        }

        return $service;
    }

    public function setService(string $name, $instance): ServiceLocatorAbstract
    {
        $this->services[$name] = $instance;

        return $this;
    }

    public function setFactory(string $name, FactoryInterface $factory, bool $shared = false): ServiceLocatorAbstract
    {
        $this->factories[$name] = $factory;
        $this->shared[$name] = $shared;

        return $this;
    }
}

class Apple
{
}

class Basket
{
    protected $apples = [];

    public function addApple(Apple $apple)
    {
        $this->apples[] = $apple;

        return $this;
    }
}

class BasketFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $apple = $serviceLocator->get('apple');
        $basket = new Basket();
        $basket->addApple($apple); // set dependency explicitly

        return $basket;
    }
}

class AppleFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $apple = new Apple();

        return $apple;
    }
}

$sl = new ServiceLocator();
$sl->setFactory('basket', new BasketFactory);
$sl->setFactory('apple', new AppleFactory);

$basket_with_apple = $sl->get('basket');
var_dump($basket_with_apple);
