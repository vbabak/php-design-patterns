<?php

declare(strict_types = 1);

namespace AbstractFactory;

interface PizzaInterface
{
    public function prepare($choice): void;
}

interface SpiceInterface
{
}


interface CheeseInterface
{
}


class RedPepper implements SpiceInterface
{
}


class Salt implements SpiceInterface
{
}


class GoodCheese implements CheeseInterface
{
}


class SpicyCheese implements CheeseInterface
{
}

abstract class PizzaComponentFactoryAbstract
{
    public abstract function createSpice(): SpiceInterface;

    public abstract function createCheese(): CheeseInterface;
}

class LitePizzaComponentFactory extends PizzaComponentFactoryAbstract
{
    public function createSpice(): SpiceInterface
    {
        return new Salt();
    }

    public function createCheese(): CheeseInterface
    {
        return new GoodCheese();
    }
}

class SpicyPizzaComponentFactory extends PizzaComponentFactoryAbstract
{
    public function createSpice(): SpiceInterface
    {
        return new RedPepper();
    }

    public function createCheese(): CheeseInterface
    {
        return new SpicyCheese();
    }
}

class PizzaComponentFactoryProducer
{
    public function getComponentFactory($choice): PizzaComponentFactoryAbstract
    {
        if ($choice === 'lite') {
            return new LitePizzaComponentFactory();
        } else if ($choice === 'spicy') {
            return new SpicyPizzaComponentFactory();
        } else {
            throw new \Exception('Unknown $choise');
        }
    }
}

class Pizza implements PizzaInterface
{
    public function prepare($choice): void
    {
        $componentsFactoryProducer = new PizzaComponentFactoryProducer();
        $componentsFactory = $componentsFactoryProducer->getComponentFactory($choice);
        $spice = $componentsFactory->createSpice();
        $this->addSpice($spice);
        $cheese = $componentsFactory->createCheese();
        $this->addCheese($cheese);
    }

    protected function addSpice($spice)
    {
        echo 'Added spice' . PHP_EOL;
    }

    protected function addCheese($cheese)
    {
        echo 'Added cheese' . PHP_EOL;
    }
}

$pizza = new Pizza();
$pizza->prepare('spicy');
