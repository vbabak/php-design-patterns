<?php

declare(strict_types = 1);

namespace Application;

interface FoodInterface
{
    public function getCalories(): float;
}

class Apple implements FoodInterface
{
    public function getCalories(): float
    {
        $calories = 52 / 100;

        return $calories;
    }
}

class Orange implements FoodInterface
{
    public function getCalories(): float
    {
        $calories = 47 / 100;

        return $calories;
    }
}

class FoodFactory
{
    public function create(string $type)
    {
        $instance = null;
        if ($type === 'orange') {
            $instance = new Orange();
        } else if ($type === 'apple') {
            $instance = new Apple();
        }

        return $instance;
    }
}

class CaloriesCalculator
{
    public function calculateCalories($weight, $type): float
    {
        $productFactory = new FoodFactory();
        $product = $productFactory->create($type);
        if (!$product instanceof FoodInterface) {
            throw new \Exception('Unknown product type');
        }
        $calories = $product->getCalories();
        $total = $calories * $weight;

        return $total;
    }
}

$calories_calculator = new CaloriesCalculator();

$p_type = 'apple';
$p_weight = 100;

$total_calories = $calories_calculator->calculateCalories($p_weight, $p_type);
echo sprintf('%dg. of %s has %.0F calories', $p_weight, $p_type, $total_calories);
