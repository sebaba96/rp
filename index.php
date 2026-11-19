<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$hero = site_config('hero');
$logos = marquee_logos(get_client_logos());
$marqueeSeconds = marquee_duration((int) ceil(count($logos) / 2));
$services = get_services();

render_header(
    'Početna',
    (string) ($hero['text'] ?? ''),
    'home',
    true
);
?>
<main id="sadrzaj">
    <section class="hero" aria-label="Uvod">
        <canvas
            id="hero-canvas"
            data-model="STR_kotlovnica_export/result.gltf"
            aria-label="Trodimenzionalni prikaz kotlovnice"
        ></canvas>
        <div class="hero-scrim" aria-hidden="true"></div>
        <div class="hero-copy">
            <div class="hero-copy-inner">
                <h1>
                    <span class="hero-lead"><?= e((string) ($hero['title_lead'] ?? '')) ?></span>
                    <span class="hero-mark"><?= e((string) ($hero['title_mark'] ?? '')) ?></span>
                </h1>
                <p><?= e((string) ($hero['text'] ?? '')) ?></p>
            </div>
        </div>
        <div class="hero-loader" id="hero-loader">
            <p id="hero-loader-label">Učitavanje kotlovnice</p>
            <div class="hero-loader-track" aria-hidden="true"><span id="hero-loader-bar"></span></div>
        </div>
    </section>

    <section class="facts" aria-label="U brojkama">
        <ul>
            <li>
                <span class="facts-value">31</span>
                <span class="facts-name">Stručnjak</span>
            </li>
            <li>
                <span class="facts-value">122</span>
                <span class="facts-name">Projekta</span>
            </li>
            <li>
                <span class="facts-value">40+</span>
                <span class="facts-name">Klijenata</span>
            </li>
        </ul>
    </section>

    <section class="work" aria-labelledby="work-title">
        <div class="work-head">
            <div>
                <p class="eyebrow">Djelatnosti</p>
                <h2 id="work-title">Osnovna područja rada</h2>
            </div>
            <a class="text-link" href="djelatnosti.php">Otvori djelatnosti</a>
        </div>
        <div class="card-grid">
            <?php foreach ($services as $index => $service): ?>
                <?php $slug = slug_id((string) $service['slug']); ?>
                <a class="flip-card" href="djelatnosti.php#<?= e($slug) ?>">
                    <span class="flip-inner">
                        <span class="flip-face flip-front">
                            <span class="card-index"><?= e(sprintf('%02d', $index + 1)) ?></span>
                            <span class="card-title"><?= e((string) $service['title']) ?></span>
                        </span>
                        <span class="flip-face flip-back" aria-hidden="true">
                            <img src="<?= e((string) $service['image']) ?>" alt="">
                            <span class="card-caption"><?= e((string) $service['title']) ?></span>
                        </span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($logos !== []): ?>
    <section class="clients" aria-labelledby="clients-title">
        <div class="clients-label">
            <h2 id="clients-title">Klijenti</h2>
        </div>
        <div class="marquee">
            <div class="marquee-track" style="animation-duration: <?= (int) $marqueeSeconds ?>s">
                <?php foreach ($logos as $logo): ?>
                    <div class="logo-item" <?= (int) $logo['copy'] === 2 ? 'aria-hidden="true"' : '' ?> data-copy="<?= (int) $logo['copy'] ?>">
                        <img src="<?= e($logo['src']) ?>" alt="<?= (int) $logo['copy'] === 1 ? e($logo['alt']) : '' ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php render_footer(); ?>
