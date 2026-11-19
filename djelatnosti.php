<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$services = get_services();

render_header(
    'Djelatnosti',
    'Strojarski projekt, fizika zgrade, energetsko certificiranje, blower door ispitivanje, nadzor i dimnjaci.',
    'services'
);
?>
<main id="sadrzaj" class="page-main">
    <header class="page-intro">
        <p class="eyebrow">Djelatnosti</p>
        <h1>Šest područja, od prve skice do mjerenja na gotovoj zgradi.</h1>
    </header>

    <div class="service-tabs" data-tabs>
        <div class="tablist" role="tablist" aria-label="Djelatnosti">
            <?php foreach ($services as $service): ?>
                <?php $slug = slug_id((string) $service['slug']); ?>
                <button
                    class="tab"
                    type="button"
                    role="tab"
                    id="tab-<?= e($slug) ?>"
                    data-slug="<?= e($slug) ?>"
                    aria-controls="panel-<?= e($slug) ?>"
                    aria-selected="false"
                    tabindex="-1"
                ><?= e((string) $service['title']) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($services as $service): ?>
            <?php $slug = slug_id((string) $service['slug']); ?>
            <section
                class="tab-panel"
                role="tabpanel"
                id="panel-<?= e($slug) ?>"
                aria-labelledby="tab-<?= e($slug) ?>"
                hidden
            >
                <figure>
                    <img src="<?= e((string) $service['image']) ?>" alt="<?= e((string) $service['image_alt']) ?>">
                </figure>
                <div class="tab-copy">
                    <h2><?= e((string) $service['title']) ?></h2>
                    <?php if (!empty($service['sections'])): ?>
                        <?php foreach ($service['sections'] as $section): ?>
                            <h3><?= e((string) $section['title']) ?></h3>
                            <p><?= e((string) $section['text']) ?></p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="tab-lead"><?= e((string) $service['lead']) ?></p>
                        <?php foreach ($service['paragraphs'] as $paragraph): ?>
                            <p><?= e((string) $paragraph) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <a class="button" href="kontakt.php">Zatražite razgovor</a>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</main>
<?php render_footer(); ?>
