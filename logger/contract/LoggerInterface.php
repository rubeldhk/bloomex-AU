<?php

declare(strict_types=1);

namespace logger\contract;

interface LoggerInterface
{
    public function info(string $message);
    public function warning(string $message);
    public function error(string $message);
}