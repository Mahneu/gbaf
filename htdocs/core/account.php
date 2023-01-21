<?php

session_start();

require_once('helper.php');


// FONCTION pour effacer les valeurs de session 
function unsetPreviousSession()
{
    unset($_SESSION['username']);
}



// FONCTION pour effacer toute session
function deleteSession()
{
    $_SESSION = array();
    session_destroy();
}



// Fonction cherche l'utilisateur
function searchUser($bdd, $userName)
{

    $req_select_info_user = $bdd->prepare('SELECT * FROM account WHERE username = ?');
    $req_select_info_user->bindValue(1, $userName);
    $req_select_info_user->execute();
    $dataAccount = $req_select_info_user->fetch();
    $req_select_info_user->closeCursor();

    return $dataAccount;
}



// message / erreur
$message = '';

// message si la personne viens de changer de mp OU viens de s'inscrire
if (!isset($_POST['connexionSubmit'])) {

    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
    }

    unset($_SESSION['message']);
}

define("USERNAME_UNKNOWN",      "Cet identifiant n'existe pas");
define("EMPTY_FIELD",           "Veuillez remplir tous les champs");
define("PASSWORD_WRONG",        "Ce n'est pas le bon mot de passe");
define("PASSWORD_INVALID",      "Le mot de passe doit contenir au moins 4 caractères, dont une minuscule, une majuscule et un chiffre");
define("ACCOUNT_UPDATE",        "Vos changements ont bien été pris en compte");
define("QUESTION",              "répondez à votre question secrète : ");
define("ANSWER_WRONG",          "Ce n'est pas la réponse attendue");
define("PASSWORD_CAN_CHANGE",   "Vous pouvez changer votre mot de passe : ");
define("PASSWORD_UPDATE",       "Votre mot de passe à bien été changé . <br> Vous pouvez vous connecter");
define("USERNAME_EXIST",        "Cet identifiant existe déjà");
define("WELCOME",               "Bienvenue ! Vous pouvez vous connecter");



// REGEX mp
$mpValid = "#(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z.-_]{4,}#";



// Fonction garde en mémoire les infos postées
function defaultInputValue($valuePost_Session, $defaultDataUser)
{

    if (isset($_POST['dataSubmit'])){

        echo htmlspecialchars($_POST[$valuePost_Session]);

    } elseif (isset($_SESSION[$valuePost_Session])) {

        echo htmlspecialchars($_SESSION[$valuePost_Session]);

    } elseif (isset($defaultDataUser)){
        
        echo htmlspecialchars($defaultDataUser);

    }
}