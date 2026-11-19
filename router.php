<?php

declare(strict_types=1);

/**
 * Returns true when a request tries to read stored contact messages directly.
 */
function is_blocked_path(string $path): bool
{
    return str_starts_with($path, '/data/');
}

$requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (is_blocked_path($requestedPath)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Zabranjen pristup.';
    return true;
}

return false;
