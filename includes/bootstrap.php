<?php

declare(strict_types=1);

require __DIR__ . '/functions.php';

/**
 * Sends baseline response headers and starts the session used by the contact form.
 */
function bootstrap_request(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

bootstrap_request();
