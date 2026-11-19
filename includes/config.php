<?php

declare(strict_types=1);

/**
 * Site-wide content for Rijekaprojekt Energetika.
 * Pages read this through site_config() so names, copy, and registry data stay in one place.
 */
return [
    'name' => 'Rijekaprojekt Energetika',
    'legal_name' => 'RIJEKAPROJEKT - ENERGETIKA d.o.o.',
    'legal_note' => 'za projektiranje, nadzor i izvođenje',
    'address' => 'Moše Alabaharija 10a',
    'city' => '51000 Rijeka',
    'map_lat' => 45.331599,
    'map_lon' => 14.436386,
    'phone' => '051 344 262',
    'phone_href' => '+38551344262',
    'fax' => '051 344 009',
    'email' => 'info@rijekaprojekt-energetika.hr',
    'email_alt' => 'rijekaprojekt-energetika@ri.t-com.hr',
    'hours' => 'Radnim danom 8–16 h',
    'mbs' => '040051010',
    'oib' => '96541170357',
    'iban' => 'HR2324020061100393901',
    'hero' => [
        'title_lead' => 'Tradicija i',
        'title_mark' => 'Inovacija',
        'text' => 'S tradicijom duljom od 70 godina, specijalizirani smo za strojarske projekte, projektiranje toplinske i zvučne zaštite zgrada, energetske certifikate, blower door ispitivanja i nadzor nad izvođenjem strojarskih instalacija. Naša tradicija i iskustvo garantiraju kvalitetu, uštedu energije i zaštitu okoliša.',
    ],
    'nav' => [
        ['page' => 'home', 'href' => 'index.php', 'label' => 'Početna'],
        ['page' => 'about', 'href' => 'o-nama.php', 'label' => 'O nama'],
        ['page' => 'services', 'href' => 'djelatnosti.php', 'label' => 'Djelatnosti'],
        ['page' => 'contact', 'href' => 'kontakt.php', 'label' => 'Kontakt'],
    ],
    'services' => [
        [
            'slug' => 'strojarski-projekt',
            'title' => 'Strojarski projekt',
            'image' => 'assets/images/services/strojarski-projekt.jpg',
            'image_alt' => 'Kotlovnica s bojlerima, bakrenim cijevima, crpkama i razvodnim ormarom',
            'lead' => 'Pružamo cjelovite usluge projektiranja i stručnog nadzora u području instalacija grijanja, hlađenja, ventilacije i klimatizacije osiguravajući pouzdana, učinkovita i kvalitetno projektirana rješenja za svaki objekt.',
            'paragraphs' => [
                'Bavimo se projektiranjem strojarskih instalacija, s naglaskom na izradu glavnih i izvedbenih projekata usklađenih s važećim tehničkim propisima, standardima i normama struke.',
                'Naši projekti osiguravaju optimalnu funkcionalnost, energetsku učinkovitost i pouzdanost sustava, uz precizno planiranje i tehničku kvalitetu svakog rješenja.',
            ],
        ],
        [
            'slug' => 'fizika-zgrade',
            'title' => 'Fizika zgrade',
            'image' => 'assets/images/services/fizika-zgrade.jpg',
            'image_alt' => 'Presjek suvremenog pročelja s toplinskom izolacijom i tihim unutarnjim prostorom',
            'lead' => '',
            'paragraphs' => [],
            'sections' => [
                [
                    'title' => 'Projektiranje toplinske zaštite',
                    'text' => 'Projektiramo toplinsku zaštitu zgrada kako bi se smanjili gubici energije, troškove grijanja i hlađenja te osigurali stabilnu unutarnju temperaturu tijekom cijele godine.',
                ],
                [
                    'title' => 'Projektiranje zvučne zaštite',
                    'text' => 'Projektiramo rješenja koja smanjuju prodor buke izvana i između prostorija, čime se povećava privatnost i osigurava mirniji i ugodniji boravak.',
                ],
                [
                    'title' => 'Kombinirano projektiranje toplinske i zvučne zaštite',
                    'text' => 'Kombiniranim pristupom toplinskoj i zvučnoj zaštiti osiguravamo energetsku učinkovitost, akustičku udobnost i usklađenost sa zakonskim propisima.',
                ],
            ],
        ],
        [
            'slug' => 'energetsko-certificiranje',
            'title' => 'Energetsko certificiranje',
            'image' => 'assets/images/services/energetsko-certificiranje.jpg',
            'image_alt' => 'Pročelje stambene zgrade u termovizijskim bojama koje pokazuju gubitak topline',
            'lead' => 'Pružamo stručne usluge energetskog certificiranja i energetskih pregleda zgrada s jednostavnim i složenim tehničkim sustavom, analizu potrošnje energenata, savjetovanja o poboljšanju energetske učinkovitosti zgrada te pripremu i izdavanje zakonski propisane dokumentacije.',
            'paragraphs' => [],
        ],
        [
            'slug' => 'blower-door-ispitivanje',
            'title' => 'Blower door ispitivanje',
            'image' => 'assets/images/services/blower-door.jpg',
            'image_alt' => 'Ventilator za ispitivanje zrakopropusnosti ugrađen u vrata kuće',
            'lead' => 'Blower door test je precizna dijagnostička metoda kojom mjerimo zrakopropusnost zgrada. Omogućuje otkrivanje svih mjesta kroz koja zrak može neplanirano ulaziti ili izlaziti iz prostora — spojeva zidova, vrata i prozora, prodora instalacija i pukotina.',
            'paragraphs' => [
                'Ispitivanjem se provjerava kvaliteta brtvljenja zgrade, a zadovoljavanjem propisanih standarda utječe se na povećanje energetske učinkovitosti, smanjenje troškova grijanja i hlađenja te poboljšanje udobnosti unutarnjeg prostora.',
                'Ugradnja prozora, vrata i ostakljenih stijena mora biti izvedena prema RAL principima kako bi se osigurala visoka razina brtvljenja spojeva između krila i okvira te okvira i građevinskog otvora. Time se postiže minimalna razina zrakopropusnosti i dugoročno sprječava pojava površinske kondenzacije uzrokovane toplinskim mostom kao i pojava građevinskih šteta zbog unutrašnje kondenzacije.',
            ],
        ],
        [
            'slug' => 'nadzor',
            'title' => 'Nadzor',
            'image' => 'assets/images/services/nadzor.jpg',
            'image_alt' => 'Nadzor ugradnje ventilacijskih kanala i cijevi na gradilištu',
            'lead' => 'Nudimo profesionalne usluge stručnog nadzora strojarskih instalacija, s naglaskom na usklađenost izvedenih radova s glavnim i izvedbenim projektima i trenutno važećim tehničkim propisima. Kroz sustavan nadzor osiguravamo kvalitetu, funkcionalnost i pouzdanost sustava, uz pravovremeno otkrivanje i otklanjanje mogućih nepravilnosti tijekom izvođenja radova. Specijalizirani smo za planiranje, praćenje i kontrolu radova kako bismo osigurali vrhunsku kvalitetu, sigurnost i energetsku učinkovitost svakog projekta.',
            'paragraphs' => [],
        ],
        [
            'slug' => 'dimnjaci',
            'title' => 'Dimnjaci',
            'image' => 'assets/images/services/dimnjaci.jpg',
            'image_alt' => 'Nehrđajući dimnjaci na krovu zidane kotlovnice',
            'lead' => 'Izračun uzgona dimnjaka omogućuje pravilno dimenzioniranje i planiranje dimnjaka kako bi se osigurao učinkoviti izlaz dimnih plinova, čime se jamči optimalan rad i sprječava povrat dima u prostorije. Osim projektiranja novih, važna je i provjera postojećih dimnjaka radi sigurnosti, ispravnog rada sustava grijanja i zaštite zdravlja korisnika.',
            'paragraphs' => [],
        ],
    ],
];
