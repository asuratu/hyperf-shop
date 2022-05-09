<?php
declare(strict_types=1);

namespace Mine\Foundation\Factory;

use Mine\Foundation\Facades\Log;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class StdoutLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        return Log::channel();
    }
}
