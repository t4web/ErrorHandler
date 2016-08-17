<?php

namespace T4web\ErrorHandler;

use T4web\Log\Logger;

class ErrorHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Exception $e
     */
    public function handleException(\Exception $e)
    {
        $this->logger->log(
            $e->getMessage(),
            [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ],
            Logger::PRIORITY_ERR
        );
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function handle($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
                $typestr = 'E_ERROR'; break;
            case E_WARNING:
                $typestr = 'E_WARNING'; break;
            case E_PARSE:
                $typestr = 'E_PARSE'; break;
            case E_NOTICE:
                $typestr = 'E_NOTICE'; break;
            case E_CORE_ERROR:
                $typestr = 'E_CORE_ERROR'; break;
            case E_CORE_WARNING:
                $typestr = 'E_CORE_WARNING'; break;
            case E_COMPILE_ERROR:
                $typestr = 'E_COMPILE_ERROR'; break;
            case E_CORE_WARNING:
                $typestr = 'E_COMPILE_WARNING'; break;
            case E_USER_ERROR:
                $typestr = 'E_USER_ERROR'; break;
            case E_USER_WARNING:
                $typestr = 'E_USER_WARNING'; break;
            case E_USER_NOTICE:
                $typestr = 'E_USER_NOTICE'; break;
            case E_STRICT:
                $typestr = 'E_STRICT'; break;
            case E_RECOVERABLE_ERROR:
                $typestr = 'E_RECOVERABLE_ERROR'; break;
            case E_DEPRECATED:
                $typestr = 'E_DEPRECATED'; break;
            case E_USER_DEPRECATED:
                $typestr = 'E_USER_DEPRECATED'; break;
        }

        if (!$errno) {
            return;
        }

        $message = "Error PHP in file : ".$errfile." at line : ".$errline."
                with type error : ".$typestr." : ".$errstr." in ".$_SERVER['REQUEST_URI'];

        $priority = Logger::PRIORITY_WARN;

        if (in_array($errno, [E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
            $priority = Logger::PRIORITY_ERR;
        }

        $this->logger->log('general', $message, $priority);
    }

    /**
     * @return void
     */
    public function onShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_USER_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
            $this->handle($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}