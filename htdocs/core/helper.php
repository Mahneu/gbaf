<?php


//  connexion bdd : 
try {
    $bdd = new PDO(
        'mysql:host=localhost;
        dbname=gbaf_extranet;
        charset=utf8',
        'root',
        'root',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

