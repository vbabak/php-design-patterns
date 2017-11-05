<?php

declare(strict_types = 1);

namespace Application;

interface FruitInterface
{
    public function getCalories(): float;
}

class Apple implements FruitInterface
{
    public function getCalories(): float
    {
        $calories = 52 / 100;

        return $calories;
    }
}

class Orange implements FruitInterface
{
    public function getCalories(): float
    {
        $calories = 47 / 100;

        return $calories;
    }
}

abstract class CaloriesCalculatorAbstract
{
    public function calculateCalories(float $weight): float
    {
        $product = $this->makeFruit();

        $calories = $product->getCalories();
        $total = $calories * $weight;

        return $total;
    }

    protected abstract function makeFruit(): FruitInterface;
}

class OrangeCaloriesCalculator extends CaloriesCalculatorAbstract
{
    protected function makeFruit(): FruitInterface
    {
        $fruit = new Orange();

        return $fruit;
    }
}

$calories_calculator = new OrangeCaloriesCalculator();

$p_weight = 500;
$p_type = 'orange';

$total_calories = $calories_calculator->calculateCalories($p_weight);
echo sprintf('%dg. of %s has %.0F calories', $p_weight, $p_type, $total_calories);
