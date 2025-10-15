<?php

declare(strict_types=1);

namespace logger;

use logger\contract\LoggerInterface;


class LogFile implements LoggerInterface
{
    private $logFilePath;
    private $prefix;
    private $logFolderPath = '/logs/';
    private $logFileName = 'bloomex';

    public function __construct(string $logFilePath = null, string $prefix = null)
    {
        $this->logFilePath = $logFilePath;
        $this->prefix = $prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $this->camelCaseToSnakeCase($prefix);

        return $this;
    }

    public function setLogFileName(string $fileName): self
    {
        $this->logFileName = $this->camelCaseToSnakeCase($fileName);

        return $this;
    }

    public function setLogFolderPath(string $path): self
    {
        $result = str_replace('/', '', $path);
        $this->logFolderPath = $this->camelCaseToSnakeCase($result);

        return $this;
    }

    public function info(string $message)
    {
        $this->log('info', $message);
    }

    public function warning(string $message)
    {
        $this->log('warning', $message);
    }

    public function error(string $message)
    {
        $this->log('error', $message);
    }

    private function log($level, $message) {
        $path = $this->getLogFolderPath();

        if (!file_exists($path) && !mkdir($concurrentDirectory = $path, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $formattedMessage = sprintf('[%s] [%s] [%s]: %s',
            $level,
            $this->prefix ?? 'bloomex',
            date('Y-m-d H:i:s'),
            $message . "\n"
        );
        $filePath = $this->getFilePath($path);

        file_put_contents($filePath, $formattedMessage, FILE_APPEND);
    }

    private function getLogFolderPath(): string
    {
        global $mosConfig_absolute_path;

        return $mosConfig_absolute_path . $this->logFolderPath;
    }

    private function camelCaseToSnakeCase(string $input): string
    {
        $result = preg_replace_callback('/([a-z])([A-Z])/', static function($matches) {
            return $matches[1] . '_' . strtolower($matches[2]);
        }, $input);

        return strtolower($result);
    }

    private function getFilePath(string $path): string
    {
        $logFile = $this->logFileName ? $this->logFileName . '.log' : $this->prefix . '.log';

        return $path . date('y_m_d') . '_' . $logFile;
    }
}