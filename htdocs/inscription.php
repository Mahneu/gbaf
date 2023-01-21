<?php

require_once('core/account.php');

// REDIRECTION: CO
if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) {

    header('Location: /espace-membre/accueil.php');
    exit();
}


// Si on envoi le formulaire
if (isset($_POST['dataSubmit'])) {

    // Cherche de l'utilisateur dans la BDD (account)
    $dataAccount = searchUser($bdd, $_POST['username']);

    if (!$dataAccount 
        && !empty($_POST['nom']) && !empty($_POST['prenom'])
        && !empty($_POST['username']) && !empty($_POST['password'])
        && !empty($_POST['question']) && !empty($_POST['reponse']))  {
    
        // Verification mdp contient minimum 1 lettre 1 maj et 1 chiffre
        if (preg_match($mpValid, $_POST['password'])) {

            // Hashage du mdp
            $passwordHashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $reponseHashed = password_hash($_POST['reponse'], PASSWORD_DEFAULT);

            // Ajout du nouvel user dans la BDD
            $req_add_user = $bdd->prepare('INSERT INTO account (nom, prenom, username, password, question, reponse) 
                                    VALUES (:nom, :prenom, :username, :password, :question, :reponse)');
            $req_add_user->execute(array(
                'nom' => ($_POST['nom']),
                'prenom' => ($_POST['prenom']),
                'username' => ($_POST['username']),
                'password' => $passwordHashed,
                'question' => ($_POST['question']),
                'reponse' => $reponseHashed
            ));

            $req_add_user->closeCursor();

            $message = WELCOME;

            $_SESSION['username'] = htmlspecialchars($_POST['username']);
            $_SESSION['message'] = $message;

            header('Location: /index.php');
            exit();
        } 
        // message : mauvais mdp
        if (!preg_match($mpValid, $_POST['password'])) {

            $message = PASSWORD_INVALID;
        }
    }

    // message : un des champs est vide
    if (empty($_POST['nom']) OR empty($_POST['prenom'])
        OR empty($_POST['username']) OR empty($_POST['password'])
        OR empty($_POST['question']) OR empty($_POST['reponse'])
    ) {

        $message = EMPTY_FIELD;
    }

    // message : l'username existe déja
    if ($dataAccount) {

        $message = USERNAME_EXIST;
    }
}

// NON CO- page inscription
if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) {

    // HEADER

    require_once('layout/header.php');

    ?>

<!-- HTML -->

    <main class="inscription-connexion">
        <div class="form_container">
            <fieldset>
                <legend>Créer un compte :</legend>

                <!-- message erreur -->
                <span class="message-erreur">
                    <?php echo $message; ?>
                </span>

                <!-- Formulaire avec 'value' préenregistrées -->
                <form method="post" action="inscription.php">
                    <p>
                        <label for="pseudo">Identifiant : </label>
                        <input
                            type="text"
                            id="pseudo"
                            name="username"
                            size="20"
                            value="<?php defaultInputValue('username', '');?>"
                        />

                        <label for="mp">Mot de passe : </label>
                        <input
                            type="password"
                            id="mp"
                            name="password"
                            size="20"
                        />

                        <label for="nom">Nom : </label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            size="30"
                            value="<?php defaultInputValue('nom', '');?>"
                        />

                        <label for="prenom">Prénom : </label>
                        <input 
                            type="text" 
                            id="prenom" 
                            name="prenom" 
                            size="30"
                            value="<?php defaultInputValue('prenom', '');?>"
                        />

                        <label for="question">
                            Votre question secrète : 
                        </label>
                        <input 
                            type="text" 
                            id="question" 
                            name="question"
                            value="<?php defaultInputValue('question', '');?>"
                        />

                        <label for="reponse">
                            La réponse à votre question : 
                        </label>
                        <input 
                            type="text" 
                            id="reponse" 
                            name="reponse"
                            value="<?php defaultInputValue('reponse', '');?>"
                        />

                        <input
                            class="button-envoyer"
                            type="submit"
                            name="dataSubmit"
                            value="Envoyer"
                        />

                        <span>
                            Les champs indiqués par une * sont obligatoires
                        </span>

                    </p>
                </form>

                <a href="/index.php"> se connecter </a>

                <a href="/mp.php"> mot de passe oublié ? </a>
            </fieldset>
        </div>
    </main>

    <?php 

    // FOOTER

    require_once('layout/footer.php');
}
?>