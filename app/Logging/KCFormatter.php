<?php


namespace App\Logging;


use Monolog\Formatter\LineFormatter;

class KCFormatter
{
    public function __invoke($logger)
    {
        $clientIP = \request()->getClientIp();
        $path = \request()->getPathInfo();

        foreach ($logger->getHandlers() as $handler) {
            $format = "[%datetime%] [%level_name%] [$clientIP] [$path] [%message%] %context%\n";
            $lineFormatter = new LineFormatter($format);
            $handler->setFormatter($lineFormatter);
        }
    }
}
