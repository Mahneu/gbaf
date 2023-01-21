<?php

session_start();

// REDIRECTION: NON CO
if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])
    && !isset($_GET['vote']) && !isset($_GET['id_partenaire'])) {

    header('Location: ../index.php');
    exit();
}



// CO
if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])
    && isset($_GET['vote']) && isset($_GET['id_partenaire'])) {

    require_once('../core/helper.php');


    //Cherche le vote de l'utilisateur sur la page partenaire
    $req_vote_user = $bdd->prepare('SELECT vote FROM vote WHERE id_partenaire = ? AND id_user = ?');
    $req_vote_user->execute(array($_GET['id_partenaire'], $_SESSION['id_user']));
    $userVote = $req_vote_user->fetch();
    $req_vote_user->closeCursor();

    // Si il a pas voté
    if (!$userVote) {

        // on Ajoute son vote
        $req_insert_vote = $bdd->prepare('INSERT INTO vote (id_user, id_partenaire, vote)
                                VALUES (:id_user, :id_partenaire, :vote)');
        $req_insert_vote->execute(array(
            'id_user' => ($_SESSION['id_user']),
            'id_partenaire' => ($_GET['id_partenaire']),
            'vote' => ($_GET['vote'])
        ));

        $req_insert_vote->closeCursor();

        header('Location: partenaire.php?id_partenaire=' . $_GET['id_partenaire']);
        exit();
    } 

    if ($userVote && $_GET['vote'] != $userVote['vote']) {

        // on change son vote
        $req_update_vote = $bdd->prepare('UPDATE vote SET vote = :vote WHERE id_user = :id_user AND id_partenaire = :id_partenaire');
        $req_update_vote->execute(array(
            'vote' => ($_GET['vote']),
            'id_user' => ($_SESSION['id_user']),
            'id_partenaire' => ($_GET['id_partenaire']),
        ));
        $req_update_vote->closeCursor();

        header('Location: partenaire.php?id_partenaire=' . $_GET['id_partenaire']);
        exit();
    } 

    // REDIRECTION: vote identique
    if ($userVote && $_GET['vote'] == $userVote['vote']) {

        header('Location: partenaire.php?id_partenaire=' . $_GET['id_partenaire']);
        exit();
    }
}

