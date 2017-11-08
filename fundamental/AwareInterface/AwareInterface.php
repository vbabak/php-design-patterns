<?php

declare(strict_types = 1);

namespace AwareInterface;

interface LoggerInterface
{
    public function log($msg);
}

interface LoggerAwareInterface
{
    public function setLogger(Logger $logger);

    public function getLogger(): Logger;
}

trait LoggerAwareTrait
{
    protected $logger;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }
}

class Logger implements LoggerInterface
{
    public function log($msg)
    {
        echo $msg . PHP_EOL;
    }
}

class Application implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function run()
    {
        $this->getLogger()->log(__METHOD__);
    }
}

$logger = new Logger();
$app = new Application();

if ($app instanceof LoggerAwareInterface) {
    $app->setLogger($logger);
}

$app->run();
