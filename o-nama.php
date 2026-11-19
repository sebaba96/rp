<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

render_header(
    'O nama',
    'Ured za strojarske projekte, fiziku zgrade i energetsko certificiranje u Rijeci, s tradicijom duljom od 70 godina.',
    'about'
);
?>
<main id="sadrzaj" class="page-main">
    <header class="page-intro">
        <p class="eyebrow">O nama</p>
        <h1>Inženjerski ured koji projektira instalacije i mjeri što zgrada stvarno troši.</h1>
    </header>

    <section class="about-grid">
        <div class="about-copy">
            <p>S dugogodišnjim iskustvom, Rijekaprojekt-energetika d.o.o. razvila se u pouzdanog partnera u području strojarstva, energetske učinkovitosti i kontrole kvalitete objekata.</p>
            <p>Naše ključne djelatnosti obuhvaćaju:</p>
            <ul>
                <li>izradu strojarskih projekata,</li>
                <li>projektiranje toplinske i zvučne zaštite zgrada,</li>
                <li>stručni nadzor izvođenja strojarskih instalacija,</li>
                <li>izradu energetskih certifikata,</li>
                <li>blower door - ispitivanje zrakopropusnosti.</li>
            </ul>
            <p>Kombiniramo znanje, iskustvo i suvremene tehnologije kako bismo našim klijentima pružili učinkovita, održiva i usklađena rješenja prema važećim tehničkim i zakonskim standardima. Naš cilj je visoka kvaliteta izvedbe, energetska učinkovitost i dugotrajna vrijednost svakog projekta.</p>
        </div>
    </section>

    <ol class="timeline">
        <li>
            <span>1947.</span>
            <h2>Centralni biro za projektiranje</h2>
            <p>Osnivanje biroa i prvi veliki poslovi obnove brodogradilišta te industrije koja ih prati.</p>
        </li>
        <li>
            <span>1954.</span>
            <h2>Rijekaprojekt</h2>
            <p>Biro prerasta u zavod za visokogradnju, niskogradnju, luke i energetska postrojenja.</p>
        </li>
        <li>
            <span>1991.</span>
            <h2>Energetika d.o.o.</h2>
            <p>Registrirano je društvo usmjereno na projektiranje i nadzor strojarskih instalacija.</p>
        </li>
        <li>
            <span>Danas</span>
            <h2>Toplina, zrak i dokaz</h2>
            <p>Uz projekt i nadzor radimo fiziku zgrade, energetske certifikate, blower door ispitivanja i dimnjake.</p>
        </li>
    </ol>

    <section class="about-close">
        <h2>Što radimo u praksi</h2>
        <p>
            Grijanje, hlađenje, ventilacija, klimatizacija, plin, medicinski i tehnički plinovi, kotlovnice i industrijska postrojenja.
            Uz crtež ide proračun ovojnice i, kad je zgrada gotova, dokument koji kaže koliko energije troši.
        </p>
        <a class="button" href="djelatnosti.php">Pogledajte djelatnosti</a>
    </section>
</main>
<?php render_footer(); ?>
