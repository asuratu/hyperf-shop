<?php

declare(strict_types=1);

namespace Mine\AsyncQueue;

use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\AsyncQueue\JobInterface;

class Queue
{
    /**
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    /**
     * 生产消息
     * @param JobInterface $jobObj
     * @param int $delay
     * @return bool
     */
    public function push(JobInterface $jobObj, int $delay = 0): bool
    {
        return $this->driver->push($jobObj, $delay);
    }
}
