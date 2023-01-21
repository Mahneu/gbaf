<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />

		<title>GBAF extranet</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="/style/style.css" />
		<link
			rel="stylesheet"
			media="screen"
			href="/style/style-mobile.css"
		/>
		<link
			rel="stylesheet"
			media="screen"
			href="/style/style-tablette.css"
		/>

	</head>

	<body>
        <div class="contain_all">
            <header>
				<!-- Logo GBAF -->
				<a class="header-logo" href="/espace-membre/accueil.php">
					<img src="/images/GBAF.png" alt="Logo GBAF" />
				</a>

                <?php // affiche le profil uniquement si un utilsateur est connecté
                if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) {
                ?>

                <!-- Profil -->
                <ul class="header-profil">
                    <!-- Nom Prenom -->
                    <li class="profil_nom">
                        <p> <?php echo htmlspecialchars($_SESSION['nom']); ?> </p>
                        <p> <?php echo htmlspecialchars($_SESSION['prenom']); ?> </p>
                    </li>

                    <!-- bouton Déconnexion -->
                    <li>
                        <a href="/espace-membre/deconnexion.php?redirection=exit">Se déconnecter</a>
                    </li>

                    <!-- bouton Modifier son Profil -->
                    <li>
                        <a href="/espace-membre/parametres-compte.php">Paramètres du compte</a>
                    </li>
                </ul>
                <?php
                }
                ?>
            </header>