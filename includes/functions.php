<?php

declare(strict_types=1);

/**
 * Returns the whole site configuration, or one top-level section when a key is given.
 */
function site_config(?string $key = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? null;
}

/**
 * Escapes a value so it can be printed safely in HTML text or attributes.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Counts characters with multibyte support when the mbstring extension exists.
 */
function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

/**
 * Remembers which navigation item belongs to the page currently being rendered.
 */
function page_key(?string $key = null): string
{
    static $current = '';

    if ($key !== null) {
        $current = $key;
    }

    return $current;
}

/**
 * Returns every service shown on the home cards and the Djelatnosti page.
 */
function get_services(): array
{
    $services = site_config('services');

    return is_array($services) ? $services : [];
}

/**
 * Finds one service by its URL slug, or null when the slug is unknown.
 */
function get_service(string $slug): ?array
{
    foreach (get_services() as $service) {
        if (($service['slug'] ?? '') === $slug) {
            return $service;
        }
    }

    return null;
}

/**
 * Keeps only characters that are safe inside an HTML id derived from a slug.
 */
function slug_id(string $slug): string
{
    $safe = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));

    return $safe !== null ? $safe : '';
}

/**
 * Reads the logo name from an SVG title, otherwise tidies the filename.
 */
function client_logo_label(string $path, string $filename): string
{
    if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'svg') {
        $svg = file_get_contents($path);

        if (is_string($svg) && preg_match('/<title>(.*?)<\/title>/s', $svg, $match) === 1) {
            return trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
    }

    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = preg_replace('/^\d+-/', '', $name) ?? $name;

    return ucfirst(trim(str_replace(['-', '_'], ' ', $name)));
}

/**
 * Returns the folder that holds the real client logo files.
 */
function client_logo_directory(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . '1_logo klijenata';
}

/**
 * Builds a URL for one logo file, encoding the folder name and the filename.
 */
function client_logo_src(string $filename): string
{
    return rawurlencode('1_logo klijenata') . '/' . rawurlencode($filename);
}

/**
 * Lists every image in "1_logo klijenata", in filename order.
 * Drop a png, jpg, webp, or svg into that folder and it joins the strip.
 */
function get_client_logos(): array
{
    $directory = client_logo_directory();

    if (!is_dir($directory)) {
        return [];
    }

    $allowed = ['svg', 'png', 'jpg', 'jpeg', 'webp'];
    $logos = [];

    foreach (scandir($directory) ?: [] as $file) {
        $path = $directory . DIRECTORY_SEPARATOR . $file;

        if (!is_file($path)) {
            continue;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            continue;
        }

        $logos[] = [
            'src' => client_logo_src($file),
            'alt' => client_logo_label($path, $file),
        ];
    }

    usort($logos, static function (array $a, array $b): int {
        return strcmp($a['src'], $b['src']);
    });

    return $logos;
}

/**
 * Repeats the logo list into two equal halves so the strip can loop without a gap.
 */
function marquee_logos(array $logos): array
{
    if ($logos === []) {
        return [];
    }

    $half = [];
    $minimum = 10;
    $sets = (int) ceil($minimum / count($logos));

    for ($set = 0; $set < $sets; $set++) {
        foreach ($logos as $logo) {
            $half[] = $logo;
        }
    }

    $track = [];

    foreach ([1, 2] as $copy) {
        foreach ($half as $logo) {
            $logo['copy'] = $copy;
            $track[] = $logo;
        }
    }

    return $track;
}

/**
 * Sets how long one full pass of the logo strip lasts.
 * A longer row gets more time so the logos do not rush past.
 */
function marquee_duration(int $logosInOneHalf): int
{
    return max(120, $logosInOneHalf * 8);
}

/**
 * Returns the contact-form token, creating one for this session when needed.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

/**
 * Checks that a submitted contact form carries the token from this session.
 */
function verify_csrf(): bool
{
    $sent = $_POST['csrf'] ?? '';
    $known = $_SESSION['csrf'] ?? '';

    return is_string($sent)
        && is_string($known)
        && $known !== ''
        && hash_equals($known, $sent);
}

/**
 * Stores a one-time message to show after a redirect.
 */
function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Reads and removes a one-time message.
 */
function take_flash(string $key): string
{
    $message = (string) ($_SESSION['flash'][$key] ?? '');
    unset($_SESSION['flash'][$key]);

    return $message;
}

/**
 * Remembers the contact fields so a failed submit can refill the form.
 */
function set_old(array $input): void
{
    $_SESSION['old'] = $input;
}

/**
 * Forgets the saved contact fields after a successful send.
 */
function clear_old(): void
{
    unset($_SESSION['old']);
}

/**
 * Returns one previously typed contact field, already escaped for HTML.
 */
function old(string $key): string
{
    $value = $_SESSION['old'][$key] ?? '';

    return e(is_string($value) ? $value : '');
}

/**
 * Checks the contact form and returns a list of messages the visitor needs to fix.
 */
