<?php

// Lire tous les fichiers de langue FR et comparer avec EN
$files = ['home', 'common', 'nav', 'auth', 'forum', 'evenements', 'actualites', 'acteurs', 'bibliotheque', 'medias'];

$missingTranslations = [];

foreach ($files as $file) {
    $frFile = __DIR__ . "/lang/fr/$file.php";
    $enFile = __DIR__ . "/lang/en/$file.php";
    
    if (!file_exists($frFile) || !file_exists($enFile)) {
        continue;
    }
    
    $fr = include $frFile;
    $en = include $enFile;
    
    $missing = array_diff_key($fr, $en);
    if ($missing) {
        $missingTranslations[$file] = $missing;
    }
}

// Si pas de traductions manquantes, afficher message
if (empty($missingTranslations)) {
    echo "✓ Toutes les traductions sont complètes !\n";
    exit(0);
}

// Afficher les traductions manquantes
echo "=== Traductions manquantes en EN ===\n\n";
foreach ($missingTranslations as $file => $translations) {
    echo "Fichier: lang/en/$file.php\n";
    foreach ($translations as $key => $value) {
        echo "  '$key' => '$value',\n";
    }
    echo "\n";
}
