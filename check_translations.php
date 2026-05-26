<?php

$files = ['home', 'common', 'nav', 'auth', 'forum', 'evenements', 'actualites', 'acteurs', 'bibliotheque', 'medias'];

foreach ($files as $file) {
    $frFile = "lang/fr/$file.php";
    $enFile = "lang/en/$file.php";
    
    if (!file_exists($frFile) || !file_exists($enFile)) {
        echo "File missing: $file\n";
        continue;
    }
    
    $fr = include $frFile;
    $en = include $enFile;
    
    $missingInEn = array_diff_key($fr, $en);
    $missingInFr = array_diff_key($en, $fr);
    
    if ($missingInEn) {
        echo "\n=== Missing in EN/$file.php ===\n";
        foreach ($missingInEn as $key => $value) {
            echo "  '$key' => '$value',\n";
        }
    }
    
    if ($missingInFr) {
        echo "\n=== Missing in FR/$file.php ===\n";
        foreach ($missingInFr as $key => $value) {
            echo "  '$key' => '$value',\n";
        }
    }
}

echo "\nDone!\n";