function validate_contact(array $input): array
{
    $errors = [];
    $name = trim((string) ($input['ime'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $phone = trim((string) ($input['telefon'] ?? ''));
    $message = trim((string) ($input['poruka'] ?? ''));

    if ($name === '' || text_length($name) > 120) {
        $errors[] = 'Upišite ime i prezime, najviše 120 znakova.';
    }

    if ($email === '' || text_length($email) > 160) {
        $errors[] = 'Upišite adresu e-pošte.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false || preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u', $email) !== 1) {
        $errors[] = 'Upišite ispravnu adresu e-pošte.';
    }

    if ($phone !== '' && !preg_match('/^[0-9+().\/\s-]{6,40}$/', $phone)) {
        $errors[] = 'Telefon smije sadržavati znamenke, razmake i znakove + ( ) / -';
    }

    if (text_length($message) < 10 || text_length($message) > 4000) {
        $errors[] = 'Poruka treba imati između 10 i 4000 znakova.';
    }

    return $errors;
}

/**
 * Appends one contact message to the private log outside direct browsing.
 */
function save_contact_message(array $message): bool
{
    $directory = dirname(__DIR__) . '/data';

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return false;
    }

    $line = json_encode($message, JSON_UNESCAPED_UNICODE);

    if ($line === false) {
        return false;
    }

    return file_put_contents($directory . '/poruke.jsonl', $line . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

/**
 * Turns raw contact input into the plain-text record that gets stored.
 */
function contact_record(array $input): array
{
    return [
        'ime' => trim(strip_tags((string) ($input['ime'] ?? ''))),
        'email' => trim((string) ($input['email'] ?? '')),
        'telefon' => trim(strip_tags((string) ($input['telefon'] ?? ''))),
        'poruka' => trim(strip_tags((string) ($input['poruka'] ?? ''))),
        'vrijeme' => date('c'),
    ];
}

/**
 * Handles a contact POST: validates, stores the message, then redirects.
 * Returns validation messages when the form needs to be shown again.
 * A normal visit forgets any previously typed values so the form starts empty.
 */
function handle_contact_post(): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        clear_old();

        return [];
    }

    if (!verify_csrf()) {
        return ['Obrada obrasca nije uspjela. Osvježite stranicu i pokušajte ponovno.'];
    }

    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        set_flash('contact', 'Poruka je zaprimljena. Javit ćemo se u radnom vremenu.');
        header('Location: kontakt.php');
        exit;
    }

    $errors = validate_contact($_POST);

    if ($errors !== []) {
        set_old([
            'ime' => trim((string) ($_POST['ime'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'telefon' => trim((string) ($_POST['telefon'] ?? '')),
            'poruka' => trim((string) ($_POST['poruka'] ?? '')),
        ]);

        return $errors;
    }

    if (!save_contact_message(contact_record($_POST))) {
        return ['Poruku trenutačno nije moguće spremiti. Nazovite ured ili pišite izravno na e-poštu.'];
    }

    clear_old();
    unset($_SESSION['csrf']);
    set_flash('contact', 'Poruka je zaprimljena. Javit ćemo se u radnom vremenu.');
    header('Location: kontakt.php');
    exit;
}

/**
 * Prints the document head, skip link, and the site navigation.
 */
function render_header(string $title, string $description, string $page, bool $withHero = false): void
{
    page_key($page);
    $siteName = (string) site_config('name');
    $documentTitle = $page === 'home' ? $siteName : $title . ' · ' . $siteName;
    ?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($documentTitle) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&family=Jura:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= e((string) filemtime(dirname(__DIR__) . '/assets/css/style.css')) ?>">
    <?php if ($withHero): ?>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.4.0/model-viewer.min.js"></script>
    <script type="module" src="assets/js/hero.js?v=<?= e((string) filemtime(dirname(__DIR__) . '/assets/js/hero.js')) ?>"></script>
    <?php endif; ?>
</head>
<body class="page-<?= e($page) ?>">
<a class="skip-link" href="#sadrzaj">Preskoči na sadržaj</a>
<?php render_nav(); ?>
    <?php
}

/**
 * Prints the top navigation and marks the page that is open.
 */
function render_nav(): void
{
    $links = site_config('nav');
    $current = page_key();
    ?>
<header class="site-nav">
    <a class="brand" href="index.php">
        <span class="brand-mark" aria-hidden="true"></span>
        <span class="brand-text">
            <strong>Rijekaprojekt</strong>
            <small>Energetika</small>
        </span>
    </a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="glavni-izbornik">
        <span class="nav-toggle-lines" aria-hidden="true"></span>
        <span class="visually-hidden">Izbornik</span>
    </button>
    <nav id="glavni-izbornik" class="nav-links" aria-label="Glavni izbornik">
        <?php foreach ($links as $link): ?>
            <a
                href="<?= e($link['href']) ?>"
                <?= $current === $link['page'] ? 'aria-current="page"' : '' ?>
            ><?= e($link['label']) ?></a>
        <?php endforeach; ?>
    </nav>
</header>
    <?php
}

/**
 * Prints the registry block and closes the document.
 */
function render_footer(): void
{
    $name = (string) site_config('legal_name');
    $note = (string) site_config('legal_note');
    $address = (string) site_config('address');
    $city = (string) site_config('city');
    $phone = (string) site_config('phone');
    $phoneHref = (string) site_config('phone_href');
    $email = (string) site_config('email');
    ?>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <p class="footer-name"><?= e($name) ?></p>
            <p class="footer-note"><?= e($note) ?></p>
            <p class="footer-address">
                <?= e($address) ?>, <?= e($city) ?><br>
                <a href="tel:<?= e($phoneHref) ?>"><?= e($phone) ?></a>
                · <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            </p>
            <p class="footer-contact"><a href="kontakt.php#kontakt-obrazac">Pošaljite poruku</a></p>
        </div>
        <dl class="legal">
            <div>
                <dt>MBS</dt>
                <dd><?= e((string) site_config('mbs')) ?></dd>
            </div>
            <div>
                <dt>OIB</dt>
                <dd><?= e((string) site_config('oib')) ?></dd>
            </div>
            <div>
                <dt>IBAN</dt>
                <dd><?= e((string) site_config('iban')) ?></dd>
            </div>
        </dl>
    </div>
    <p class="footer-copy">© <?= e(date('Y')) ?> <?= e($name) ?></p>
</footer>
<script src="assets/js/main.js?v=<?= e((string) filemtime(dirname(__DIR__) . '/assets/js/main.js')) ?>"></script>
</body>
</html>
    <?php
}
