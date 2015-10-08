<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\app;

use Psr\Log\AbstractLogger;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class FakeLogger extends AbstractLogger implements DebugLoggerInterface
{
    /** @var array */
    private static $logs;

    /** {@inheritdoc} */
    public function getLogs()
    {
        return static::$logs;
    }

    /** {@inheritdoc} */
    public function countErrors()
    {
        return 0;
    }

    /** {@inheritdoc} */
    public function log($level, $message, array $context = array())
    {
        static::$logs[] = array(
            'message' => $message,
        );
    }
}
