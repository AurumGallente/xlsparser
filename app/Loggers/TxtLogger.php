<?php

namespace App\Loggers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class TxtLogger
{
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('txtfile');

        // Create a StreamHandler
        $stream = new StreamHandler(storage_path('logs/result.txt'));

        // Create a custom formatter
        $formatter = new LineFormatter(
            "%message%", // The log message format, excluding datetime.
            null, // The date format (null means default).
            true, // Allow multiline messages
            false
        );

        // Apply the formatter to the handler
        $stream->setFormatter($formatter);

        // Push the handler to the logger
        $logger->pushHandler($stream);

        return $logger;
    }
}
