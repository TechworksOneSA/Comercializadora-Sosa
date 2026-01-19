<?php

class Logger
{
    private static string $logFile = __DIR__ . '/../../debug.log';

    public static function log(string $message, $data = null): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        
        if ($data !== null) {
            $logMessage .= "\nData: " . print_r($data, true);
        }
        
        $logMessage .= "\n" . str_repeat('-', 80) . "\n";
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function clear(): void
    {
        file_put_contents(self::$logFile, '');
    }
}
