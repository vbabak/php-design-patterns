<?php

declare(strict_types = 1);

namespace Decorator;

interface PizzaInterface
{
    public function prepare(): void;

    public function addComponent($component): void;
}

final class Pizza implements PizzaInterface
{
    public function prepare(): void
    {
        $this->addComponent('cheese');
        $this->addComponent('chicken');
        echo 'Done!' . PHP_EOL;
    }

    public function addComponent($component): void
    {
        echo 'Added ' . $component . PHP_EOL;
    }
}

abstract class PizzaDecoratorAbstract implements PizzaInterface
{
    /**
     * @var PizzaInterface $pizza
     */
    protected $pizza;

    public function __construct(PizzaInterface $pizza)
    {
        $this->pizza = $pizza;
    }

    public function prepare(): void
    {
        $this->pizza->prepare();
    }

    public function addComponent($component): void
    {
        $this->pizza->addComponent($component);
    }
}

class DoubleCheesePizzaDecorator extends PizzaDecoratorAbstract
{
    public function prepare(): void
    {
        $this->pizza->addComponent('cheese');
        $this->pizza->prepare();
    }
}

$pizza = new Pizza();

$doubleCheesePizza = new DoubleCheesePizzaDecorator($pizza);
$doubleCheesePizza->prepare();
