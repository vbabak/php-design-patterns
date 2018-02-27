<?php

declare(strict_types = 1);

namespace Singleton;

class Singleton
{
    static protected $instance;

    protected $data = [];

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function setData(array $data): Singleton
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}

Singleton::getInstance()->setData(['test', 'data']);
$data = Singleton::getInstance()->getData();
print_r($data);
