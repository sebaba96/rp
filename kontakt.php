<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$errors = handle_contact_post();
$flash = take_flash('contact');
$mapLat = sprintf('%.6f', (float) site_config('map_lat'));
$mapLon = sprintf('%.6f', (float) site_config('map_lon'));
$mapBbox = implode(',', [
    sprintf('%.6f', (float) $mapLon - 0.006),
    sprintf('%.6f', (float) $mapLat - 0.004),
    sprintf('%.6f', (float) $mapLon + 0.006),
    sprintf('%.6f', (float) $mapLat + 0.004),
]);
$mapEmbed = 'https://www.openstreetmap.org/export/embed.html?bbox='
    . rawurlencode($mapBbox)
    . '&layer=mapnik&marker='
    . rawurlencode($mapLat . ',' . $mapLon);
$mapLink = 'https://www.openstreetmap.org/?mlat=' . rawurlencode($mapLat)
    . '&mlon=' . rawurlencode($mapLon)
    . '#map=17/' . $mapLat . '/' . $mapLon;

render_header(
    'Kontakt',
    'RIJEKAPROJEKT - ENERGETIKA d.o.o., Moše Alabaharija 10a, 51000 Rijeka. Telefon 051 344 262.',
    'contact'
);
?>
<main id="sadrzaj" class="page-main">
    <header class="page-intro">
        <p class="eyebrow">Kontakt</p>
        <h1>Pišite za projekt, certifikat, ispitivanje ili nadzor.</h1>
    </header>

    <div class="contact-layout">
        <div class="contact-form-wrap">
            <?php if ($flash !== ''): ?>
                <p class="form-success" role="status"><?= e($flash) ?></p>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <div class="form-errors" role="alert">
                    <p>Provjerite obrazac.</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="kontakt-obrazac" class="contact-form" method="post" action="kontakt.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div class="hp" aria-hidden="true">
                    <label for="website">Web stranica</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                <label for="ime">Ime i prezime</label>
                <input id="ime" name="ime" type="text" required maxlength="120" value="<?= old('ime') ?>" autocomplete="name">

                <label for="email">E-pošta</label>
                <input id="email" name="email" type="email" required maxlength="160" value="<?= old('email') ?>" autocomplete="email" inputmode="email" aria-describedby="email-error">
                <p id="email-error" class="field-error" hidden></p>

                <label for="telefon">Telefon <span>(nije obavezno)</span></label>
                <input id="telefon" name="telefon" type="tel" maxlength="40" value="<?= old('telefon') ?>" autocomplete="tel">

                <label for="poruka">Poruka</label>
                <textarea id="poruka" name="poruka" required rows="7" maxlength="4000"><?= old('poruka') ?></textarea>

                <button class="button" type="submit">Pošalji poruku</button>
            </form>
        </div>

        <aside class="contact-aside">
            <p class="contact-name"><?= e((string) site_config('legal_name')) ?></p>
            <p><?= e((string) site_config('legal_note')) ?></p>
            <address>
                <?= e((string) site_config('address')) ?><br>
                <?= e((string) site_config('city')) ?>
            </address>
            <ul class="contact-lines">
                <li><a href="tel:<?= e((string) site_config('phone_href')) ?>"><?= e((string) site_config('phone')) ?></a></li>
                <li>Faks <?= e((string) site_config('fax')) ?></li>
                <li><a href="mailto:<?= e((string) site_config('email')) ?>"><?= e((string) site_config('email')) ?></a></li>
                <li><a href="mailto:<?= e((string) site_config('email_alt')) ?>"><?= e((string) site_config('email_alt')) ?></a></li>
                <li><?= e((string) site_config('hours')) ?></li>
            </ul>
            <dl class="legal legal-compact">
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
            <iframe
                class="office-map"
                title="Karta ureda, Moše Alabaharija 10a, Rijeka"
                src="<?= e($mapEmbed) ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
            <a class="text-link" href="<?= e($mapLink) ?>" rel="noopener noreferrer">Otvori adresu na karti</a>
        </aside>
    </div>
</main>
<?php render_footer(); ?>
