<?php

require_once('core/account.php');

// SI CONNECTÉ
if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) {

    header('Location: /espace-membre/accueil.php');
    exit();
}


if (isset($_POST['dataSubmit']) && !empty($_POST['username']) && !empty($_POST['password'])) {

    // cherche user dans la base de données ( account )
    $dataAccount = searchUser($bdd, $_POST['username']);
    // Si user exite et que tous les champs sont remplis
    if ($dataAccount) {

        // Vérif mdp (crypter)
        $isPasswordCorrect = password_verify($_POST['password'], $dataAccount['password']);
        // Si bon mdp
        if ($isPasswordCorrect) {
            // Connexion
            $_SESSION['nom'] = htmlspecialchars($dataAccount['nom']);
            $_SESSION['prenom'] = htmlspecialchars($dataAccount['prenom']);
            $_SESSION['id_user'] = $dataAccount['id_user'];
            $_SESSION['username'] = htmlspecialchars($dataAccount['username']);

            header('Location: /espace-membre/accueil.php');
            exit();
        }
        //Si mauvais mdp
        if (!$isPasswordCorrect) {

            $message = PASSWORD_WRONG;
        }
    }
    // username -> faux ou existe pas 
    if (!$dataAccount) {

        $message = USERNAME_UNKNOWN;
    }
}
// champs non remplis
if (isset($_POST['dataSubmit']) && empty($_POST['username']) && empty($_POST['password'])) {

    $message = EMPTY_FIELD;
}



// SI NON CONNECTÉ -> redirection page de connexion 
if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) {


    // HEADER 

    require_once('layout/header.php');

    ?>


 <!-- HTML -->
    <main class="inscription-connexion">
                <div class="form_container">
                    <fieldset>
                        <legend>Se connecter :</legend>

                        <span class="message">
                            <?php echo $message; ?>
                        </span>

                        <form method="post" action="index.php">
                            <p>
                                <label for="pseudo">Identifiant : </label>
                                <input
                                    type="text"
                                    id="pseudo"
                                    name="username"
                                    required
                                    value="<?php /* valueInputUsername(); */
                                    defaultInputValue('username', '')
                                    
                                    ?>"
                                />

                                <label for="mp">Mot de passe : </label>
                                <input
                                    type="password"
                                    id="mp"
                                    name="password"
                                    required
                                />

                                <input
                                    class="button-envoyer"
                                    type="submit"
                                    name="dataSubmit"
                                    value="Connexion"
                                    onclick=" unsetPreviousSession()"
                                />

                                <span
                                    >Les champs indiqués par une <em>*</em> sont
                                    obligatoires</span
                                >
                            </p>
                        </form>

                        <a href="/mp.php"> Mot de passe oublié ? </a>

                        <a href="/inscription.php"> Créer un compte </a>
                    </fieldset>
                </div>
            </main>


 <!-- FOOTER -->

    <?php 

    require_once('layout/footer.php');
}
?>