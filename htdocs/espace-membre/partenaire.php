<?php

session_start();

// REDIRECTION: NON CO
if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) {

    header('Location: index.php');
    exit();
}

// BDD
require_once('../core/helper.php');

$idPartenaire = htmlspecialchars($_GET['id_partenaire']);



// Cherche les infos du Partenaire
$req_data_partenaire = $bdd->prepare('SELECT * FROM partenaire WHERE id_partenaire = ?');
$req_data_partenaire->execute(array($idPartenaire));
$dataPartenaire = $req_data_partenaire->fetch();
$req_data_partenaire->closeCursor();



//  Ajoute un nouveau commentaire unique

// Cherche si l'utilisateur a déjà commenté
$req_already_comment = $bdd->prepare('SELECT * FROM post WHERE id_user = ? AND id_partenaire = ?');
$req_already_comment->execute(array($_SESSION['id_user'], $idPartenaire));
$userHasComment = $req_already_comment->fetch();
$req_already_comment->closeCursor();

// si il n'a pas déjà commenté
if (!$userHasComment) {

    $formComment = '';

    if (isset($_POST['newCommentPosted']) and !empty($_POST['post'])) {

        $req_insert_comment = $bdd->prepare('INSERT into post (id_user, id_partenaire, date_add, post)
                                VALUES (:id_user, :id_partenaire, NOW(), :post)
                                ');
        $req_insert_comment->execute(array(
            'id_user' => $_SESSION['id_user'],
            'id_partenaire' => $dataPartenaire['id_partenaire'],
            'post' => ($_POST['post'])
        ));
        $req_insert_comment->closeCursor();
    }
}

if ($userHasComment) {

    $formComment = 'Vous avez déjà commenté';
}



// Compte le nombre de commentaire sur le partenaire
$req_nbr_comments = $bdd->prepare('SELECT COUNT(*) as nbrComments FROM post WHERE id_partenaire = ?');
$req_nbr_comments->execute(array($idPartenaire));
$commentsPosted = $req_nbr_comments->fetch();
$nbrcommentsPosted = $commentsPosted['nbrComments'];
$req_nbr_comments->closeCursor();


//  Fonction Compte le nombre de 'like' et 'Dislike' sur le partenaire
function nbrLikeDislike($idPartenaire, $voteValue, $bdd)
{

    $req_nbr_like_dislike = $bdd->prepare('SELECT COUNT(vote) as `nombre` FROM `vote` WHERE id_partenaire = ? AND vote = ?');

    $req_nbr_like_dislike->bindValue(1, $idPartenaire, PDO::PARAM_INT);
    $req_nbr_like_dislike->bindValue(2, $voteValue, PDO::PARAM_STR);

    $req_nbr_like_dislike->execute();
    $likeOrDislike = $req_nbr_like_dislike->fetch();
    $req_nbr_like_dislike->closeCursor();

    if (isset($likeOrDislike['nombre'])) {
        echo $likeOrDislike['nombre'];
    }
}



// Cherche si l'user a like ou a dislike
$req_vote_user = $bdd->prepare('SELECT vote FROM vote WHERE id_partenaire = ? AND id_user = ?');
$req_vote_user->execute(array($_GET['id_partenaire'], $_SESSION['id_user']));
$userVote = $req_vote_user->fetch();
$req_vote_user->closeCursor();

if (isset($userVote['vote'])) {
    if ($userVote['vote'] == 'like') {
        $iconeVoteLike = 'icone-like-active';
        $iconeVoteDislike = 'icone-dislike';
    } elseif ($userVote['vote'] == 'dislike') {
        $iconeVoteLike = 'icone-like';
        $iconeVoteDislike = 'icone-dislike-active';
    }
}
if (!isset($userVote['vote'])) {
    $iconeVoteLike = 'icone-like';
    $iconeVoteDislike = 'icone-dislike';
}



// Fonction qui affiche tous les commentaires sur le Partenaire
function listCommentaires($bdd, $idPartenaire)
{
    $req_comment = $bdd->prepare('SELECT  p.post as comment, 
                                    DATE_FORMAT(p.date_add, "%d/%m/%Y") as commentDate,
                                    DATE_FORMAT(p.date_add, "%d/%m/%Y %T") as commentDateOrder, 
                                    a.prenom as autorName
                            FROM post p
                            INNER JOIN account a
                            ON p.id_user = a.id_user
                            WHERE p.id_partenaire = ?
                            ORDER by commentDateOrder DESC');

    $req_comment->bindValue(1, $idPartenaire, PDO::PARAM_INT);
    $req_comment->execute();

    while ($dataComment = $req_comment->fetch()) {

        echo '<li>';
        echo '<p>' . htmlspecialchars($dataComment['autorName'])  . '</p>';
        echo '<p>' . $dataComment['commentDate']  . '</p>';
        echo '<p>' . htmlspecialchars($dataComment['comment'])  . '</p>';
        echo '</li>';
    }

    $req_comment->closeCursor();
}


// CO:
if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) {

    // HEADER

    require_once('../layout/header.php');
    
    ?>

<!-- HTML -->

    <main>
        <!--  Section infos du Partenaire -->
        <section class="partenaire">
            <?php
            echo '<img src="/images/'.$dataPartenaire['logo'].'">';
            echo '<h2>' . $dataPartenaire['partenaire'] . '</h2>';
            echo '<a href="' . $dataPartenaire['site'] . '">Visiter le site</a>';
            echo '<div class="text"><p>' . $dataPartenaire['description'] . '</p></div>'
            ?>
        </section>

        <!-- Section commentaires -->
        <section class="commentaires">
            <div class="commentaires_dynamic">
                <!--  Nombre de commentaires -->
                <h4> 
                    <?php echo $nbrcommentsPosted; ?> 
                    commentaires 
                </h4>

                <!-- Ajouter un nouveau commentaire -->
                <div class="new_commentaire">
                    <label class="open_popup" for="popup_button"> 
                        Nouveau commentaire
                    </label>
                    <input type="checkbox" id="popup_button"/>

                    <!--  fenêtre pop-up du formulaire -->
                    <form 
                        class="new_commentaire_formulaire"
                        method="post"
                        action="#"
                    >
                        <div>
                            <label class="close_popup" for="popup_button"></label>
                            <label for="post">
                                Ajoutez un nouveau commentaire sur 
                                <em> <?php echo $dataPartenaire['partenaire']; ?> </em>
                                : 
                            </label>
                            <textarea id="post" name="post"><?php echo $formComment; ?></textarea>
                            <input 
                                type="submit" 
                                value="Envoyer" 
                                name="newCommentPosted" 
                            />
                        </div>
                    </form>
                </div>

                                <!-- Likes / Dislikes -->
                                <div class="commentaires_vote">
                    <!-- Ajoute un like (vote) -->
                    <a class="vote_like"
                        href="<?php echo 'vote.php?id_partenaire=' . $dataPartenaire['id_partenaire'] . '&vote=like'; ?>"
                    >
                        <!--  Nombre de like -->
                        <p>
                            <?php nbrLikeDislike($dataPartenaire['id_partenaire'], 'like', $bdd); ?>
                        </p>

                        <!--  icone like -->
                        <img 
                            src="<?php echo '/images/' . $iconeVoteLike . '.png'; ?>" 
                            alt="like"
                        />
                    </a>

                    <!-- Ajoute un dislike (vote) -->
                    <a class="vote_dislike"
                        href="<?php echo 'vote.php?id_partenaire=' . $dataPartenaire['id_partenaire'] . '&vote=dislike'; ?>"
                    >
                        <!--  icone dislike -->
                        <img 
                            src="<?php echo '/images/' . $iconeVoteDislike . '.png'; ?>" 
                            alt="dislike"
                        />

                        <!--  Nombre de dislike -->
                        <p> 
                            <?php nbrLikeDislike($dataPartenaire['id_partenaire'], 'dislike', $bdd); ?> 
                        </p>
                    </a>
                </div>
            </div>

            <!--  Liste de tous les commentaires -->
            <ul class="commentaires-list">
                <!--<li> -->
                <?php listCommentaires($bdd, $idPartenaire); ?>
            </ul>
        </section>

        <aside class="retour-accueil">
            <a href="accueil.php">Retour à la page d'accueil</a>
        </aside>
    </main>

    <?php

    //FOOTER

    require_once('../layout/footer.php');
}
?>